<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Str;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TicketType::factory()->create([
            'name' => 'Adult',
            'description' => '18+'
        ]);
        TicketType::factory()->create([
            'name' => 'Child',
            'description' => '6-17'
        ]);
        TicketType::factory()->create([
            'name' => 'Infant',
            'description' => '0-5'
        ]);

        /**
         * Instead of making a seeder, I'm making it inside ticket type seeder
         * This is for making user's custom tickets
         */
        $users = User::all();
        foreach ($users as $user) {
            $randomValue = rand(1,3);
            for ($i=0; $i < $randomValue; $i++) { 
                $ticketType = new TicketType;
                $randomString = Str::random(5);
                $randomString2 = Str::random(8);
                $ticketType->name = "CustomTicket ".$randomString;
                $ticketType->description = $randomString2;
                $ticketType->save();
                $ticketType->users()->attach($user->id);
                $ticketType->save();
            }
        }
    }
}
