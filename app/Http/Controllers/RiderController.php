<?php

namespace App\Http\Controllers;

use App\Helpers\BranchHelper;
use App\Models\Branch;
use App\Models\Rider;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RiderController extends Controller
{
    /**
     * Display a listing of riders.
     */
    public function index(Request $request)
    {
        $activeBranchId = BranchHelper::getActiveBranchId();
        $activeBranch = BranchHelper::getActiveBranch();
        $branches = Branch::where('status', 1)->get();

        $query = Rider::query()->with('branch');

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        } elseif ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'LIKE', "%{$s}%")
                  ->orWhere('phone', 'LIKE', "%{$s}%")
                  ->orWhere('vehicle_no', 'LIKE', "%{$s}%")
                  ->orWhere('cnic', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('duty_status') && $request->duty_status !== 'all') {
            $query->where('duty_status', $request->duty_status);
        }

        $riders = $query->orderBy('name')->get();

        // Calculate metrics for each rider
        foreach ($riders as $r) {
            $r->active_deliveries_count = Sale::where('rider_id', $r->id)
                ->where('delivery_status', 'dispatched')
                ->count();

            $r->today_completed_count = Sale::where('rider_id', $r->id)
                ->where('delivery_status', 'delivered')
                ->whereDate('updated_at', today())
                ->count();

            $r->unsettled_cash = (float) Sale::where('rider_id', $r->id)
                ->where('delivery_status', 'delivered')
                ->where('rider_cash_settled', false)
                ->sum('total_bill_amount');
        }

        // Summary metrics
        $totalRiders = $riders->count();
        $availableRiders = $riders->where('duty_status', 'available')->where('is_active', true)->count();
        $onDeliveryRiders = $riders->where('duty_status', 'on_delivery')->count();
        $totalCashInHand = $riders->sum('unsettled_cash');

        return view('admin_panel.restaurant.riders.index', compact(
            'riders',
            'branches',
            'activeBranch',
            'activeBranchId',
            'totalRiders',
            'availableRiders',
            'onDeliveryRiders',
            'totalCashInHand'
        ));
    }

    /**
     * Store a newly created or updated rider.
     */
    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
        $activeBranchId = BranchHelper::getActiveBranchId();
        $branchId = $request->filled('branch_id') ? $request->branch_id : ($activeBranchId ?: 1);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100',
            'phone'       => 'required|string|max:30',
            'cnic'        => 'nullable|string|max:30',
            'vehicle_no'  => 'nullable|string|max:50',
            'duty_status' => 'nullable|in:available,on_delivery,off_duty',
            'is_active'   => 'nullable|boolean',
            'address'     => 'nullable|string|max:255',
            'notes'       => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!empty($editId)) {
            $rider = Rider::findOrFail($editId);
            $msg = 'Rider updated successfully.';
        } else {
            $rider = new Rider();
            $rider->branch_id = $branchId;
            $msg = 'Rider created successfully.';
        }

        $rider->name        = trim($request->name);
        $rider->phone       = trim($request->phone);
        $rider->cnic        = $request->cnic ? trim($request->cnic) : null;
        $rider->vehicle_no  = $request->vehicle_no ? trim($request->vehicle_no) : null;
        $rider->duty_status = $request->duty_status ?? 'available';
        $rider->is_active   = $request->has('is_active') ? (bool) $request->is_active : true;
        $rider->address     = $request->address;
        $rider->notes       = $request->notes;

        if ($request->filled('branch_id')) {
            $rider->branch_id = $request->branch_id;
        }

        $rider->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => $msg,
                'rider'   => $rider,
                'reload'  => true,
            ]);
        }

        return redirect()->route('restaurant.riders.index')->with('success', $msg);
    }

    /**
     * Delete a rider.
     */
    public function destroy($id)
    {
        $rider = Rider::findOrFail($id);

        $hasActiveDeliveries = Sale::where('rider_id', $rider->id)
            ->where('delivery_status', 'dispatched')
            ->exists();

        if ($hasActiveDeliveries) {
            $msg = "Cannot delete Rider '{$rider->name}' because they currently have orders out for delivery. Please mark deliveries as completed or reassign them first.";
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 400);
            }
            return redirect()->route('restaurant.riders.index')->with('error', $msg);
        }

        $name = $rider->name;
        $rider->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Rider '{$name}' deleted successfully.",
                'reload'  => true,
            ]);
        }

        return redirect()->route('restaurant.riders.index')->with('success', "Rider '{$name}' deleted successfully.");
    }

    /**
     * Toggle rider active status or duty status.
     */
    public function toggleStatus($id, Request $request)
    {
        $rider = Rider::findOrFail($id);

        if ($request->filled('duty_status')) {
            $rider->duty_status = $request->duty_status;
        } else {
            $rider->is_active = !$rider->is_active;
        }

        $rider->save();

        return response()->json([
            'status'      => 'success',
            'message'     => "Rider '{$rider->name}' status updated.",
            'is_active'   => $rider->is_active,
            'duty_status' => $rider->duty_status,
        ]);
    }

    /**
     * Settle / Clear all COD cash in rider's hand.
     */
    public function settleCash($id, Request $request)
    {
        $rider = Rider::findOrFail($id);

        $unsettledSales = Sale::where('rider_id', $rider->id)
            ->where('delivery_status', 'delivered')
            ->where('rider_cash_settled', false)
            ->get();

        $count = $unsettledSales->count();
        $totalAmount = $unsettledSales->sum('total_bill_amount');

        if ($count === 0) {
            return response()->json([
                'status'  => 'info',
                'message' => "No unsettled cash for Rider '{$rider->name}'.",
            ]);
        }

        Sale::where('rider_id', $rider->id)
            ->where('delivery_status', 'delivered')
            ->where('rider_cash_settled', false)
            ->update([
                'rider_cash_settled' => true,
            ]);

        return response()->json([
            'status'        => 'success',
            'message'       => "Successfully settled Rs. " . number_format($totalAmount, 0) . " from {$count} delivery orders for {$rider->name}.",
            'settled_amount' => $totalAmount,
            'reload'        => true,
        ]);
    }

    /**
     * Return JSON list of active riders for POS selection.
     */
    public function apiList(Request $request)
    {
        $activeBranchId = BranchHelper::getActiveBranchId();

        $riders = Rider::where('is_active', true)
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'riders' => $riders,
        ]);
    }
}
