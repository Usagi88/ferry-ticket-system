<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Island;

class AgentIslandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i<10; $i++){
            DB::table('agent_islands')->insert([
                'user_id' => User::whereHas('roles', function($q){
                    $q->where('name', 'Agent');
                })->get()->random()->id,
                'island_id' => Island::get()->random()->id,
            ]);
        }
        
    }
}
