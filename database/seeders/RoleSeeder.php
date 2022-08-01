<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::factory()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);
        Role::factory()->create([
            'name' => 'Staff',
            'slug' => 'staff',
        ]);
        Role::factory()->create([
            'name' => 'Merchant',
            'slug' => 'merchant',
        ]);
        Role::factory()->create([
            'name' => 'Agent',
            'slug' => 'agent',
        ]);
        
    }
}
