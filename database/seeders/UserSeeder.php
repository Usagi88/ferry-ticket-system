<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'username' => 'testAdmin',
            'first_name' => 'testAdminFirstName',
            'last_name' => 'testAdminLastName',
            'email' => 'admin@mail.com',
            'password' => \bcrypt('test'),
        ]);

        User::factory()->create([
            'username' => 'testStaff',
            'first_name' => 'testStaffFirstName',
            'last_name' => 'testStaffLastName',
            'email' => 'staff@mail.com',
            'password' => \bcrypt('test'),
        ]);

        User::factory()->create([
            'username' => 'testMerchant',
            'first_name' => 'testMerchantFirstName',
            'last_name' => 'testMerchantLastName',
            'email' => 'merchant@mail.com',
            'password' => \bcrypt('test'),
        ]);

        User::factory()->create([
            'username' => 'testAgent',
            'first_name' => 'testAgentFirstName',
            'last_name' => 'testAgentLastName',
            'email' => 'agent@mail.com',
            'password' => \bcrypt('test'),
        ]);

        User::factory()->count(20)->create();

        $users = User::get();
        foreach($users as $user){
            DB::table('profiles')->insert([
                'user_id' => $user->id,
                'title' => $user->username,
                'description' => "No Description"
            ]);
        }
        
    }
}
