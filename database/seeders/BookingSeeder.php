<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Route;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Booking::factory()->count(20)->create();
        // for ($i = 1; $i < 21; $i++) {
        //     DB::table('route_schedule')->insert([
        //         'route_id' => $i,
        //         'schedule_id' => $i,
        //     ]);
        // }  
        // for ($i = 1; $i < 21; $i++) {
        //     DB::table('booking_schedule')->insert([
        //         'booking_id' => $i,
        //         'schedule_id' => $i,
        //     ]);
        // }
        
        //$bookings = Booking::all(); 

        $bookings = Booking::with('vessel','schedule','ticket_type','booking_status','schedule.route.allTicketTypeOfRoute')->get();
        foreach ($bookings as $booking){
            if (!$booking->schedule->route->allTicketTypeOfRoute->isEmpty() && $booking->schedule->route->allTicketTypeOfRoute != null ){
                foreach($booking->schedule->route->allTicketTypeOfRoute->where('id',$booking->ticket_type_id) as $route){
                    $booking->total = $booking->ticket_quantity * $route->pivot->price;
                    $booking->save();
                }
                
            }
        }
        
    }
}
