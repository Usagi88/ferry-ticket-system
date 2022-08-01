<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('booking_statuses')->insert([
            'name' => 'Pending'
        ]);
        DB::table('booking_statuses')->insert([
            'name' => 'Paid'
        ]);
        DB::table('booking_statuses')->insert([
            'name' => 'Cancelled'
        ]);
    }
}
