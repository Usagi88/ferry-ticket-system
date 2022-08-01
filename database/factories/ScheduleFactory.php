<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\Vessel;
use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Schedule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $number = 1;
        $start = $this->faker->dateTimeBetween('next Monday', 'next Monday +7 days')->format('Y-m-d H:i:s');
        return [
            'vessel_id' => Vessel::all()->random()->id,
            'user_id' => User::all()->random()->id,
            'title' => "none",
            'route_id' => $number++,
            'start' => $start,
            'end' => $this->faker->dateTimeBetween($start, $start.'+2 days', $timezone = null),
            'available_seats' => 0,
        ];
    }
}
