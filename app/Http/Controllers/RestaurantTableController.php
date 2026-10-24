<?php

namespace App\Http\Controllers;

use App\Helpers\BranchHelper;
use App\Models\Branch;
use App\Models\RestaurantTable;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RestaurantTableController extends Controller
{
    /**
     * Display a listing of restaurant tables.
     */
    public function index(Request $request)
    {
        $activeBranchId = BranchHelper::getActiveBranchId();
        $activeBranch = BranchHelper::getActiveBranch();

        // Branches for dropdown (if admin / all branches mode)
        $branches = Branch::where('status', 1)->get();

        // Query tables
        $tablesQuery = RestaurantTable::query()->with('branch');

        if ($activeBranchId) {
            $tablesQuery->where('branch_id', $activeBranchId);
        } elseif ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $tablesQuery->where('branch_id', $request->branch_id);
        }

        // Floor filter
        if ($request->filled('floor_area') && $request->floor_area !== 'all') {
            $tablesQuery->where('floor_area', $request->floor_area);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $tablesQuery->where('table_no', 'LIKE', "%{$search}%");
        }

        $tables = $tablesQuery->orderBy('floor_area')
            ->orderBy('sort_order')
            ->orderBy('table_no')
            ->get();

        // Fetch all current running orders to show live occupancy
        $runningOrders = Sale::where('order_status', 'running')
            ->where('sale_status', 'booked')
            ->when($activeBranchId, function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->get()
            ->keyBy('table_no');

        // Attach live occupancy details
        foreach ($tables as $t) {
            $order = $runningOrders->get($t->table_no);
            $t->is_occupied = !empty($order);
            $t->active_order = $order;
        }

        // Get distinct floor areas for filter tabs
        $floorsQuery = RestaurantTable::query();
        if ($activeBranchId) {
            $floorsQuery->where('branch_id', $activeBranchId);
        }
        $floors = $floorsQuery->distinct()->pluck('floor_area')->filter()->values();

        // If no tables exist for active restaurant branch, provide option to initialize defaults
        $totalTablesCount = RestaurantTable::when($activeBranchId, function($q) use ($activeBranchId) {
            $q->where('branch_id', $activeBranchId);
        })->count();

        return view('admin_panel.restaurant.tables.index', compact(
            'tables',
            'branches',
            'activeBranch',
            'activeBranchId',
            'floors',
            'totalTablesCount'
        ));
    }

    /**
     * Store a newly created or updated table.
     */
    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
        $activeBranchId = BranchHelper::getActiveBranchId();

        $branchId = $request->filled('branch_id') ? $request->branch_id : ($activeBranchId ?: 1);

        $validator = Validator::make($request->all(), [
            'table_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('restaurant_tables', 'table_no')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId);
                    })
                    ->ignore($editId),
            ],
            'floor_area' => 'nullable|string|max:100',
            'capacity'   => 'required|integer|min:1|max:100',
            'sort_order' => 'nullable|integer',
            'notes'      => 'nullable|string|max:255',
            'is_active'  => 'nullable|boolean',
        ], [
            'table_no.unique' => "Table '{$request->table_no}' already exists in this branch.",
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!empty($editId)) {
            $table = RestaurantTable::findOrFail($editId);
            $msg = 'Table updated successfully.';
        } else {
            $table = new RestaurantTable();
            $table->branch_id = $branchId;
            $msg = 'Table created successfully.';
        }

        $table->table_no   = trim($request->table_no);
        $table->floor_area = !empty($request->floor_area) ? trim($request->floor_area) : 'Main Hall';
        $table->capacity   = (int) $request->capacity;
        $table->sort_order = (int) ($request->sort_order ?? 0);
        $table->notes      = $request->notes;
        $table->is_active  = $request->has('is_active') ? (bool) $request->is_active : true;

        if ($request->filled('branch_id')) {
            $table->branch_id = $request->branch_id;
        }

        $table->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => $msg,
                'table'   => $table,
                'reload'  => true,
            ]);
        }

        return redirect()->route('restaurant.tables.index')->with('success', $msg);
    }

    /**
     * Batch generate tables (e.g. Prefix T-, Start 1, Count 10).
     */
    public function batchCreate(Request $request)
    {
        $activeBranchId = BranchHelper::getActiveBranchId();
        $branchId = $request->filled('branch_id') ? $request->branch_id : ($activeBranchId ?: 1);

        $validator = Validator::make($request->all(), [
            'prefix'     => 'nullable|string|max:20',
            'start_num'  => 'required|integer|min:1|max:999',
            'count'      => 'required|integer|min:1|max:50',
            'floor_area' => 'nullable|string|max:100',
            'capacity'   => 'required|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $prefix = $request->filled('prefix') ? trim($request->prefix) : 'T-';
        $startNum = (int) $request->start_num;
        $count = (int) $request->count;
        $floorArea = !empty($request->floor_area) ? trim($request->floor_area) : 'Main Hall';
        $capacity = (int) $request->capacity;

        $created = 0;
        $skipped = 0;

        for ($i = 0; $i < $count; $i++) {
            $num = $startNum + $i;
            $tableNo = $prefix . $num;

            // Check if already exists for this branch
            $exists = RestaurantTable::where('branch_id', $branchId)
                ->where('table_no', $tableNo)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            RestaurantTable::create([
                'branch_id'  => $branchId,
                'table_no'   => $tableNo,
                'floor_area' => $floorArea,
                'capacity'   => $capacity,
                'status'     => 'available',
                'is_active'  => true,
                'sort_order' => $num,
            ]);
            $created++;
        }

        $msg = "Successfully generated {$created} tables.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} tables already existed and were skipped)";
        }

        return response()->json([
            'status'  => 'success',
            'message' => $msg,
            'reload'  => true,
        ]);
    }

    /**
     * Delete a table.
     */
    public function destroy($id)
    {
        $table = RestaurantTable::findOrFail($id);

        // Check if there is an active running order on this table
        $hasActiveOrder = Sale::where('table_no', $table->table_no)
            ->where('order_status', 'running')
            ->where('sale_status', 'booked')
            ->when($table->branch_id, function ($q) use ($table) {
                $q->where('branch_id', $table->branch_id);
            })
            ->first();

        if ($hasActiveOrder) {
            $msg = "Cannot delete Table '{$table->table_no}' because Order #{$hasActiveOrder->invoice_no} is currently active on it. Please settle or cancel the order first.";
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 400);
            }
            return redirect()->route('restaurant.tables.index')->with('error', $msg);
        }

        $tableNo = $table->table_no;
        $table->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Table '{$tableNo}' deleted successfully.",
                'reload'  => true,
            ]);
        }

        return redirect()->route('restaurant.tables.index')->with('success', "Table '{$tableNo}' deleted successfully.");
    }

    /**
     * Toggle table active/inactive status.
     */
    public function toggleStatus($id)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->is_active = !$table->is_active;
        $table->save();

        return response()->json([
            'status'    => 'success',
            'message'   => "Table '{$table->table_no}' status updated.",
            'is_active' => $table->is_active,
        ]);
    }

    /**
     * Return JSON list of tables for Restaurant POS with live status.
     */
    public function apiList(Request $request)
    {
        $activeBranchId = BranchHelper::getActiveBranchId();

        $tables = RestaurantTable::where('is_active', true)
            ->when($activeBranchId, function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->orderBy('floor_area')
            ->orderBy('sort_order')
            ->orderBy('table_no')
            ->get();

        // Fetch running orders for live occupied state
        $runningOrders = Sale::where('order_status', 'running')
            ->where('sale_status', 'booked')
            ->when($activeBranchId, function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->get()
            ->keyBy('table_no');

        $data = $tables->map(function ($t) use ($runningOrders) {
            $order = $runningOrders->get($t->table_no);
            return [
                'id'          => $t->id,
                'table_no'    => $t->table_no,
                'floor_area'  => $t->floor_area ?: 'Main Hall',
                'capacity'    => $t->capacity,
                'is_occupied' => !empty($order),
                'order_info'  => $order ? [
                    'id'          => $order->id,
                    'invoice_no'  => $order->invoice_no,
                    'total'       => (float) $order->total_bill_amount,
                    'items_count' => $order->items()->count(),
                    'time'        => $order->created_at ? $order->created_at->format('h:i A') : '',
                ] : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'tables' => $data,
        ]);
    }
}
