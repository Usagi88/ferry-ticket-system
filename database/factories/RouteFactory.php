<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\User;
use App\Models\Island;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Route::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $randomValue = rand(1,4);
        $listOfOrigins = Island::all()->random($randomValue);
        $listOfDestinations = Island::all()->random($randomValue);
        $userOne = User::get()->random();
        
        //Combining atoll and island name
        foreach ($listOfOrigins as $key => $island) {
            $originIslandNames[] = $island->atoll.".".$island->name;
        }
        foreach ($listOfDestinations as $key => $island) {
            $destinationIslandNames[] = $island->atoll.".".$island->name;
        }
        /**
         * Looping till amount of origin (same as destination amount)
         * Getting user's random custom ticket type's name
         * Adding the random amount of custom tickets into another array so that we'll know these tickets belong to this origin/destination
         * Getting random date for each origin/destination
         * Getting random values for adult,child,infant ticket
         * Adding the price tickets to an array, so each origin/destination will have it's own price
         */
        $userCustomTicketCount = $userOne->ticket_types->count();
        for ($x=0; $x < count($listOfOrigins); $x++) {
            $customTicketArr = [];
            $randomValue2 = rand(1,3); 
            $counter = 0;
            for ($z=0; $z < $randomValue2; $z++) { 
                if($counter < $userCustomTicketCount){
                    $customTicket = rand(12,50);
                    $customTicketArr[$userOne->ticket_types[$counter]->name] = $customTicket;
                }   
                $counter = $counter + 1;
            }
            $listOfCustomTicket[] = $customTicketArr;
            $listOfDepartureTime[] = $this->faker->time($format = 'H:i');
            $adult = rand(10,50);
            $child = rand(8,50);
            $infant = rand(5,50);
            $priceArr[] = ['Adult'=> $adult, 'Child'=> $child, 'Infant'=> $infant];
        }
        /**
         * Adding them to data array very easily now that values belong in an array.
         * key is the index.
         * For example, 3 origin/destination. And the first origin's value will be in key 0. In other words, index 0.
         */
        foreach ($listOfOrigins as $key => $value) {
            $data[] = [
                'Origin' => $originIslandNames[$key],
                'Destination' => $destinationIslandNames[$key],
                'Departure_time' => $listOfDepartureTime[$key],
                'Price_list' => $priceArr[$key],
                'Custom_ticket'=> $listOfCustomTicket[$key],
            ];
            
        }
        //Finally, returning them with a random route name & user id. Also, the data that we made above.
        return [
            'route_name'=> $this->faker->unique()->lexify('Route ???'),
            'user_id' => $userOne->id,
            'data' => $data
        ];
    }
}
