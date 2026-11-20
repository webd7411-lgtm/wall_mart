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

        // 1. Load All Website Settings (with defaults)
        $rawWebSettings = DB::table('settings')->where('group', 'website')->pluck('value', 'key')->toArray();

        $defaults = [
            'site_name' => 'Munch Fried Chicken',
            'site_tagline' => 'Handcrafted • Secret 12-Spice Blend',
            'site_logo' => '',
            'top_announcement' => 'FREE DELIVERY on all orders above Rs. 1,500!',
            'opening_hours' => '12:00 PM – 02:00 AM Daily',
            'contact_phone' => '0300-1234567',
            'whatsapp_number' => '923001234567',
            'contact_address' => 'Main Commercial Boulevard, Food Street, Hyderabad',
            'delivery_charges' => '150',
            'free_delivery_above' => '1500',
            'hero_badge' => 'Handcrafted • Secret 12-Spice Blend',
            'hero_title_line1' => 'CRUNCHY.',
            'hero_title_gold' => 'JUICY.',
            'hero_title_red' => 'UNFORGETTABLE.',
            'hero_lead' => 'Indulge in 100% fresh, farm-raised Halal chicken hand-breaded in our proprietary golden recipe. Fried to sizzling crunch outside, bursting with tender flavors inside.',
            'hero_btn1_text' => 'Order Online Now',
            'hero_btn2_text' => 'WhatsApp Order',
            'hero_image' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=85',
            'hero_deal_emoji' => '🔥',
            'hero_deal_tag' => 'Mega Deal',
            'hero_deal_title' => 'Family Bucket Feast',
            'hero_deal_price' => 'Starting from Rs. 1,050',
            'hero_stat1_val' => '4.9 ★',
            'hero_stat1_text' => '2,800+ Foodies',
            'hero_stat2_val' => '30 Min',
            'hero_stat2_text' => 'Fast Hot Delivery',
            'hero_stat3_val' => '100%',
            'hero_stat3_text' => 'Halal & Fresh Daily',
            'deals_tag' => "Chef's Signature Specials",
            'deals_title' => "Today's Hot Deals & Combos",
            'deals_subtitle' => "Specially curated value buckets and mouthwatering combos designed for maximum crunch and big savings.",
            'menu_tag' => "Fresh From The Kitchen",
            'menu_title' => "Explore Our Full Menu",
            'menu_subtitle' => "Every piece is fresh, marinated with authentic seasonings, and fried to golden perfection on order.",
            'why_tag' => 'Quality In Every Bite',
            'why_title' => 'Why Foodies Love Munch',
            'why_subtitle' => 'We never compromise on freshness, hygiene, or authentic crunch. Here is what makes us extraordinary.',
            'why_icon_1' => '🍗',
            'why_title_1' => '100% Fresh Halal',
            'why_desc_1' => 'Never frozen chicken sourced daily from certified farms. Tender, juicy, and naturally delicious.',
            'why_icon_2' => '🌶️',
            'why_title_2' => 'Secret 12-Spice Blend',
            'why_desc_2' => 'A proprietary master mix of aromatic spices marinated for 12 hours for deep, explosive flavor.',
            'why_icon_3' => '⚡',
            'why_title_3' => 'Super Fast Delivery',
            'why_desc_3' => 'Piping hot food delivered straight to your doorstep in thermal heat-sealed boxes within 30-40 mins.',
            'why_icon_4' => '🧼',
            'why_title_4' => 'Ultra Clean Kitchens',
            'why_desc_4' => 'Prepared under stringent food safety and hygienic protocols in modern commercial deep-fryers.',
            'reviews_tag' => 'Real Foodie Experiences',
            'reviews_title' => 'Loved by Thousands',
            'reviews_subtitle' => 'Discover what our happy diners and online orderers say about Munch Fried Chicken.',
            'rev_name_1' => 'Ali Zafar',
            'rev_role_1' => 'Verified Foodie • 15+ Orders',
            'rev_stars_1' => '5',
            'rev_text_1' => "Hands down the crispiest fried chicken I've tasted in town! The seasoning hits every note and the Double Crunch Tower Burger is colossal. Fast delivery too!",
            'rev_name_2' => 'Sana Naveed',
            'rev_role_2' => 'Regular Family Customer',
            'rev_stars_2' => '5',
            'rev_text_2' => "The Family Feast bucket was piping hot when it arrived! The pieces are generous, skin is insanely crunchy without feeling oily. Our kids loved the wings!",
            'rev_name_3' => 'Hamza Khan',
            'rev_role_3' => 'Food Blogger',
            'rev_stars_3' => '5',
            'rev_text_3' => "Ordered online and received instant WhatsApp updates. The loaded fries with melted cheese and chicken bites are addictive. 10/10 highly recommended!",
            'footer_about' => "Crafting Hyderabad's favorite crispy fried chicken, towering burgers, and sizzling deals. Freshly prepared, hygienically packed, and delivered blazing fast.",
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'tiktok_url' => 'https://tiktok.com',
            'google_maps_url' => '',
            'copyright_text' => '© 2026 MUNCH FRIED CHICKEN. All Rights Reserved.'
        ];

        $settings = array_merge($defaults, $rawWebSettings);

        $restaurantName = !empty($settings['site_name']) ? $settings['site_name'] : "MUNCH FRIED CHICKEN";
        $phone = !empty($settings['contact_phone']) ? $settings['contact_phone'] : "0300-1234567";
        $address = !empty($settings['contact_address']) ? $settings['contact_address'] : "Main Commercial Boulevard, Food Street";
        $whatsappNumber = !empty($settings['whatsapp_number']) ? $settings['whatsapp_number'] : "923001234567";
        $openingHours = !empty($settings['opening_hours']) ? $settings['opening_hours'] : "12:00 PM – 02:00 AM Daily";

        $branch = Branch::find($restaurantBranchId);
        if ($branch && !empty($branch->name) && empty($rawWebSettings['site_name'])) {
            $restaurantName = $branch->name;
        }

        $branchSettings = DB::table('settings')->where('branch_id', $restaurantBranchId)->pluck('value', 'key');
        if (!empty($branchSettings['company_name']) && empty($rawWebSettings['site_name'])) {
            $restaurantName = $branchSettings['company_name'];
        }
        if (!empty($branchSettings['company_phone']) && empty($rawWebSettings['contact_phone'])) {
            $phone = $branchSettings['company_phone'];
            $whatsappNumber = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($whatsappNumber, '0')) {
                $whatsappNumber = '92' . substr($whatsappNumber, 1);
            }
        }
        if (!empty($branchSettings['company_address']) && empty($rawWebSettings['contact_address'])) {
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

            // Parse Variants from color column
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
            $minPrice = $price;
            $maxPrice = $price;
            if (!empty($variants)) {
                $pricesList = [];
                foreach ($variants as $idx => $v) {
                    $sizeStr = (isset($v['size']) && $v['size'] !== '-') ? " {$v['size']}" : '';
                    $colorStr = (isset($v['color']) && $v['color'] !== '-') ? " ({$v['color']})" : '';
                    $vName = $v['name'] ?? ($p->item_name . $sizeStr . $colorStr);
                    $vPrice = (float) (!empty($v['sale_price']) ? $v['sale_price'] : $price);
                    $pricesList[] = $vPrice;

                    $variantItems[] = [
                        'id' => $p->id,
                        'variant_index' => $idx,
                        'name' => $vName,
                        'size' => $v['size'] ?? '-',
                        'color' => $v['color'] ?? '-',
                        'price' => $vPrice,
                        'formatted_price' => 'Rs. ' . number_format($vPrice, 0),
                        'variant_data' => base64_encode(json_encode($v)),
                    ];
                }
                if (!empty($pricesList)) {
                    $minPrice = min($pricesList);
                    $maxPrice = max($pricesList);
                }
            }

            $hasVariants = count($variantItems) > 0;
            $displayPrice = $hasVariants 
                ? ($minPrice === $maxPrice ? 'Rs. ' . number_format($minPrice, 0) : 'Rs. ' . number_format($minPrice, 0) . ' - ' . number_format($maxPrice, 0))
                : ('Rs. ' . number_format($price, 0));

            return [
                'id' => $p->id,
                'name' => $p->item_name,
                'item_code' => $p->item_code,
                'category_id' => $p->category_id,
                'category_name' => $catName,
                'price' => $minPrice,
                'max_price' => $maxPrice,
                'formatted_price' => $displayPrice,
                'promo_tag' => $p->promo_tag ?: ($p->show_on_homepage ? 'Popular' : ''),
                'description' => $p->meta_description ?: 'Freshly prepared crispy delight marinated with secret spices and served golden & crunchy.',
                'image' => $imageUrl,
                'has_variants' => $hasVariants,
                'variants' => $variantItems,
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
            'settings' => $settings,
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

            // Parse Variants from color column
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
            $minPrice = $price;
            $maxPrice = $price;
            if (!empty($variants)) {
                $pricesList = [];
                foreach ($variants as $idx => $v) {
                    $sizeStr = (isset($v['size']) && $v['size'] !== '-') ? " {$v['size']}" : '';
                    $colorStr = (isset($v['color']) && $v['color'] !== '-') ? " ({$v['color']})" : '';
                    $vName = $v['name'] ?? ($p->item_name . $sizeStr . $colorStr);
                    $vPrice = (float) (!empty($v['sale_price']) ? $v['sale_price'] : $price);
                    $pricesList[] = $vPrice;

                    $variantItems[] = [
                        'id' => $p->id,
                        'variant_index' => $idx,
                        'name' => $vName,
                        'size' => $v['size'] ?? '-',
                        'color' => $v['color'] ?? '-',
                        'price' => $vPrice,
                        'formatted_price' => 'Rs. ' . number_format($vPrice, 0),
                        'variant_data' => base64_encode(json_encode($v)),
                    ];
                }
                if (!empty($pricesList)) {
                    $minPrice = min($pricesList);
                    $maxPrice = max($pricesList);
                }
            }

            $hasVariants = count($variantItems) > 0;
            $displayPrice = $hasVariants 
                ? ($minPrice === $maxPrice ? 'Rs. ' . number_format($minPrice, 0) : 'Rs. ' . number_format($minPrice, 0) . ' - ' . number_format($maxPrice, 0))
                : ('Rs. ' . number_format($price, 0));

            return [
                'id' => $p->id,
                'name' => $p->item_name,
                'category_id' => $p->category_id,
                'category_name' => $p->category_relation ? $p->category_relation->name : 'Menu',
                'price' => $minPrice,
                'max_price' => $maxPrice,
                'formatted_price' => $displayPrice,
                'promo_tag' => $p->promo_tag,
                'description' => $p->meta_description ?: 'Fresh & crispy fried chicken delicacy.',
                'image' => $imageUrl,
                'has_variants' => $hasVariants,
                'variants' => $variantItems,
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
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'required|string|max:25',
            'order_type' => 'required|in:delivery,takeaway,dine_in',
            'delivery_address' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $restaurantBranchId = $this->getRestaurantBranchId();

        try {
            DB::beginTransaction();

            $orderNumber = 'MFC-' . strtoupper(substr(uniqid(), -6));
            $orderType = $request->input('order_type', 'delivery');
            $customerPhone = trim($request->input('customer_phone'));
            $rawName = trim($request->input('customer_name', ''));
            $customerName = $rawName ?: ($customerPhone ? 'Customer (' . $customerPhone . ')' : 'Customer');
            $rawAddress = trim($request->input('delivery_address', ''));
            $address = $orderType === 'delivery' ? ($rawAddress ?: 'Address not specified') : ucfirst($orderType);
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

                    $itemName = $p->item_name;
                    $size = $item['size'] ?? null;
                    $rawColor = $item['color'] ?? null;
                    $rawVariant = $item['variant_data'] ?? null;

                    // Extract size and clean color from variant_data if present
                    if (!empty($rawVariant)) {
                        $decoded = base64_decode($rawVariant, true);
                        $parsed = $decoded ? json_decode($decoded, true) : json_decode($rawVariant, true);
                        if (is_array($parsed)) {
                            if (empty($size) && !empty($parsed['size'])) {
                                $size = trim($parsed['size']);
                            }
                            if (empty($rawColor) && !empty($parsed['color']) && $parsed['color'] !== '-') {
                                $rawColor = trim($parsed['color']);
                            }
                        }
                    }

                    // Build a compact, clean representation that fits safely in database column
                    $compactColor = null;
                    if (!empty($size) || (!empty($rawColor) && $rawColor !== '-')) {
                        $compactArray = array_filter([
                            'size' => $size,
                            'color' => (!empty($rawColor) && $rawColor !== '-') ? $rawColor : null,
                        ]);
                        if (!empty($compactArray)) {
                            $compactColor = json_encode($compactArray);
                        }
                    }
                    if (!$compactColor && !empty($rawColor) && $rawColor !== '-') {
                        $compactColor = $rawColor;
                    }

                    // Guard against any length limits
                    $color = $compactColor ? \Illuminate\Support\Str::limit($compactColor, 250, '') : null;
                    $size = $size ? \Illuminate\Support\Str::limit($size, 100, '') : null;

                    // If variant details are provided in request
                    if (!empty($item['name']) && $item['name'] !== $p->item_name) {
                        $itemName = trim($item['name']);
                    }
                    if (isset($item['price']) && (float)$item['price'] > 0) {
                        $unitPrice = (float)$item['price'];
                    }

                    $qty = (int)$item['quantity'];
                    $lineTotal = $unitPrice * $qty;
                    $subtotal += $lineTotal;

                    $itemsData[] = [
                        'product_id' => $p->id,
                        'product_name' => $itemName,
                        'price' => $unitPrice,
                        'quantity' => $qty,
                        'size' => $size,
                        'color' => $color,
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

            // 2. Insert items with variant support
            foreach ($itemsData as $it) {
                EcommerceOrderItem::create([
                    'ecommerce_order_id' => $order->id,
                    'product_id' => $it['product_id'],
                    'product_name' => $it['product_name'],
                    'price' => $it['price'],
                    'quantity' => $it['quantity'],
                    'size' => $it['size'],
                    'color' => $it['color'],
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
