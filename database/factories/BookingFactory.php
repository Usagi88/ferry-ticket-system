<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vessel;
use App\Models\Schedule;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::all()->random()->id,
            'vessel_id' => Vessel::all()->random()->id,
            'schedule_id' => Schedule::all()->random()->id,
            'ticket_type_id' => TicketType::all()->random()->id,
            'ticket_quantity' => $this->faker->numberBetween($min = 1, $max = 6),
            'total' => 0,
        ];
    }
}
