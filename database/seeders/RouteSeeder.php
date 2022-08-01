<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        Route::factory()->count(20)->create();
        //$routes = Route::all();
        //foreach($routes as $route) {
        //    $route->route_code = 'U'.$route->user_id . '_' . $route->origin . '_' . $route->destination;
        //    $route->save();
        //}
        
        // foreach($routes as $route) {
        //     $newRoute = new Route;
        //     $newRoute->origin = $route->origin;
        //     $newRoute->destination = $route->destination;
        //     $newRoute->route_code = $route->route_code;
        //     $newRoute->duration = $route->duration;
        //     $newRoute->ticket_type_id = "2";
        //     $newRoute->user_id = $route->user_id;
        //     $newRoute->price = rand(5,40);
        //     $newRoute->save();
        // }
        // foreach($routes as $route) {
        //     $newRoute = new Route;
        //     $newRoute->origin = $route->origin;
        //     $newRoute->destination = $route->destination;
        //     $newRoute->route_code = $route->route_code;
        //     $newRoute->duration = $route->duration;
        //     $newRoute->ticket_type_id = "3";
        //     $newRoute->user_id = $route->user_id;
        //     $newRoute->price = rand(6,50);
        //     $newRoute->save();
        // }
    }
}
