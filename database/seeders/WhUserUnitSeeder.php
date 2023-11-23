<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\WhUserUnit;

class WhUserUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            ["uid" => "U0", "title" => "Rektorat"],
            ["uid" => "U1", "title" => "Dosen"],
            ["uid" => "U2", "title" => "Staf"],
            ["uid" => "U3", "title" => "Cleaning Service"],
            ["uid" => "U4", "title" => "Security"]
        ];

        foreach ($data as $x) {
            if(!WhUserUnit::where('uid', $x['uid'])->first()){
                $m = new WhUserUnit();
                $m->uid = $x['uid'];
                $m->title = $x['title'];
                $m->save();
            }
        }
    }
}
