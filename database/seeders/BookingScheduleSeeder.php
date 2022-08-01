<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Schedule;

use Illuminate\Database\Seeder;

class BookingScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 20; $i++) {
            DB::table('booking_schedule')->insert([
                'booking_id' => Booking::get()->random()->id,
                'schedule_id' => Schedule::get()->random()->id,
            ]);
        } 
    }
}
