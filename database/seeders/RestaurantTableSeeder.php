<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantBranches = Branch::where('type', 'restaurant')->get();

        foreach ($restaurantBranches as $b) {
            if (RestaurantTable::where('branch_id', $b->id)->count() === 0) {
                for ($i = 1; $i <= 12; $i++) {
                    $floor = $i <= 6 ? 'Ground Floor' : '1st Floor';
                    RestaurantTable::create([
                        'branch_id'  => $b->id,
                        'table_no'   => 'T-' . $i,
                        'floor_area' => $floor,
                        'capacity'   => ($i % 2 === 0) ? 4 : 2,
                        'status'     => 'available',
                        'is_active'  => true,
                        'sort_order' => $i,
                    ]);
                }
            }
        }
    }
}
