<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schedule::factory()->count(20)->create();
        
        $schedules = Schedule::get();
        foreach($schedules as $schedule){
            $schedule->available_seats = $schedule->vessel->seat_capacity;
            $schedule->title = $schedule->route->route_name;
            $schedule->save();
        }
    }
}
