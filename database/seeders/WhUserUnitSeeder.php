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
            ["uid" => "U1", "title" => "Dosen Tetap"],
            ["uid" => "U1a", "title" => "Dosen Slab 1-2"],
            ["uid" => "U1b", "title" => "Dosen Slab 3-4"],
            ["uid" => "U1x", "title" => "Dosen Tidak Tetap"],
            ["uid" => "U2", "title" => "Staf/Karyawan"],
            ["uid" => "U3", "title" => "Cleaning Service"],
            ["uid" => "U4", "title" => "Security"],
            ["uid" => "U5", "title" => "Laboran"]
        ];

        foreach ($data as $x) {
            if(!WhUserUnit::where('uid', $x['uid'])->first()){
                $m = new WhUserUnit();
                $m->uid = $x['uid'];
                $m->title = $x['title'];
                $m->save();
            } else if(WhUserUnit::where('uid', $x['uid'])->where('title','!=', $x['title'])->first() ){
                WhUserUnit::where('uid', $x['uid'])
                ->update([
                    'title'=> $x['title']
                ]);
            }
        }
    }
}
