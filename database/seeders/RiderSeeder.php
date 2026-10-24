<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Rider;
use Illuminate\Database\Seeder;

class RiderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantBranches = Branch::where('type', 'restaurant')->get();

        foreach ($restaurantBranches as $b) {
            if (Rider::where('branch_id', $b->id)->count() === 0) {
                Rider::create([
                    'branch_id'   => $b->id,
                    'name'        => 'Ali Raza',
                    'phone'       => '0300-1234567',
                    'cnic'        => '42101-1234567-1',
                    'vehicle_no'  => 'KHI-7845 (Honda 70)',
                    'duty_status' => 'available',
                    'is_active'   => true,
                    'notes'       => 'Morning Shift'
                ]);

                Rider::create([
                    'branch_id'   => $b->id,
                    'name'        => 'Usman Khan',
                    'phone'       => '0312-9876543',
                    'cnic'        => '42201-9876543-3',
                    'vehicle_no'  => 'KHI-3210 (CD 70)',
                    'duty_status' => 'available',
                    'is_active'   => true,
                    'notes'       => 'Evening Shift'
                ]);

                Rider::create([
                    'branch_id'   => $b->id,
                    'name'        => 'Hamza Tariq',
                    'phone'       => '0333-5554433',
                    'cnic'        => '42301-5554433-5',
                    'vehicle_no'  => 'KHI-9988 (Yamaha 125)',
                    'duty_status' => 'available',
                    'is_active'   => true,
                    'notes'       => 'Full Day Shift'
                ]);
            }
        }
    }
}
