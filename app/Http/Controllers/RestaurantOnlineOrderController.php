<?php

namespace App\Http\Controllers;

use App\Helpers\BranchHelper;
use App\Models\Account;
use App\Models\AccountHead;
use App\Models\Branch;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\Product;
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

        // 2. Fetch Active Riders for Branch 2 (or global) & Auto-Sync Duty Status
        Rider::syncDutyStatus();
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

        // Fetch active Cash and Bank accounts for delivery settlement
        $cashAndBankHeads = AccountHead::where(function ($q) {
            $q->whereIn('name', ['Cash', 'Bank', 'cash', 'bank'])
              ->orWhere('name', 'like', '%cash%')
              ->orWhere('name', 'like', '%bank%');
        })->pluck('id');

        $accounts = Account::where(function ($q) use ($cashAndBankHeads) {
            if ($cashAndBankHeads->isNotEmpty()) {
                $q->whereIn('head_id', $cashAndBankHeads);
            }
            $q->orWhere('title', 'like', '%cash%')
              ->orWhere('title', 'like', '%bank%');
        })
        ->where('status', 1)
        ->when($branch, function($q) use ($branch) {
            $q->where(function($b) use ($branch) {
                $b->where('branch_id', $branch->id)->orWhereNull('branch_id');
            });
        })
        ->orderBy('title')
        ->get();

        if ($accounts->isEmpty()) {
            $accounts = Account::where('status', 1)->orderBy('title')->get();
        }
        if ($accounts->isEmpty()) {
            $accounts = Account::all();
        }

        return view('admin_panel.restaurant.online_orders', compact(
            'orders',
            'riders',
            'branch',
            'accounts',
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
     * AJAX: Update Order Status & Settle Delivery
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'account_id' => 'nullable|exists:accounts,id',
            'cash_received' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $order = EcommerceOrder::findOrFail($id);
        $order->order_status = $request->status;

        // Auto update payment if marked delivered
        if ($request->status === 'delivered') {
            $order->payment_status = 'paid';
            if ($request->filled('discount') && $request->discount > 0) {
                $order->discount = (float)$request->discount;
                if (\Illuminate\Support\Facades\Schema::hasColumn('ecommerce_orders', 'discount_amount')) {
                    $order->discount_amount = (float)$request->discount;
                }
                $order->total = max(0, (float)$order->subtotal + (float)$order->delivery_charges - (float)$request->discount);
            }
            $order->paid_amount = $order->total;
        }

        $order->save();

        // Synchronize with Central Sales, Inventory, Accounting and Riders
        $sale = RestaurantOnlineOrderService::syncOrderToSale($order, $request->account_id);

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} " . ($request->status === 'delivered' ? 'settled and marked as DELIVERED!' : 'status updated to ' . strtoupper($request->status)),
            'new_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'receipt_url' => $sale ? route('sales.receipt', $sale->id) : route('restaurant.online_orders.slip', $order->id),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Print Delivery Slip / Main Thermal Bill
     */
    public function deliverySlip($id)
    {
        $order = EcommerceOrder::with(['items', 'rider', 'sale'])->findOrFail($id);
        if ($order->sale_id) {
            return redirect()->route('sales.receipt', $order->sale_id);
        }
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

    /**
     * AJAX: Get Order Details for Editing
     */
    public function getOrderDetails($id)
    {
        $order = EcommerceOrder::with(['items.product', 'rider', 'sale'])->findOrFail($id);

        $branchId = $order->branch_id ?: 2;
        $products = Product::where('is_active', true)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderBy('item_name')
            ->get(['id', 'item_name', 'sale_price_per_piece', 'web_sale_price', 'wholesale_price'])
            ->map(function ($p) {
                $price = (float)($p->web_sale_price > 0 ? $p->web_sale_price : ($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : ($p->wholesale_price ?? 0)));
                return [
                    'id' => $p->id,
                    'item_name' => $p->item_name,
                    'retail_price' => $price,
                ];
            });

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'shipping_name' => $order->shipping_name,
                'shipping_phone' => $order->shipping_phone,
                'shipping_address' => $order->shipping_address,
                'shipping_city' => $order->shipping_city ?? 'Hyderabad',
                'order_notes' => $order->order_notes,
                'order_status' => $order->order_status,
                'payment_method' => $order->payment_method ?? 'COD',
                'payment_status' => $order->payment_status,
                'subtotal' => (float)$order->subtotal,
                'delivery_charges' => (float)$order->delivery_charges,
                'discount' => (float)($order->discount ?: ($order->discount_amount ?: 0)),
                'total' => (float)$order->total,
                'items' => $order->items->map(function ($it) {
                    return [
                        'id' => $it->id,
                        'product_id' => $it->product_id,
                        'product_name' => $it->product_name,
                        'price' => (float)$it->price,
                        'quantity' => (int)$it->quantity,
                        'total' => (float)$it->total,
                    ];
                }),
            ],
            'products' => $products,
        ]);
    }

    /**
     * AJAX: Update Order Details, Items, and Recalculate Totals
     */
    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'shipping_name' => 'nullable|string|max:191',
            'shipping_phone' => 'required|string|max:50',
            'shipping_address' => 'nullable|string|max:500',
            'order_notes' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:50',
            'order_status' => 'nullable|string|in:pending,processing,shipped,delivered,cancelled',
            'delivery_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:191',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = EcommerceOrder::with('items')->findOrFail($id);

        DB::transaction(function () use ($request, $order) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $qty = (int)$item['quantity'];
                $price = (float)$item['price'];
                $lineTotal = round($qty * $price, 2);
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id' => !empty($item['product_id']) ? $item['product_id'] : null,
                    'product_name' => trim($item['product_name']),
                    'price' => $price,
                    'quantity' => $qty,
                    'total' => $lineTotal,
                ];
            }

            $deliveryCharges = (float)($request->delivery_charges ?? 0);
            $discount = (float)($request->discount ?? 0);
            $total = max(0, round($subtotal + $deliveryCharges - $discount, 2));

            // Update EcommerceOrder
            $order->shipping_name = trim($request->shipping_name);
            $order->shipping_phone = trim($request->shipping_phone);
            $order->shipping_address = trim($request->shipping_address ?? '');
            $order->order_notes = trim($request->order_notes ?? '');
            $order->subtotal = $subtotal;
            $order->delivery_charges = $deliveryCharges;
            $order->discount = $discount;
            if (\Illuminate\Support\Facades\Schema::hasColumn('ecommerce_orders', 'discount_amount')) {
                $order->discount_amount = $discount;
            }
            $order->total = $total;

            if ($request->filled('payment_method')) {
                $order->payment_method = $request->payment_method;
            }

            if ($request->filled('order_status')) {
                $order->order_status = $request->order_status;
                if ($order->order_status === 'delivered') {
                    $order->payment_status = 'paid';
                    $order->paid_amount = $total;
                }
            }

            $order->save();

            // Re-sync items in EcommerceOrderItem
            EcommerceOrderItem::where('ecommerce_order_id', $order->id)->delete();
            foreach ($itemsData as $data) {
                EcommerceOrderItem::create([
                    'ecommerce_order_id' => $order->id,
                    'product_id' => $data['product_id'],
                    'product_name' => $data['product_name'],
                    'price' => $data['price'],
                    'quantity' => $data['quantity'],
                    'total' => $data['total'],
                ]);
            }

            // Sync with Central Sales, Kitchen, and Inventory
            RestaurantOnlineOrderService::syncOrderToSale($order);
        });

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} has been updated successfully!",
            'order' => $order->fresh(['items']),
        ]);
    }
}
