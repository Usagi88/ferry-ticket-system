<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RouteTicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i < 21; $i++) {
            for ($x = 1; $x < 4; $x++){
                DB::table('route_ticket_type')->insert([
                    'route_id' => $i,
                    'ticket_type_id' => $x,
                    'user_id' => $i,
                    'price' => rand(5,50),
                ]);
            }
            
        } 
    }
}
