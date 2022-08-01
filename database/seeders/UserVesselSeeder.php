<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vessel;

class UserVesselSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for ($i = 0; $i <= 10; $i++) {
            $vessel = Vessel::get()->random();
            DB::table('user_vessel')->insert([
                'user_id' => User::whereHas('roles', function($q){
                    $q->where('name', 'Agent');
                })->get()->random()->id,
                'owner_id' => $vessel->owner_id,
                'vessel_id' => $vessel->id,
            ]);
        } 
        
    }
}
