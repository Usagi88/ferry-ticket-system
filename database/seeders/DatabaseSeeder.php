<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory(10)->create();

        
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            RoleUserSeeder::class,
            VesselTypeSeeder::class,
            VesselSeeder::class,
            TicketTypeSeeder::class,
            BookingStatusSeeder::class,
            IslandSeeder::class,
            RouteSeeder::class,
            RouteTicketTypeSeeder::class,
            ScheduleSeeder::class,
            BookingSeeder::class,
            PermissionSeeder::class,
            PermissionRoleSeeder::class,
            PermissionUserSeeder::class,
            UserVesselSeeder::class,
            AgentIslandSeeder::class,
            //BookingScheduleSeeder::class,//using the code inside bookingseeder
            //RouteScheduleSeeder::class,//using the code inside bookingseeder
            //RouteTicketTypeSeeder::class,//using the code inside bookingseeder
        ]);

        

        //\App\Models\Bookmark::factory(10)->create([
        //    'is_active' => 1,
        //]);
    }
}
