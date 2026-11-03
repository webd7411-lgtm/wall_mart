<?php

namespace App\Http\Controllers;

use App\Helpers\BranchHelper;
use App\Models\Branch;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\Rider;
use App\Models\Sale;
use App\Services\RestaurantOnlineOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantOnlineOrderController extends Controller
{
    /**
     * Display the Restaurant Online Orders Management Page
     */
    public function index(Request $request)
    {
        // 1. Fetch Restaurant Branch info
        $branch = Branch::where('type', 'restaurant')->orWhere('id', 2)->first() ?? Branch::find(2);

        $query = EcommerceOrder::with(['items.product', 'rider', 'sale'])->orderBy('id', 'desc');

        // Status filter
        $activeStatus = $request->get('status', 'all');
        if ($activeStatus !== 'all' && !empty($activeStatus)) {
            $query->where('order_status', $activeStatus);
        }

        // Search filter (Order #, Name, Phone, Address, Rider)
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                  ->orWhere('shipping_name', 'like', "%{$term}%")
                  ->orWhere('shipping_phone', 'like', "%{$term}%")
                  ->orWhere('shipping_address', 'like', "%{$term}%")
                  ->orWhere('rider_name', 'like', "%{$term}%");
            });
        }

        // Date filter
        $dateFilter = $request->get('date_filter', 'all');
        if ($dateFilter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($dateFilter === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($dateFilter === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->filled('custom_date')) {
            $query->whereDate('created_at', $request->custom_date);
        }

        $orders = $query->paginate(20)->withQueryString();

        // 2. Fetch Active Riders for Branch 2 (or global)
        $riders = Rider::where('is_active', true)
            ->where(function ($q) {
                $q->where('branch_id', 2)->orWhereNull('branch_id');
            })
            ->orderBy('name')
            ->get();

        // 3. Summary Counters
        $todayQuery = EcommerceOrder::whereDate('created_at', today());
        $allOrdersCount = EcommerceOrder::count();
        $pendingCount = EcommerceOrder::where('order_status', 'pending')->count();
        $processingCount = EcommerceOrder::where('order_status', 'processing')->count();
        $shippedCount = EcommerceOrder::where('order_status', 'shipped')->count();
        $deliveredCount = EcommerceOrder::where('order_status', 'delivered')->count();
        $cancelledCount = EcommerceOrder::where('order_status', 'cancelled')->count();
        
        $todayRevenue = EcommerceOrder::whereDate('created_at', today())
            ->where('order_status', '!=', 'cancelled')
            ->sum('total');

        $latestOrderId = EcommerceOrder::max('id') ?? 0;

        return view('admin_panel.restaurant.online_orders', compact(
            'orders',
            'riders',
            'branch',
            'activeStatus',
            'dateFilter',
            'allOrdersCount',
            'pendingCount',
            'processingCount',
            'shippedCount',
            'deliveredCount',
            'cancelledCount',
            'todayRevenue',
            'latestOrderId'
        ));
    }

    /**
     * AJAX: Assign Rider to Order and Dispatch
     */
    public function assignRider(Request $request, $id)
    {
        $request->validate([
            'rider_id' => 'nullable',
            'new_rider_name' => 'nullable|string|max:100',
            'new_rider_phone' => 'nullable|string|max:30',
        ]);

        $order = EcommerceOrder::findOrFail($id);

        $rider = RestaurantOnlineOrderService::assignRider(
            $order,
            $request->rider_id,
            $request->new_rider_name,
            $request->new_rider_phone
        );

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} assigned to Rider {$rider->name} and dispatched!",
            'rider' => $rider
        ]);
    }

    /**
     * AJAX: Update Order Status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = EcommerceOrder::findOrFail($id);
        $order->order_status = $request->status;

        // Auto update payment if marked delivered
        if ($request->status === 'delivered') {
            $order->payment_status = 'paid';
            $order->paid_amount = $order->total;
        }

        $order->save();

        // Synchronize with Central Sales, Inventory, Accounting and Riders
        RestaurantOnlineOrderService::syncOrderToSale($order);

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} status updated to " . strtoupper($request->status),
            'new_status' => $order->order_status,
            'payment_status' => $order->payment_status,
        ]);
    }

    /**
     * Print Delivery Challan / Slip (80mm Thermal Receipt)
     */
    public function deliverySlip($id)
    {
        $order = EcommerceOrder::with(['items', 'rider', 'sale'])->findOrFail($id);
        $branch = Branch::where('type', 'restaurant')->orWhere('id', 2)->first() ?? Branch::find(2);

        $branchSettings = DB::table('settings')->where('branch_id', $branch->id)->pluck('value', 'key');
        $phone = $branchSettings['company_phone'] ?? ($branch->number ?? '0300-1234567');
        $address = $branchSettings['company_address'] ?? ($branch->address ?? 'Main Commercial Boulevard, Food Street');
        $companyName = $branchSettings['company_name'] ?? ($branch->name ?? 'MUNCH FRIED CHICKEN');

        return view('admin_panel.restaurant.online_order_slip', compact('order', 'branch', 'companyName', 'phone', 'address'));
    }

    /**
     * Print Kitchen Order Ticket (KOT)
     */
    public function kotSlip($id)
    {
        $order = EcommerceOrder::with(['items', 'rider', 'sale'])->findOrFail($id);
        $branch = Branch::where('type', 'restaurant')->orWhere('id', 2)->first() ?? Branch::find(2);

        return view('admin_panel.restaurant.online_order_kot', compact('order', 'branch'));
    }

    /**
     * Live Polling API: Return new orders count & stats for live audio notification
     */
    public function getStatsJson()
    {
        $pendingCount = EcommerceOrder::where('order_status', 'pending')->count();
        $latestOrder = EcommerceOrder::orderBy('id', 'desc')->first();

        return response()->json([
            'pending_count' => $pendingCount,
            'latest_order_id' => $latestOrder ? $latestOrder->id : 0,
            'latest_order_number' => $latestOrder ? $latestOrder->order_number : null,
            'latest_customer' => $latestOrder ? $latestOrder->shipping_name : null,
            'latest_total' => $latestOrder ? 'Rs. ' . number_format($latestOrder->total, 0) : null,
        ]);
    }
}
