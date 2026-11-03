<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestaurantWebsiteController extends Controller
{
    /**
     * Get the designated Restaurant Branch ID
     */
    private function getRestaurantBranchId(): int
    {
        $branch = Branch::where('type', 'restaurant')
            ->orWhere('id', 2)
            ->first();

        return $branch ? (int)$branch->id : 2;
    }

    /**
     * Display the Restaurant Main Landing Page & Interactive Menu
     */
    public function index(Request $request)
    {
        $restaurantBranchId = $this->getRestaurantBranchId();

        // 1. Restaurant Settings & Info for Restaurant Branch
        $restaurantName = "MUNCH FRIED CHICKEN";
        $phone = "0300-1234567";
        $address = "Main Commercial Boulevard, Food Street";
        $whatsappNumber = "923001234567";
        $openingHours = "12:00 PM – 02:00 AM Daily";

        $branch = Branch::find($restaurantBranchId);
        if ($branch && !empty($branch->name)) {
            $restaurantName = $branch->name;
        }

        $branchSettings = DB::table('settings')->where('branch_id', $restaurantBranchId)->pluck('value', 'key');
        if (!empty($branchSettings['company_name'])) {
            $restaurantName = $branchSettings['company_name'];
        }
        if (!empty($branchSettings['company_phone'])) {
            $phone = $branchSettings['company_phone'];
            $whatsappNumber = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($whatsappNumber, '0')) {
                $whatsappNumber = '92' . substr($whatsappNumber, 1);
            }
        }
        if (!empty($branchSettings['company_address'])) {
            $address = $branchSettings['company_address'];
        }

        // 2. Fetch Categories ONLY for the Restaurant Branch
        $categories = Category::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('branch_id', $restaurantBranchId)
            ->orderBy('id', 'asc')
            ->get();

        // 3. Fetch Products ONLY for the Restaurant Branch
        $rawProducts = Product::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('branch_id', $restaurantBranchId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->with(['category_relation'])
            ->orderBy('id', 'desc')
            ->get();

        // Fallback images if no photo is attached yet
        $placeholderImages = [
            'chicken' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=800&q=80',
            'burger' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=800&q=80',
            'wings' => 'https://images.unsplash.com/photo-1527477321055-43615862e771?auto=format&fit=crop&w=800&q=80',
            'deal' => 'https://images.unsplash.com/photo-1513639776629-7b61b0ac49cb?auto=format&fit=crop&w=800&q=80',
            'fries' => 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?auto=format&fit=crop&w=800&q=80',
            'drink' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=800&q=80',
            'default' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=800&q=80',
        ];

        $products = $rawProducts->map(function ($p) use ($placeholderImages) {
            $price = (float)($p->web_sale_price > 0 
                ? $p->web_sale_price 
                : ($p->sale_price_per_piece > 0 
                    ? $p->sale_price_per_piece 
                    : ($p->sale_price_per_box > 0 ? $p->sale_price_per_box : 0)));

            $img = $p->web_main_image ?: $p->image;
            $imageUrl = '';
            if (!empty($img)) {
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                    $imageUrl = $img;
                } elseif (file_exists(public_path('uploads/products/' . $img))) {
                    $imageUrl = asset('uploads/products/' . $img);
                } elseif (file_exists(public_path($img))) {
                    $imageUrl = asset($img);
                } else {
                    $imageUrl = asset('uploads/products/' . $img);
                }
            }

            if (empty($imageUrl)) {
                $lowerName = strtolower($p->item_name);
                if (str_contains($lowerName, 'burger')) {
                    $imageUrl = $placeholderImages['burger'];
                } elseif (str_contains($lowerName, 'wing') || str_contains($lowerName, 'tender') || str_contains($lowerName, 'nugget')) {
                    $imageUrl = $placeholderImages['wings'];
                } elseif (str_contains($lowerName, 'feast') || str_contains($lowerName, 'bucket') || str_contains($lowerName, 'deal') || str_contains($lowerName, 'family')) {
                    $imageUrl = $placeholderImages['deal'];
                } elseif (str_contains($lowerName, 'fries') || str_contains($lowerName, 'side')) {
                    $imageUrl = $placeholderImages['fries'];
                } elseif (str_contains($lowerName, 'drink') || str_contains($lowerName, 'shake') || str_contains($lowerName, 'juice') || str_contains($lowerName, 'lemonade') || str_contains($lowerName, 'beverage')) {
                    $imageUrl = $placeholderImages['drink'];
                } else {
                    $imageUrl = $placeholderImages['chicken'];
                }
            }

            $catName = $p->category_relation ? $p->category_relation->name : 'Restaurant Menu';

            return [
                'id' => $p->id,
                'name' => $p->item_name,
                'item_code' => $p->item_code,
                'category_id' => $p->category_id,
                'category_name' => $catName,
                'price' => $price,
                'formatted_price' => 'Rs. ' . number_format($price, 0),
                'promo_tag' => $p->promo_tag ?: ($p->show_on_homepage ? 'Popular' : ''),
                'description' => $p->meta_description ?: 'Freshly prepared crispy delight marinated with secret spices and served golden & crunchy.',
                'image' => $imageUrl,
            ];
        });

        // Featured Deals (Combos or items tagged with Deals / Best Seller)
        $featuredDeals = $products->filter(function ($p) {
            $tag = strtolower($p['promo_tag']);
            $name = strtolower($p['name']);
            return str_contains($tag, 'deal') || str_contains($tag, 'best') || str_contains($tag, 'chef') || str_contains($name, 'feast') || str_contains($name, 'bucket') || str_contains($name, 'box');
        })->take(4);

        if ($featuredDeals->isEmpty()) {
            $featuredDeals = $products->take(4);
        }

        return view('website.index', [
            'restaurantName' => $restaurantName,
            'phone' => $phone,
            'address' => $address,
            'whatsappNumber' => $whatsappNumber,
            'openingHours' => $openingHours,
            'categories' => $categories,
            'products' => $products,
            'featuredDeals' => $featuredDeals,
        ]);
    }

    /**
     * API: Get Products strictly for the Restaurant Branch
     */
    public function getProductsJson(Request $request)
    {
        $restaurantBranchId = $this->getRestaurantBranchId();
        $catId = $request->get('category_id');
        $search = $request->get('search');

        $query = Product::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('branch_id', $restaurantBranchId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->with(['category_relation']);

        if (!empty($catId) && $catId !== 'all') {
            $query->where('category_id', $catId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('meta_description', 'like', "%{$search}%")
                  ->orWhere('promo_tag', 'like', "%{$search}%");
            });
        }

        $raw = $query->orderBy('id', 'desc')->get();

        $data = $raw->map(function ($p) {
            $price = (float)($p->web_sale_price > 0 
                ? $p->web_sale_price 
                : ($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : ($p->sale_price_per_box > 0 ? $p->sale_price_per_box : 0)));

            $img = $p->web_main_image ?: $p->image;
            $imageUrl = '';
            if (!empty($img)) {
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                    $imageUrl = $img;
                } else {
                    $imageUrl = asset('uploads/products/' . $img);
                }
            }
            if (empty($imageUrl)) {
                $imageUrl = 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=800&q=80';
            }

            return [
                'id' => $p->id,
                'name' => $p->item_name,
                'category_id' => $p->category_id,
                'category_name' => $p->category_relation ? $p->category_relation->name : 'Menu',
                'price' => $price,
                'formatted_price' => 'Rs. ' . number_format($price, 0),
                'promo_tag' => $p->promo_tag,
                'description' => $p->meta_description ?: 'Fresh & crispy fried chicken delicacy.',
                'image' => $imageUrl,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * API: Place Customer Order from Website
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:25',
            'order_type' => 'required|in:delivery,takeaway,dine_in',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $restaurantBranchId = $this->getRestaurantBranchId();

        try {
            DB::beginTransaction();

            $orderNumber = 'MFC-' . strtoupper(substr(uniqid(), -6));
            $orderType = $request->input('order_type', 'delivery');
            $customerName = trim($request->input('customer_name'));
            $customerPhone = trim($request->input('customer_phone'));
            $address = $orderType === 'delivery' ? trim($request->input('delivery_address', '')) : ucfirst($orderType);
            $notes = trim($request->input('order_notes', ''));
            $paymentMethod = $request->input('payment_method', 'cod');

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->input('items') as $item) {
                $p = Product::withoutGlobalScope(\App\Scopes\BranchScope::class)
                    ->where('branch_id', $restaurantBranchId)
                    ->find($item['id']);

                if ($p) {
                    $unitPrice = (float)($p->web_sale_price > 0 
                        ? $p->web_sale_price 
                        : ($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : ($p->sale_price_per_box > 0 ? $p->sale_price_per_box : 0)));
                    
                    $qty = (int)$item['quantity'];
                    $lineTotal = $unitPrice * $qty;
                    $subtotal += $lineTotal;

                    $itemsData[] = [
                        'product_id' => $p->id,
                        'product_name' => $p->item_name,
                        'price' => $unitPrice,
                        'quantity' => $qty,
                        'total' => $lineTotal,
                    ];
                }
            }

            // Free delivery on orders >= 1500, else 150 (0 for takeaway/dine-in)
            $deliveryCharges = 0;
            if ($orderType === 'delivery') {
                $deliveryCharges = $subtotal >= 1500 ? 0 : 150;
            }

            $total = $subtotal + $deliveryCharges;

            // 1. Create EcommerceOrder in DB
            $order = EcommerceOrder::create([
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'discount' => 0.00,
                'delivery_charges' => $deliveryCharges,
                'total' => $total,
                'payment_method' => $paymentMethod === 'online' ? 'Online Bank Transfer' : 'Cash on Delivery',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'order_notes' => "Type: " . strtoupper($orderType) . ($notes ? " | Note: " . $notes : ""),
                'shipping_name' => $customerName,
                'shipping_phone' => $customerPhone,
                'shipping_address' => $address,
                'shipping_city' => 'Local Delivery Area',
            ]);

            // 2. Insert items
            foreach ($itemsData as $it) {
                EcommerceOrderItem::create([
                    'ecommerce_order_id' => $order->id,
                    'product_id' => $it['product_id'],
                    'product_name' => $it['product_name'],
                    'price' => $it['price'],
                    'quantity' => $it['quantity'],
                    'total' => $it['total'],
                ]);
            }

            DB::commit();

            // 2b. Sync immediately with Central Sales, Cash & Inventory
            \App\Services\RestaurantOnlineOrderService::syncOrderToSale($order);

            // 3. Build formatted WhatsApp message text
            $restaurantPhone = DB::table('settings')->where('branch_id', $restaurantBranchId)->where('key', 'company_phone')->value('value') ?: '03001234567';
            $waCleanPhone = preg_replace('/[^0-9]/', '', $restaurantPhone);
            if (str_starts_with($waCleanPhone, '0')) {
                $waCleanPhone = '92' . substr($waCleanPhone, 1);
            }

            $waLines = [];
            $waLines[] = "*🍗 NEW ORDER - MUNCH FRIED CHICKEN*";
            $waLines[] = "--------------------------------";
            $waLines[] = "*Order ID:* #" . $orderNumber;
            $waLines[] = "*Customer:* " . $customerName;
            $waLines[] = "*Phone:* " . $customerPhone;
            $waLines[] = "*Order Type:* " . strtoupper($orderType);
            if ($orderType === 'delivery') {
                $waLines[] = "*Address:* " . $address;
            }
            if (!empty($notes)) {
                $waLines[] = "*Special Instructions:* " . $notes;
            }
            $waLines[] = "--------------------------------";
            $waLines[] = "*ITEMS ORDERED:*";
            foreach ($itemsData as $it) {
                $waLines[] = "• {$it['quantity']}x {$it['product_name']} = Rs. " . number_format($it['total']);
            }
            $waLines[] = "--------------------------------";
            $waLines[] = "*Subtotal:* Rs. " . number_format($subtotal);
            if ($orderType === 'delivery') {
                $waLines[] = "*Delivery Charges:* " . ($deliveryCharges > 0 ? "Rs. " . number_format($deliveryCharges) : "FREE");
            }
            $waLines[] = "*TOTAL BILL:* Rs. " . number_format($total);
            $waLines[] = "*Payment:* " . ($paymentMethod === 'online' ? 'Online Transfer' : 'Cash On Delivery');
            $waLines[] = "--------------------------------";
            $waLines[] = "Please confirm this order! Thank you.";

            $waText = urlencode(implode("\n", $waLines));
            $whatsappUrl = "https://wa.me/{$waCleanPhone}?text={$waText}";

            return response()->json([
                'status' => 'success',
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'delivery_charges' => $deliveryCharges,
                'total' => $total,
                'formatted_total' => 'Rs. ' . number_format($total, 0),
                'whatsapp_url' => $whatsappUrl,
                'message' => 'Your order has been received successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Track Order Status
     */
    public function trackOrderApi(Request $request)
    {
        $keyword = trim($request->input('keyword'));
        if (empty($keyword)) {
            return response()->json(['status' => 'error', 'message' => 'Please enter Order Number or Phone.'], 400);
        }

        $order = EcommerceOrder::with('items')
            ->where('order_number', $keyword)
            ->orWhere('shipping_phone', $keyword)
            ->orderBy('id', 'desc')
            ->first();

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'No active order found with this details.'], 404);
        }

        $statusText = match ($order->order_status) {
            'pending' => 'Order Received & Sent to Kitchen',
            'processing' => 'Sizzling Hot in Kitchen (Cooking)',
            'shipped' => 'Out for Hot & Fresh Delivery',
            'delivered' => 'Delivered! Enjoy your meal',
            'cancelled' => 'Cancelled',
            default => ucfirst($order->order_status),
        };

        $statusStep = match ($order->order_status) {
            'pending' => 1,
            'processing' => 2,
            'shipped' => 3,
            'delivered' => 4,
            default => 1,
        };

        return response()->json([
            'status' => 'success',
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->order_status,
                'status_text' => $statusText,
                'status_step' => $statusStep,
                'customer_name' => $order->shipping_name,
                'total' => 'Rs. ' . number_format($order->total, 0),
                'created_at' => $order->created_at->format('M d, Y - h:i A'),
                'items' => $order->items->map(function ($it) {
                    return [
                        'name' => $it->product_name,
                        'qty' => $it->quantity,
                        'total' => 'Rs. ' . number_format($it->total, 0),
                    ];
                }),
            ]
        ]);
    }

    /**
     * API: Table Booking / Reservation
     */
    public function bookTable(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:100',
            'guest_phone' => 'required|string|max:25',
            'guests' => 'required|integer|min:1|max:30',
            'date' => 'required|string',
            'time' => 'required|string',
        ]);

        $restaurantBranchId = $this->getRestaurantBranchId();
        $bookingRef = 'MFC-TBL-' . rand(100, 999);
        $name = $request->guest_name;
        $phone = $request->guest_phone;
        $guests = $request->guests;
        $date = $request->date;
        $time = $request->time;

        $restaurantPhone = DB::table('settings')->where('branch_id', $restaurantBranchId)->where('key', 'company_phone')->value('value') ?: '03001234567';
        $waCleanPhone = preg_replace('/[^0-9]/', '', $restaurantPhone);
        if (str_starts_with($waCleanPhone, '0')) {
            $waCleanPhone = '92' . substr($waCleanPhone, 1);
        }

        $waMsg = "*🍗 TABLE RESERVATION - MUNCH FRIED CHICKEN*\n"
            . "*Booking Ref:* #{$bookingRef}\n"
            . "*Guest Name:* {$name}\n"
            . "*Phone:* {$phone}\n"
            . "*Guests:* {$guests} Persons\n"
            . "*Date & Time:* {$date} at {$time}\n"
            . "Please confirm my table reservation. Thank you!";

        $waUrl = "https://wa.me/{$waCleanPhone}?text=" . urlencode($waMsg);

        return response()->json([
            'status' => 'success',
            'booking_ref' => $bookingRef,
            'whatsapp_url' => $waUrl,
            'message' => 'Table reservation request received! We look forward to hosting you.',
        ]);
    }
}
