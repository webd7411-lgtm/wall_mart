<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Database\Seeder;

class CategoryProductsSeeder extends Seeder
{
    public function run(): void
    {
        $unit = Unit::firstOrCreate(['name' => 'Piece']);
        $brand = Brand::firstOrCreate(['name' => 'TrendHub Pro']);
        $warehouse = Warehouse::first() ?: Warehouse::create([
            'name' => 'Main Warehouse',
            'address' => 'Default Address',
            'phone' => '1234567890'
        ]);

        // Truncate existing seed products if needed
        Product::whereIn('item_code', ['ELEC-001', 'ELEC-002', 'ELEC-003', 'MACH-001', 'MACH-002', 'MACH-003', 'TOOL-001', 'TOOL-002', 'TOOL-003'])->delete();

        $productsData = [
            // Category 1: Electronics
            [
                'category_id' => 1,
                'name' => 'Smart Digital Multimeter 1000V',
                'code' => 'ELEC-001',
                'sale_price' => 4990,
                'web_price' => 4490,
                'promo_tag' => 'New Arrival',
                'image' => '1790374749.jpg',
                'desc' => 'High precision digital multimeter with auto-ranging LCD display, NCV voltage detector, and durable rubber holster.'
            ],
            [
                'category_id' => 1,
                'name' => 'Industrial Infrared Laser Thermometer',
                'code' => 'ELEC-002',
                'sale_price' => 6990,
                'web_price' => 5990,
                'promo_tag' => 'Best Seller',
                'image' => 'outfitter_prod_1.jpg',
                'desc' => 'Contactless infrared laser temperature gun designed for industrial electrical maintenance and HVAC diagnostics.'
            ],
            [
                'category_id' => 1,
                'name' => 'Automatic Voltage Regulator AVR 2000VA',
                'code' => 'ELEC-003',
                'sale_price' => 12990,
                'web_price' => 11490,
                'promo_tag' => 'Trending',
                'image' => 'outfitter_prod_2.jpg',
                'desc' => 'Heavy-duty automatic voltage stabilizer for protecting sensitive electronic equipment against voltage fluctuations.'
            ],
            // Category 2: machine
            [
                'category_id' => 2,
                'name' => 'Heavy Duty Bench Drill Press 550W',
                'code' => 'MACH-001',
                'sale_price' => 24500,
                'web_price' => 22990,
                'promo_tag' => 'New Arrival',
                'image' => 'outfitter_prod_3.jpg',
                'desc' => 'Precision bench drill press machine engineered for heavy workshop metal drilling and woodworking.'
            ],
            [
                'category_id' => 2,
                'name' => 'Portable Silent Air Compressor 50L',
                'code' => 'MACH-002',
                'sale_price' => 38900,
                'web_price' => 35990,
                'promo_tag' => 'Trending',
                'image' => 'outfitter_prod_4.jpg',
                'desc' => 'Oil-free ultra quiet air compressor featuring dual pressure gauges and rapid quick-connect couplings.'
            ],
            [
                'category_id' => 2,
                'name' => 'Industrial Angle Grinder Machine 1400W',
                'code' => 'MACH-003',
                'sale_price' => 14800,
                'web_price' => 13490,
                'promo_tag' => 'Flash Sale',
                'image' => 'outfitter_prod_5.jpg',
                'desc' => 'High performance electric angle grinder with vibration control handle and tool-free wheel guard adjustment.'
            ],
            // Category 3: Tools
            [
                'category_id' => 3,
                'name' => 'Professional 108-Piece Socket & Tool Set',
                'code' => 'TOOL-001',
                'sale_price' => 9990,
                'web_price' => 8490,
                'promo_tag' => 'Flash Sale',
                'image' => 'outfitter_prod_6.jpg',
                'desc' => 'Chrome vanadium steel socket wrench and ratcheting tool kit housed in a heavy-duty blow-molded case.'
            ],
            [
                'category_id' => 3,
                'name' => '20V Cordless Lithium Brushless Drill Driver',
                'code' => 'TOOL-002',
                'sale_price' => 11490,
                'web_price' => 9990,
                'promo_tag' => 'Best Seller',
                'image' => 'outfitter_prod_7.jpg',
                'desc' => 'High torque 20V brushless cordless power drill with dual speed gear selection and LED work light.'
            ],
            [
                'category_id' => 3,
                'name' => 'Heavy Duty Adjustable Torque Wrench Set',
                'code' => 'TOOL-003',
                'sale_price' => 7800,
                'web_price' => 6990,
                'promo_tag' => 'New Arrival',
                'image' => 'outfitter_prod_8.jpg',
                'desc' => 'Micrometer-adjustable click torque wrench crafted from hardened alloy steel with high accuracy calibration.'
            ]
        ];

        foreach ($productsData as $data) {
            $product = Product::create([
                'creater_id' => 1,
                'category_id' => $data['category_id'],
                'brand_id' => $brand->id,
                'unit_id' => $unit->id,
                'is_part' => 0,
                'is_assembled' => 0,
                'is_active' => 1,
                'is_web_visible' => 1,
                'show_on_homepage' => 1,
                'promo_tag' => $data['promo_tag'],
                'item_code' => $data['code'],
                'item_name' => $data['name'],
                'size_mode' => 'by_pieces',
                'pieces_per_box' => 1,
                'pieces_per_m2' => 0,
                'total_m2' => 0,
                'price_per_m2' => 0,
                'sale_price_per_box' => $data['sale_price'],
                'sale_price_per_piece' => $data['sale_price'],
                'purchase_price_per_piece' => $data['sale_price'] * 0.7,
                'purchase_price_per_box' => $data['sale_price'] * 0.7,
                'web_sale_price' => $data['web_price'],
                'auto_hide_out_of_stock' => 0,
                'meta_title' => $data['name'],
                'meta_description' => $data['desc'],
                'image' => $data['image'],
                'web_main_image' => $data['image'],
                'color' => json_encode([[
                    'name' => $data['name'] . ' - Standard',
                    'size' => 'Standard',
                    'color' => 'Default',
                    'stock' => 50,
                    'sale_price' => $data['web_price'],
                    'purch_price' => $data['sale_price'] * 0.7,
                    'barcode' => rand(100000000000, 999999999999),
                    'unit' => 'Pcs',
                    'is_base_variant' => 1
                ]]),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            WarehouseStock::create([
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'quantity' => 50,
                'total_pieces' => 50,
                'remarks' => 'Category product stock'
            ]);
        }
    }
}
