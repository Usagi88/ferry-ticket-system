<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_users')->insert([
            'user_id' => 1,
            'role_id' => 1
        ]);
        DB::table('role_users')->insert([
            'user_id' => 2,
            'role_id' => 2
        ]);
        DB::table('role_users')->insert([
            'user_id' => 3,
            'role_id' => 3
        ]);
        DB::table('role_users')->insert([
            'user_id' => 4,
            'role_id' => 4
        ]);
        $userID = range(5, 14);
        shuffle($userID);
        for($i = 0; $i<10; $i++){
            
            DB::table('role_users')->insert(
                [
                    'user_id' => $userID[$i],
                    'role_id' => rand(3,4),
                ]
            );
        }
        
    }
}
