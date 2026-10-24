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
        $runningOrders = Sale::where('order_status', 'running')
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
        $customers = Customer::orderBy('customer_name')->get();

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

        // 8. Fetch Active Riders for this branch
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

        $orders = Sale::where('order_status', 'running')
            ->where('sale_status', 'booked')
            ->when($activeBranchId, function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId);
            })
            ->with(['items'])
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

                return [
                    'id' => $s->id,
                    'invoice_no' => $s->invoice_no,
                    'order_type' => $s->order_type ?: 'takeaway',
                    'table_no' => $s->table_no,
                    'token_no' => $s->token_no,
                    'walkin_name' => $s->walkin_name,
                    'total_bill' => (float) $s->total_bill_amount,
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

        return response()->json([
            'success' => true,
            'message' => 'Order #' . $sale->invoice_no . ' cancelled successfully.',
        ]);
    }
}
