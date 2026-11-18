<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WebsiteSettingsController extends Controller
{
    /**
     * Display the Complete All-in-One Website Control Page
     */
    public function index()
    {
        // Website Control is only accessible in Restaurant branch
        if (!\App\Helpers\BranchHelper::isRestaurantBranch()) {
            return redirect()->route('home')->with('error', 'Website Control sirf Restaurant branch ke liye dastyab hai.');
        }

        $rawSettings = DB::table('settings')->where('group', 'website')->pluck('value', 'key')->toArray();

        // Comprehensive defaults reflecting the live website
        $defaults = [
            // Branding & Header
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

            // Hero Section
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

            // Deals & Menu Headings
            'deals_tag' => "Chef's Signature Specials",
            'deals_title' => "Today's Hot Deals & Combos",
            'deals_subtitle' => "Specially curated value buckets and mouthwatering combos designed for maximum crunch and big savings.",
            'menu_tag' => "Fresh From The Kitchen",
            'menu_title' => "Explore Our Full Menu",
            'menu_subtitle' => "Every piece is fresh, marinated with authentic seasonings, and fried to golden perfection on order.",

            // Why Choose Us (4 Cards)
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

            // Customer Reviews (3 Cards)
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

            // Footer & Social Links
            'footer_about' => "Crafting Hyderabad's favorite crispy fried chicken, towering burgers, and sizzling deals. Freshly prepared, hygienically packed, and delivered blazing fast.",
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'tiktok_url' => 'https://tiktok.com',
            'google_maps_url' => '',
            'copyright_text' => '© 2026 MUNCH FRIED CHICKEN. All Rights Reserved.'
        ];

        // Merge DB settings with defaults
        $settings = array_merge($defaults, $rawSettings);

        return view('admin_panel.website_settings.index', compact('settings'));
    }

    /**
     * Save/Update All Website Settings & Media
     */
    public function update(Request $request)
    {
        // Website Control is only accessible in Restaurant branch
        if (!\App\Helpers\BranchHelper::isRestaurantBranch()) {
            return redirect()->route('home')->with('error', 'Website Control sirf Restaurant branch ke liye dastyab hai.');
        }

        $allInputs = $request->except(['_token', 'site_logo_file', 'hero_image_file']);

        // Handle Site Logo file upload
        if ($request->hasFile('site_logo_file')) {
            $logoFile = $request->file('site_logo_file');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('uploads/website'), $logoName);
            $allInputs['site_logo'] = asset('uploads/website/' . $logoName);
        }

        // Handle Hero Image file upload
        if ($request->hasFile('hero_image_file')) {
            $heroFile = $request->file('hero_image_file');
            $heroName = 'hero_' . time() . '.' . $heroFile->getClientOriginalExtension();
            $heroFile->move(public_path('uploads/website'), $heroName);
            $allInputs['hero_image'] = asset('uploads/website/' . $heroName);
        }

        // Upsert each setting into the settings table
        foreach ($allInputs as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key, 'group' => 'website'],
                [
                    'value' => $value ?? '',
                    'type' => 'string',
                    'label' => ucwords(str_replace('_', ' ', $key)),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            Cache::forget('website_setting_' . $key);
        }

        Cache::forget('all_website_settings');

        return redirect()->back()->with('success', 'Website settings and content updated successfully! Changes are live immediately on the website.');
    }
}
