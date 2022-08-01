<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Route;
use App\Models\Schedule;

class RouteScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 20; $i++) {
            DB::table('route_schedule')->insert([
                'route_id' => Route::get()->random()->id,
                'schedule_id' => Schedule::get()->random()->id,
            ]);
        } 
    }
}
