<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        //booking
        DB::table('permissions')->insert([
            'name' => 'Create Booking',
            'slug' => 'create-booking'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Booking',
            'slug' => 'edit-booking'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Booking',
            'slug' => 'delete-booking'
        ]);

        //island
        DB::table('permissions')->insert([
            'name' => 'Create Island',
            'slug' => 'create-island'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Island',
            'slug' => 'edit-island'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Island',
            'slug' => 'delete-island'
        ]);

        //role
        DB::table('permissions')->insert([
            'name' => 'Create Role',
            'slug' => 'create-role'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Role',
            'slug' => 'edit-role'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Role',
            'slug' => 'delete-role'
        ]);

        //route
        DB::table('permissions')->insert([
            'name' => 'Create Route',
            'slug' => 'create-route'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Route',
            'slug' => 'edit-route'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Route',
            'slug' => 'delete-route'
        ]);

        //schedule
        DB::table('permissions')->insert([
            'name' => 'Create Schedule',
            'slug' => 'create-schedule'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Schedule',
            'slug' => 'edit-schedule'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Schedule',
            'slug' => 'delete-schedule'
        ]);

        //ticket type
        DB::table('permissions')->insert([
            'name' => 'Create Ticket Type',
            'slug' => 'create-ticket-type'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Ticket Type',
            'slug' => 'edit-ticket-type'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete ticket type',
            'slug' => 'delete-ticket-type'
        ]);

        //user
        DB::table('permissions')->insert([
            'name' => 'Create User',
            'slug' => 'create-user'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit User',
            'slug' => 'edit-user'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete User',
            'slug' => 'delete-user'
        ]);

        //vessel
        DB::table('permissions')->insert([
            'name' => 'Create Vessel',
            'slug' => 'create-vessel'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Vessel',
            'slug' => 'edit-vessel'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Vessel',
            'slug' => 'delete-vessel'
        ]);

        //vessel type
        DB::table('permissions')->insert([
            'name' => 'Create Vessel Type',
            'slug' => 'create-vessel-type'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Vessel Type',
            'slug' => 'edit-vessel-type'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Vessel Type',
            'slug' => 'delete-vessel-type'
        ]);

        //profile
        DB::table('permissions')->insert([
            'name' => 'Edit Profile',
            'slug' => 'edit-profile'
        ]);

        //assign-vessel
        DB::table('permissions')->insert([
            'name' => 'Create Assign Vessel',
            'slug' => 'create-assign-vessel'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Assign Vessel',
            'slug' => 'edit-assign-vessel'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Assign Vessel',
            'slug' => 'delete-assign-vessel'
        ]);


        //agent-island
        DB::table('permissions')->insert([
            'name' => 'Create Agent Island',
            'slug' => 'create-agent-island'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Agent Island',
            'slug' => 'edit-agent-island'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Agent Island',
            'slug' => 'delete-agent-island'
        ]);
    }
}
