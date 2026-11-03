<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\EcommerceOrder;
use App\Models\Rider;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestaurantOnlineOrderService
{
    /**
     * Synchronize an EcommerceOrder into the central Sales, Inventory, and Accounting systems.
     */
    public static function syncOrderToSale(EcommerceOrder $order): ?Sale
    {
        try {
            return DB::transaction(function () use ($order) {
                $order->loadMissing('items');

                $branchId = $order->branch_id ?: 2;
                $isDelivered = ($order->order_status === 'delivered');
                $isCancelled = ($order->order_status === 'cancelled');
                $isDispatched = ($order->order_status === 'shipped');

                // Determine central Sale statuses
                if ($isDelivered) {
                    $saleStatus = 'posted';
                    $orderStatus = 'completed';
                    $deliveryStatus = 'delivered';
                } elseif ($isCancelled) {
                    $saleStatus = 'booked';
                    $orderStatus = 'cancelled';
                    $deliveryStatus = 'cancelled';
                } elseif ($isDispatched) {
                    $saleStatus = 'booked';
                    $orderStatus = 'running';
                    $deliveryStatus = 'dispatched';
                } else {
                    $saleStatus = 'booked';
                    $orderStatus = 'running';
                    $deliveryStatus = 'pending';
                }

                // 1. Find existing Sale or create new
                $sale = null;
                if (!empty($order->sale_id)) {
                    $sale = Sale::find($order->sale_id);
                }
                if (!$sale) {
                    $sale = Sale::where('ecommerce_order_id', $order->id)->first();
                }
                if (!$sale) {
                    $sale = new Sale();
                    $sale->invoice_no = $order->order_number;
                }

                $sale->ecommerce_order_id = $order->id;
                $sale->branch_id = $branchId;
                $sale->order_type = 'delivery';
                $sale->reference = 'Website Order #' . $order->order_number;
                $sale->walkin_name = $order->shipping_name;
                $sale->delivery_address = $order->shipping_address;
                $sale->delivery_charges = (float)$order->delivery_charges;
                $sale->order_notes = $order->order_notes;
                $sale->sale_status = $saleStatus;
                $sale->order_status = $orderStatus;
                $sale->delivery_status = $deliveryStatus;

                // Rider info
                if (!empty($order->rider_id)) {
                    $sale->rider_id = $order->rider_id;
                    $sale->rider_name = $order->rider_name;
                    $sale->rider_phone = $order->rider_phone;
                    if ($isDispatched && empty($sale->dispatched_at)) {
                        $sale->dispatched_at = now();
                    }
                }

                if ($isDelivered && empty($sale->delivered_at)) {
                    $sale->delivered_at = now();
                    // Cash is with rider until settled in restaurant/riders
                    $sale->rider_cash_settled = false;
                }

                // Totals
                $totalPieces = $order->items->sum('quantity');
                $sale->total_items = $totalPieces;
                $sale->total_bill_amount = (float)$order->subtotal;
                $sale->total_net = (float)$order->total;
                $sale->cash = (float)$order->total;
                $sale->change = 0;
                $sale->user_id = auth()->id() ?? 1;

                if (empty($sale->created_at)) {
                    $sale->created_at = $order->created_at ?: now();
                }

                $sale->save();

                // Link back to EcommerceOrder
                if ($order->sale_id !== $sale->id) {
                    $order->sale_id = $sale->id;
                    $order->save();
                }

                // 2. Synchronize Sale Items
                SaleItem::where('sale_id', $sale->id)->delete();
                foreach ($order->items as $it) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $it->product_id,
                        'product_name' => $it->product_name,
                        'qty' => 1,
                        'total_pieces' => $it->quantity,
                        'loose_pieces' => 0,
                        'price' => (float)$it->price,
                        'discount_percent' => 0,
                        'discount_amount' => 0,
                        'total' => (float)$it->total,
                        'warehouse_id' => 1,
                    ]);
                }

                // 3. When Delivered: Deduct Stock and post to Cash Accounting
                if ($isDelivered) {
                    // A. Stock deduction (only once)
                    if (!$order->is_stock_deducted) {
                        foreach ($order->items as $it) {
                            if (!empty($it->product_id)) {
                                $stock = WarehouseStock::firstOrCreate(
                                    ['warehouse_id' => 1, 'product_id' => $it->product_id],
                                    ['total_pieces' => 0, 'quantity' => 0, 'price' => 0]
                                );
                                $stock->total_pieces = max(0, $stock->total_pieces - $it->quantity);
                                $stock->quantity = $stock->total_pieces;
                                $stock->save();

                                DB::table('stock_movements')->insert([
                                    'product_id' => $it->product_id,
                                    'type' => 'out',
                                    'qty' => $it->quantity,
                                    'ref_type' => 'SALE',
                                    'ref_id' => $sale->id,
                                    'note' => "Website Delivery Order #{$order->order_number}",
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        $order->is_stock_deducted = 1;
                        $order->payment_status = 'paid';
                        $order->paid_amount = $order->total;
                        $order->save();
                    }

                    // B. Auto Accounting Receipt into Restaurant Cash Account
                    try {
                        $balanceService = app(BalanceService::class);
                        $transactionService = app(TransactionService::class);
                        $cashAccountId = $balanceService->getCashAccountId();
                        if ($cashAccountId) {
                            $transactionService->createReceiptFromSale($sale, [$cashAccountId], [$sale->total_net]);
                        }
                    } catch (\Exception $accEx) {
                        Log::error("Accounting receipt error for online order #{$order->order_number}: " . $accEx->getMessage());
                    }

                    // C. Release Rider if no other active deliveries
                    if ($order->rider_id) {
                        $activeCount = Sale::where('rider_id', $order->rider_id)
                            ->where('delivery_status', 'dispatched')
                            ->count();
                        if ($activeCount === 0) {
                            Rider::where('id', $order->rider_id)->update(['duty_status' => 'available']);
                        }
                    }
                }

                return $sale;
            });
        } catch (\Exception $e) {
            Log::error("Error syncing EcommerceOrder #{$order->order_number} to Sale: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Assign a rider to an online order and dispatch it.
     */
    public static function assignRider(EcommerceOrder $order, $riderId, $newRiderName = null, $newRiderPhone = null): Rider
    {
        // If creating a brand new rider on the fly
        if ($riderId === 'new' || (empty($riderId) && !empty($newRiderName))) {
            $rider = Rider::create([
                'branch_id' => $order->branch_id ?: 2,
                'name' => trim($newRiderName),
                'phone' => trim($newRiderPhone ?? ''),
                'duty_status' => 'on_delivery',
                'is_active' => true,
            ]);
        } else {
            $rider = Rider::findOrFail($riderId);
            $rider->duty_status = 'on_delivery';
            $rider->save();
        }

        $order->rider_id = $rider->id;
        $order->rider_name = $rider->name;
        $order->rider_phone = $rider->phone;
        $order->order_status = 'shipped';
        $order->save();

        self::syncOrderToSale($order);

        return $rider;
    }

    /**
     * Sync all existing EcommerceOrders that haven't been linked to Sales yet.
     */
    public static function syncAllExistingOrders(): int
    {
        $orders = EcommerceOrder::with('items')->get();
        $count = 0;
        foreach ($orders as $order) {
            self::syncOrderToSale($order);
            $count++;
        }
        return $count;
    }
}
