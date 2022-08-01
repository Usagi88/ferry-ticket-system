<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IslandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$url = "https://dhivehi.mv/api/island/?lang=en";
       // $html = file_get_html($url);
        //$data = json_decode($html);

        $json = file_get_contents('https://dhivehi.mv/api/island/?lang=en');
        $data = json_decode($json);
        foreach ($data->data as $item) {
            DB::table('islands')->insert([
                'atoll' => $item->atoll,
                'name' => $item->island,
            ]);

        }

        
    }
}
