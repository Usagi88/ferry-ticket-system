<?php

namespace Database\Factories;

use App\Models\Vessel;
use App\Models\User;
use App\Models\VesselType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VesselFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vessel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=> $this->faker->unique()->lexify('Vessel ???'),
            'seat_capacity' => $this->faker->numberBetween($min = 6, $max = 30),
            'max_accompanied_cargo' => $this->faker->numberBetween($min = 30, $max = 100),
            'max_unaccompanied_cargo' => $this->faker->numberBetween($min = 50, $max = 200),
            'vessel_type_id' => VesselType::all()->random()->id,
            'owner_id' => User::whereHas('roles', function($q){
                $q->where('name', 'Merchant');
            })->get()->random()->id,
        ];
    }
}
