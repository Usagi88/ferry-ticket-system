<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VesselType;

class VesselTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VesselType::factory()->create([
            'name' => 'Speed boat',
            'description' => 'Fast',
        ]);

        VesselType::factory()->create([
            'name' => 'Ferry',
            'description' => 'Slow',
        ]);
    }
}
