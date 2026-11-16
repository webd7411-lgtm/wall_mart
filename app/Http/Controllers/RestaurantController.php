<?php

namespace App\Http\Controllers;

use App\Helpers\BranchHelper;
use App\Models\Account;
use App\Models\AccountHead;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    /**
     * Display the Restaurant POS Terminal.
     */
    public function pos(Request $request)
    {
        // If current active branch is not a restaurant branch, redirect to standard POS
        if (!BranchHelper::isRestaurantBranch()) {
            return redirect()->route('pos.index');
        }

        $activeBranchId = BranchHelper::getActiveBranchId();
        $branch = BranchHelper::getActiveBranch();

        // 1. Fetch Categories for this branch
        $categoriesQuery = Category::query();
        if ($activeBranchId) {
            $categoriesQuery->where(function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
            });
        }
        $categories = $categoriesQuery->orderBy('name')->get();

        // 2. Fetch Products for this branch
        $productsQuery = Product::where('is_active', true);
        if ($activeBranchId) {
            $productsQuery->where('branch_id', $activeBranchId);
        }
        $products = $productsQuery->with('category_relation')->orderBy('item_name')->get();

        $menuItems = $products->map(function ($p) {
            $variants = [];
            if ($p->color) {
                try {
                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                    if (is_string($parsed)) {
                        $parsed = json_decode($parsed, true);
                    }
                    if (is_array($parsed) && count($parsed) > 0 && (isset($parsed[0]['name']) || isset($parsed[0]['size']))) {
                        $variants = $parsed;
                    }
                } catch (\Exception $e) {}
            }

            $variantItems = [];
            if (!empty($variants)) {
                foreach ($variants as $v) {
                    $size = (isset($v['size']) && $v['size'] !== '-') ? " {$v['size']}" : '';
                    $color = (isset($v['color']) && $v['color'] !== '-') ? " ({$v['color']})" : '';
                    $vName = $v['name'] ?? ($p->item_name . $size . $color);
                    $vPrice = (float) (!empty($v['sale_price']) ? $v['sale_price'] : ($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : 0));
                    $vStock = (float) ($v['stock'] ?? 0);
                    $variantJson = json_encode($v);

                    $variantItems[] = [
                        'id' => $p->id,
                        'name' => $vName,
                        'size_val' => $v['size'] ?? '-',
                        'color_val' => $v['color'] ?? '-',
                        'price' => $vPrice,
                        'stock' => $vStock,
                        'variant_data' => base64_encode($variantJson),
                    ];
                }
            }

            return [
                'id' => $p->id,
                'name' => $p->item_name,
                'category_id' => $p->category_id,
                'category_name' => optional($p->category_relation)->name ?? 'General',
                'sku' => $p->item_code ?? '',
                'price' => (float) ($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : ($p->wholesale_price ?? 0)),
                'image' => $p->image ? asset('uploads/products/' . $p->image) : null,
                'is_active' => (bool) $p->is_active,
                'has_variants' => count($variantItems) > 0,
                'variants' => $variantItems,
            ];
        });

        // 3. Fetch Active Running Orders (Dine-In tables, Takeaways, Deliveries in progress)
        $runningOrders = Sale::whereIn('order_status', ['running', 'served', 'on_delivery'])
            ->where('sale_status', 'booked')
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->with(['items.product', 'customer_relation'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // 4. Accounts (Cash/Bank)
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
        })->where('status', 1)->orderBy('title')->get();

        if ($accounts->isEmpty()) {
            $accounts = Account::where('status', 1)->orderBy('title')->get();
        }

        // 5. Customers
        $customersQuery = Customer::query();
        if ($activeBranchId) {
            $customersQuery->where(function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
            });
        }
        $customers = $customersQuery->where('status', '!=', 'inactive')->orderBy('customer_name')->get();

        // 6. Next Token Number for today
        $todayOrdersCount = Sale::whereDate('created_at', today())
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->count();
        $nextTokenNo = $todayOrdersCount + 1;

        // 7. Fetch Restaurant Tables for this branch
        $tables = \App\Models\RestaurantTable::where('is_active', true)
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->orderBy('floor_area')
            ->orderBy('sort_order')
            ->orderBy('table_no')
            ->get();

        $runningOrdersKeyed = $runningOrders->keyBy('table_no');
        foreach ($tables as $t) {
            $t->is_occupied = $runningOrdersKeyed->has($t->table_no);
            $t->active_order = $runningOrdersKeyed->get($t->table_no);
        }

        // 8. Fetch Active Riders for this branch & Auto-Sync Duty Status
        \App\Models\Rider::syncDutyStatus();
        $riders = \App\Models\Rider::where('is_active', true)
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->orderBy('name')
            ->get();

        return view('admin_panel.pos.restaurant_pos', compact(
            'branch',
            'categories',
            'menuItems',
            'runningOrders',
            'accounts',
            'customers',
            'nextTokenNo',
            'tables',
            'riders'
        ));
    }

    /**
     * Return live active running orders for ajax polling / updates.
     */
    public function activeOrders()
    {
        $activeBranchId = BranchHelper::getActiveBranchId();

        $orders = Sale::whereIn('order_status', ['running', 'served', 'on_delivery'])
            ->where('sale_status', 'booked')
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->with(['items.product', 'customer_relation'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($s) {
                $itemsSummary = $s->items->map(function ($it) {
                    $qty = (int) ($it->total_pieces > 0 ? $it->total_pieces : ($it->qty > 0 ? $it->qty : 1));
                    $name = $it->product_name ?? ($it->product->item_name ?? 'Item');
                    if (!empty($it->color)) {
                        $decoded = base64_decode($it->color, true);
                        $vData = $decoded !== false ? json_decode($decoded, true) : json_decode($it->color, true);
                        if (is_array($vData)) {
                            if (!empty($vData['name'])) {
                                $name = $vData['name'];
                            } elseif (!empty($vData['size']) && $vData['size'] !== '-') {
                                $name .= ' (' . $vData['size'] . ')';
                            }
                        }
                    }
                    return "{$qty}x {$name}";
                })->implode(', ');

                $isPaid = (float)$s->cash >= (float)$s->total_net && (float)$s->total_net > 0;
                $orderStatus = $s->order_status ?: 'running';
                if ($s->order_type === 'delivery' && $s->delivery_status === 'dispatched') {
                    $orderStatus = 'on_delivery';
                }

                return [
                    'id' => $s->id,
                    'invoice_no' => $s->invoice_no,
                    'order_type' => $s->order_type ?: 'takeaway',
                    'table_no' => $s->table_no,
                    'token_no' => $s->token_no,
                    'customer_name' => $s->walkin_name ?: (optional($s->customer_relation)->customer_name ?: 'Walk-in'),
                    'customer_phone' => optional($s->customer_relation)->phone_number ?: ($s->rider_phone ?: ''),
                    'rider_id' => $s->rider_id,
                    'rider_name' => $s->rider_name,
                    'delivery_address' => $s->delivery_address,
                    'order_status' => $orderStatus,
                    'delivery_status' => $s->delivery_status ?: 'pending',
                    'payment_status' => $isPaid ? 'paid' : 'unpaid',
                    'total_bill' => (float) $s->total_bill_amount,
                    'total_net' => (float) ($s->total_net ?: $s->total_bill_amount),
                    'items_count' => $s->items->count(),
                    'items_summary' => $itemsSummary,
                    'elapsed_time' => $s->created_at ? $s->created_at->diffForHumans(null, true) : 'just now',
                    'created_at' => $s->created_at ? $s->created_at->format('h:i A') : '',
                ];
            });

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'count' => $orders->count(),
        ]);
    }

    /**
     * Dedicated Running Orders Page (Simple Clean List View).
     */
    public function runningOrdersIndex(Request $request)
    {
        $activeBranchId = BranchHelper::getActiveBranchId();
        $branch = BranchHelper::getActiveBranch();

        $query = Sale::whereIn('order_status', ['running', 'served', 'on_delivery'])
            ->where('sale_status', 'booked')
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->with(['items.product', 'customer_relation', 'rider']);

        // Type filter: all, dine_in, takeaway, delivery, unpaid
        $typeFilter = $request->get('type', 'all');
        if ($typeFilter === 'dine_in') {
            $query->where('order_type', 'dine_in');
        } elseif ($typeFilter === 'takeaway') {
            $query->where('order_type', 'takeaway');
        } elseif ($typeFilter === 'delivery') {
            $query->where('order_type', 'delivery');
        } elseif ($typeFilter === 'unpaid') {
            $query->where(function($q) {
                $q->where('cash', '<', DB::raw('total_net'))
                  ->orWhereNull('cash')
                  ->orWhere('cash', 0);
            });
        }

        // Search filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhere('table_no', 'like', "%{$s}%")
                  ->orWhere('token_no', 'like', "%{$s}%")
                  ->orWhere('walkin_name', 'like', "%{$s}%")
                  ->orWhere('rider_name', 'like', "%{$s}%")
                  ->orWhereHas('customer_relation', function($cq) use ($s) {
                      $cq->where('customer_name', 'like', "%{$s}%")
                         ->orWhere('phone_number', 'like', "%{$s}%");
                  });
            });
        }

        $orders = $query->orderBy('updated_at', 'desc')->paginate(30)->withQueryString();

        // Stats summary
        $baseStatQuery = Sale::whereIn('order_status', ['running', 'served', 'on_delivery'])
            ->where('sale_status', 'booked')
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            });

        $stats = [
            'total' => (clone $baseStatQuery)->count(),
            'dine_in' => (clone $baseStatQuery)->where('order_type', 'dine_in')->count(),
            'takeaway' => (clone $baseStatQuery)->where('order_type', 'takeaway')->count(),
            'delivery' => (clone $baseStatQuery)->where('order_type', 'delivery')->count(),
            'unpaid_amount' => (clone $baseStatQuery)->sum('total_bill_amount'),
        ];

        // Riders for modal & Auto-Sync Duty Status
        \App\Models\Rider::syncDutyStatus();
        $riders = \App\Models\Rider::where('is_active', true)
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->orderBy('name')
            ->get();

        // Accounts for complete payment
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
        })->where('status', 1)->orderBy('title')->get();

        if ($accounts->isEmpty()) {
            $accounts = Account::where('status', 1)->orderBy('title')->get();
        }

        return view('admin_panel.restaurant.running_orders.index', compact(
            'orders',
            'stats',
            'typeFilter',
            'riders',
            'accounts',
            'branch'
        ));
    }

    /**
     * Fetch full order details JSON for Details Modal.
     */
    public function orderDetailsJson($id)
    {
        $sale = Sale::with(['items.product', 'customer_relation', 'rider'])->find($id);
        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $items = $sale->items->map(function ($it) {
            $p = $it->product;
            $qty = (int) ($it->total_pieces > 0 ? $it->total_pieces : ($it->qty > 0 ? $it->qty : 1));
            $displayName = $it->product_name ?? ($p->item_name ?? 'Item');
            $variant = '';
            if (!empty($it->color)) {
                $decoded = base64_decode($it->color, true);
                $vData = $decoded !== false ? json_decode($decoded, true) : json_decode($it->color, true);
                if (is_array($vData)) {
                    if (!empty($vData['name'])) {
                        $displayName = $vData['name'];
                    } elseif (!empty($vData['size']) && $vData['size'] !== '-') {
                        $variant = $vData['size'];
                    }
                }
            }

            return [
                'name' => $displayName,
                'variant' => $variant,
                'qty' => $qty,
                'price' => (float)$it->price,
                'total' => (float)$it->total,
                'notes' => $it->item_notes ?? '',
            ];
        });

        $isPaid = (float)$sale->cash >= (float)$sale->total_net && (float)$sale->total_net > 0;
        $orderStatus = $sale->order_status ?: 'running';
        if ($sale->order_type === 'delivery' && $sale->delivery_status === 'dispatched') {
            $orderStatus = 'on_delivery';
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'order_type' => $sale->order_type ?: 'takeaway',
                'table_no' => $sale->table_no,
                'token_no' => $sale->token_no,
                'customer_name' => $sale->walkin_name ?: (optional($sale->customer_relation)->customer_name ?: 'Walk-in Customer'),
                'customer_phone' => optional($sale->customer_relation)->phone_number ?: '',
                'rider_id' => $sale->rider_id,
                'rider_name' => $sale->rider_name,
                'rider_phone' => $sale->rider_phone,
                'delivery_address' => $sale->delivery_address,
                'delivery_charges' => (float)$sale->delivery_charges,
                'order_notes' => $sale->order_notes ?? '',
                'order_status' => $orderStatus,
                'delivery_status' => $sale->delivery_status ?: 'pending',
                'payment_status' => $isPaid ? 'paid' : 'unpaid',
                'subtotal' => (float)$sale->total_bill_amount,
                'discount' => (float)$sale->total_extradiscount,
                'net_total' => (float)($sale->total_net ?: $sale->total_bill_amount),
                'cash_paid' => (float)$sale->cash,
                'change' => (float)$sale->change,
                'created_at' => $sale->created_at ? $sale->created_at->format('d M Y, h:i A') : '',
                'elapsed' => $sale->created_at ? $sale->created_at->diffForHumans() : '',
                'items' => $items,
            ]
        ]);
    }

    /**
     * Mark Dine-In Order as Served (No waiter selection required).
     */
    public function serveOrder($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->order_status = 'served';
        $sale->save();

        return response()->json([
            'success' => true,
            'message' => 'Order #' . $sale->invoice_no . ' marked as Served.',
            'order_status' => 'served',
        ]);
    }

    /**
     * Assign Rider to Delivery Order (Sets status to on_delivery and syncs to EcommerceOrder if applicable).
     */
    public function assignRider(Request $request, $id)
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id',
        ]);

        $sale = Sale::findOrFail($id);
        $rider = \App\Models\Rider::findOrFail($request->rider_id);

        $sale->rider_id = $rider->id;
        $sale->rider_name = $rider->name;
        $sale->rider_phone = $rider->phone;
        $sale->delivery_status = 'dispatched';
        $sale->order_status = 'on_delivery';
        $sale->dispatched_at = now();
        $sale->save();

        // Update rider duty status
        $rider->duty_status = 'on_delivery';
        $rider->save();

        // BIDIRECTIONAL SYNC: Update linked EcommerceOrder so Online Orders page reflects assigned rider
        try {
            $ecomOrder = null;
            if ($sale->ecommerce_order_id) {
                $ecomOrder = \App\Models\EcommerceOrder::find($sale->ecommerce_order_id);
            }
            if (!$ecomOrder) {
                $ecomOrder = \App\Models\EcommerceOrder::where('sale_id', $sale->id)->first();
            }
            if ($ecomOrder) {
                $ecomOrder->rider_id = $rider->id;
                $ecomOrder->rider_name = $rider->name;
                $ecomOrder->rider_phone = $rider->phone;
                $ecomOrder->order_status = 'shipped';
                $ecomOrder->save();
            }
        } catch (\Exception $e) {
            \Log::warning('EcommerceOrder rider sync error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Order #{$sale->invoice_no} assigned to {$rider->name} (On Delivery).",
            'rider_name' => $rider->name,
            'order_status' => 'on_delivery',
        ]);
    }

    /**
     * Unified Complete & Settle Action for Dine-In, Takeaway, and Delivery.
     * Receives payment, selects account, clears table/rider, and prints final receipt.
     */
    public function completeAndSettle(Request $request, $id)
    {
        $sale = Sale::with(['items', 'customer_relation'])->findOrFail($id);

        if ($sale->sale_status === 'posted') {
            return response()->json([
                'success' => true,
                'message' => 'Order is already completed.',
                'receipt_url' => route('sales.receipt', $sale->id),
                'sale_id' => $sale->id,
            ]);
        }

        DB::beginTransaction();
        try {
            // Apply optional extra discount
            if ($request->filled('discount') && (float)$request->discount > 0) {
                $discount = (float)$request->discount;
                $sale->total_extradiscount = $discount;
                $sale->total_net = max(0, (float)$sale->total_bill_amount - $discount + (float)$sale->delivery_charges);
            }

            $totalNet = (float)($sale->total_net ?: $sale->total_bill_amount);
            $cashReceived = $request->filled('cash_received') ? (float)$request->cash_received : $totalNet;
            $payAmount = min($cashReceived, $totalNet);
            $change = max(0, $cashReceived - $totalNet);

            $sale->cash = $payAmount;
            $sale->change = $change;
            $sale->sale_status = 'posted';
            $sale->order_status = 'completed';
            $sale->is_booking = 0;

            if ($sale->order_type === 'delivery') {
                $sale->delivery_status = 'delivered';
                $sale->delivered_at = now();
                $sale->rider_cash_settled = 1;
            }

            $sale->save();

            // BIDIRECTIONAL SYNC: Update linked EcommerceOrder status to delivered
            try {
                $ecomOrder = null;
                if ($sale->ecommerce_order_id) {
                    $ecomOrder = \App\Models\EcommerceOrder::find($sale->ecommerce_order_id);
                }
                if (!$ecomOrder) {
                    $ecomOrder = \App\Models\EcommerceOrder::where('sale_id', $sale->id)->first();
                }
                if ($ecomOrder) {
                    $ecomOrder->order_status = 'delivered';
                    $ecomOrder->save();
                }
            } catch (\Exception $e) {
                \Log::warning('EcommerceOrder delivery complete sync error: ' . $e->getMessage());
            }

            // Release rider if any
            if ($sale->rider_id) {
                $activeRiderDeliveries = Sale::where('rider_id', $sale->rider_id)
                    ->where('id', '!=', $sale->id)
                    ->where('delivery_status', 'dispatched')
                    ->count();
                if ($activeRiderDeliveries === 0) {
                    \App\Models\Rider::where('id', $sale->rider_id)->update(['duty_status' => 'available']);
                }
            }

            // Deduct stock from warehouse
            try {
                app(\App\Http\Controllers\SaleController::class)->handleStockImpact($sale, 'out');
            } catch (\Exception $e) {
                \Log::warning('Stock impact error on complete: ' . $e->getMessage());
            }

            // Ledger updates
            try {
                $saleController = app(\App\Http\Controllers\SaleController::class);
                $saleController->updateLedger($sale);

                $balanceService = app(\App\Services\BalanceService::class);
                $cust = $sale->customer_relation ?? \App\Models\Customer::find($sale->customer_id);
                if ($cust) {
                    $balanceService->createSaleVoucher(
                        $cust,
                        $totalNet,
                        $sale->invoice_no,
                        $sale->created_at ? $sale->created_at->format('Y-m-d') : date('Y-m-d')
                    );
                }

                // Auto receipt transaction
                $accountId = $request->account_id;
                if ($payAmount > 0 && $accountId) {
                    $transactionService = app(\App\Services\TransactionService::class);
                    $transactionService->createReceiptFromSale($sale, [$accountId], [$payAmount]);
                }
            } catch (\Exception $e) {
                \Log::warning('Ledger/Transaction error on complete: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Order #{$sale->invoice_no} completed & settled successfully.",
                'receipt_url' => route('sales.receipt', $sale->id),
                'sale_id' => $sale->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to settle order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Thermal Provisional Bill / Slip (Print before payment).
     */
    public function billSlip($id)
    {
        return redirect()->route('sales.receipt', ['id' => $id, 'provisional' => 1]);
    }

    /**
     * Fetch a specific order to load back into POS cart.
     */
    public function getOrder($id)
    {
        $sale = Sale::with(['items.product', 'customer_relation'])->find($id);

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $items = $sale->items->map(function ($item) {
            $p = $item->product;
            $qty = (int) ($item->total_pieces > 0 ? $item->total_pieces : ($item->qty > 0 ? $item->qty : 1));
            $displayName = $item->product_name ?? ($p->item_name ?? 'Item');
            if (!empty($item->color)) {
                $decoded = base64_decode($item->color, true);
                $vData = $decoded !== false ? json_decode($decoded, true) : json_decode($item->color, true);
                if (is_array($vData)) {
                    if (!empty($vData['name'])) {
                        $displayName = $vData['name'];
                    } elseif (!empty($vData['size']) && $vData['size'] !== '-') {
                        $displayName .= ' (' . $vData['size'] . ')';
                    }
                }
            }

            return [
                'id' => $item->product_id,
                'name' => $displayName,
                'price' => (float) $item->price,
                'qty' => $qty,
                'item_notes' => $item->item_notes ?? '',
                'line_total' => (float) $item->total,
                'kot_printed_qty' => (float) ($item->kot_printed_qty ?? 0),
                'variant_data' => $item->color ?? '',
            ];
        });

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'order_type' => $sale->order_type ?: 'takeaway',
                'table_no' => $sale->table_no,
                'token_no' => $sale->token_no,
                'customer_id' => $sale->customer_id,
                'walkin_name' => $sale->walkin_name ?: 'Walk-in Customer',
                'order_notes' => $sale->order_notes ?? '',
                'rider_name' => $sale->rider_name ?? '',
                'rider_phone' => $sale->rider_phone ?? '',
                'total_bill' => (float) $sale->total_bill_amount,
                'items' => $items,
            ],
        ]);
    }

    /**
     * Cancel / Void a running order.
     */
    public function cancelOrder($id, Request $request)
    {
        $sale = Sale::find($id);
        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $sale->order_status = 'cancelled';
        $sale->sale_status = 'cancelled';
        $sale->order_notes = ($sale->order_notes ? $sale->order_notes . ' | ' : '') . 'Cancelled: ' . ($request->reason ?? 'By cashier');
        $sale->save();

        // BIDIRECTIONAL SYNC: Cancel linked EcommerceOrder
        try {
            if ($sale->ecommerce_order_id) {
                \App\Models\EcommerceOrder::where('id', $sale->ecommerce_order_id)->update(['order_status' => 'cancelled']);
            }
            \App\Models\EcommerceOrder::where('sale_id', $sale->id)->update(['order_status' => 'cancelled']);
        } catch (\Exception $e) {
            \Log::warning('EcommerceOrder cancel sync error: ' . $e->getMessage());
        }

        if ($sale->rider_id) {
            \App\Models\Rider::syncDutyStatus($sale->rider_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order #' . $sale->invoice_no . ' cancelled successfully.',
        ]);
    }
}
