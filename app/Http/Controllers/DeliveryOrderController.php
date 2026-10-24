<?php

namespace App\Http\Controllers;

use App\Helpers\BranchHelper;
use App\Models\Branch;
use App\Models\Rider;
use App\Models\Sale;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    /**
     * Display listing of delivery orders.
     */
    public function index(Request $request)
    {
        $activeBranchId = BranchHelper::getActiveBranchId();
        $activeBranch = BranchHelper::getActiveBranch();
        $branches = Branch::where('status', 1)->get();

        $query = Sale::where('order_type', 'delivery')
            ->with(['items.product', 'rider', 'customer_relation']);

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        } elseif ($request->filled('branch_id') && $request->branch_id !== 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        // Status tab filter
        $currentStatus = $request->get('status', 'all');
        if ($currentStatus !== 'all' && in_array($currentStatus, ['pending', 'dispatched', 'delivered', 'cancelled'])) {
            $query->where('delivery_status', $currentStatus);
        }

        // Search filter (Invoice, Customer name, Phone, Address, Rider)
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('invoice_no', 'LIKE', "%{$s}%")
                  ->orWhere('walkin_name', 'LIKE', "%{$s}%")
                  ->orWhere('rider_name', 'LIKE', "%{$s}%")
                  ->orWhere('delivery_address', 'LIKE', "%{$s}%")
                  ->orWhere('rider_phone', 'LIKE', "%{$s}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(25);

        // Fetch active riders for this branch
        $riders = Rider::where('is_active', true)
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->orderBy('name')
            ->get();

        // Metrics
        $baseQuery = Sale::where('order_type', 'delivery')
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            });

        $pendingCount = (clone $baseQuery)->where('delivery_status', 'pending')->count();
        $dispatchedCount = (clone $baseQuery)->where('delivery_status', 'dispatched')->count();
        $deliveredTodayCount = (clone $baseQuery)->where('delivery_status', 'delivered')->whereDate('updated_at', today())->count();
        $deliveredTodayRevenue = (clone $baseQuery)->where('delivery_status', 'delivered')->whereDate('updated_at', today())->sum('total_bill_amount');

        return view('admin_panel.restaurant.deliveries.index', compact(
            'orders',
            'riders',
            'activeBranch',
            'activeBranchId',
            'branches',
            'currentStatus',
            'pendingCount',
            'dispatchedCount',
            'deliveredTodayCount',
            'deliveredTodayRevenue'
        ));
    }

    /**
     * Assign a rider and dispatch order.
     */
    public function assignRider(Request $request, $orderId)
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id',
        ]);

        $order = Sale::findOrFail($orderId);
        $rider = Rider::findOrFail($request->rider_id);

        $order->rider_id        = $rider->id;
        $order->rider_name      = $rider->name;
        $order->rider_phone     = $rider->phone;
        $order->delivery_status = 'dispatched';
        $order->dispatched_at   = now();
        $order->save();

        // Update rider duty status
        $rider->duty_status = 'on_delivery';
        $rider->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Order #{$order->invoice_no} assigned to {$rider->name} and dispatched.",
                'reload'  => true,
            ]);
        }

        return redirect()->back()->with('success', "Order #{$order->invoice_no} assigned to {$rider->name}.");
    }

    /**
     * Mark an order as Delivered.
     */
    public function markDelivered(Request $request, $orderId)
    {
        $order = Sale::findOrFail($orderId);
        $order->delivery_status = 'delivered';
        $order->delivered_at   = now();
        $order->order_status   = 'completed';
        $order->save();

        // Check if rider has any more active deliveries
        if ($order->rider_id) {
            $activeCount = Sale::where('rider_id', $order->rider_id)
                ->where('delivery_status', 'dispatched')
                ->count();

            if ($activeCount === 0) {
                Rider::where('id', $order->rider_id)->update(['duty_status' => 'available']);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Order #{$order->invoice_no} marked as Delivered.",
                'reload'  => true,
            ]);
        }

        return redirect()->back()->with('success', "Order #{$order->invoice_no} marked as Delivered.");
    }

    /**
     * Cancel / Return delivery order.
     */
    public function cancelDelivery(Request $request, $orderId)
    {
        $order = Sale::findOrFail($orderId);
        $order->delivery_status = 'cancelled';
        $order->order_status   = 'cancelled';
        if ($request->filled('reason')) {
            $order->order_notes = ($order->order_notes ? $order->order_notes . ' | ' : '') . 'Cancelled: ' . $request->reason;
        }
        $order->save();

        // Update rider if needed
        if ($order->rider_id) {
            $activeCount = Sale::where('rider_id', $order->rider_id)
                ->where('delivery_status', 'dispatched')
                ->count();
            if ($activeCount === 0) {
                Rider::where('id', $order->rider_id)->update(['duty_status' => 'available']);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Order #{$order->invoice_no} delivery cancelled.",
                'reload'  => true,
            ]);
        }

        return redirect()->back()->with('success', "Order #{$order->invoice_no} delivery cancelled.");
    }

    /**
     * Print Delivery Challan / Slip.
     */
    public function deliverySlip($id)
    {
        $sale = Sale::with(['items.product', 'rider', 'customer_relation'])->findOrFail($id);
        $branch = Branch::find($sale->branch_id);

        return view('admin_panel.sale.delivery_slip', compact('sale', 'branch'));
    }
}
