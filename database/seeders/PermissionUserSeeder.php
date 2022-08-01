<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //admin - booking
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 1
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 2
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 3
        ]);


        //admin - island
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 4
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 5
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 6
        ]);


        //admin - role
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 7
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 8
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 9
        ]);


        //admin - route
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 10
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 11
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 12
        ]);


        //admin - schedule
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 13
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 14
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 15
        ]);


        //admin - ticket type
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 16
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 17
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 18
        ]);


        //admin - user
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 19
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 20
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 21
        ]);


        //admin - vessel
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 22
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 23
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 24
        ]);


        //admin - vessel type
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 25
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 26
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 27
        ]);


        //admin - profile
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 28
        ]);

        
        //admin - assign-vessel
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 29
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 30
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 31
        ]);


        //admin - agent-island
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 32
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 33
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 1,
            'permission_id' => 34
        ]);


        //////////////////////////////////////////////////////////////////////////////


        //staff - booking
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 1
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 2
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 3
        ]);


        //staff - route
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 10
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 11
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 12
        ]);


        //staff - schedule
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 13
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 14
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 15
        ]);


        //staff - vessel
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 22
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 23
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 24
        ]);


        //staff - assign-vessel
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 29
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 30
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 31
        ]);


        //staff - agent-island
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 32
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 33
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 2,
            'permission_id' => 34
        ]);
        

        //////////////////////////////////////////////////////////////////////

        //merchant - booking
        DB::table('permission_users')->insert([
            'user_id' => 3,
            'permission_id' => 1
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 3,
            'permission_id' => 2
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 3,
            'permission_id' => 3
        ]);


        //merchant - vessel
        DB::table('permission_users')->insert([
            'user_id' => 3,
            'permission_id' => 22
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 3,
            'permission_id' => 23
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 3,
            'permission_id' => 24
        ]);

        ///////////////////////////////////////////////////////////////////////////////////


        //agent - booking
        DB::table('permission_users')->insert([
            'user_id' => 4,
            'permission_id' => 1
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 4,
            'permission_id' => 2
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 4,
            'permission_id' => 3
        ]);


        //agent - schedule
        DB::table('permission_users')->insert([
            'user_id' => 4,
            'permission_id' => 13
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 4,
            'permission_id' => 14
        ]);
        DB::table('permission_users')->insert([
            'user_id' => 4,
            'permission_id' => 15
        ]);
    }
}
