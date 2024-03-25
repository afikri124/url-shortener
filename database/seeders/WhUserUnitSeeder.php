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
            ["uid" => "U0", "title" => "Rektorat", "time_in" => "08:00:00", "time_out" => "16:00:00", "time_total" => "08:00:00"],
            ["uid" => "U1", "title" => "Dosen Tetap", "time_in" => "08:00:00", "time_out" => "16:00:00", "time_total" => "08:00:00"],
            ["uid" => "U1a", "title" => "Dosen Slab 1-2", "time_in" => "08:00:00", "time_out" => "16:00:00", "time_total" => "08:00:00"],
            ["uid" => "U1b", "title" => "Dosen Slab 3-4", "time_in" => "08:00:00", "time_out" => "16:00:00", "time_total" => "08:00:00"],
            ["uid" => "U1x", "title" => "Dosen Tidak Tetap", "time_in" => "08:00:00", "time_out" => "16:00:00", "time_total" => "08:00:00"],
            ["uid" => "U2", "title" => "Staf/Karyawan", "time_in" => "08:00:00", "time_out" => "16:00:00", "time_total" => "08:00:00"],
            ["uid" => "U3", "title" => "Cleaning Service", "time_in" => "07:00:00", "time_out" => "17:00:00", "time_total" => "10:00:00"],
            ["uid" => "U4", "title" => "Security", "time_in" => "08:00:00", "time_out" => "20:00:00", "time_total" => "12:00:00"],
            ["uid" => "U5", "title" => "Laboran", "time_in" => "08:00:00", "time_out" => "16:00:00", "time_total" => "08:00:00"]
        ];

        foreach ($data as $x) {
            if(!WhUserUnit::where('uid', $x['uid'])->first()){
                $m = new WhUserUnit();
                $m->uid = $x['uid'];
                $m->title = $x['title'];
                $m->time_in = $x['time_in'];
                $m->time_out = $x['time_out'];
                $m->time_total = $x['time_total'];
                $m->save();
            } else if(WhUserUnit::where('uid', $x['uid'])->where('title','!=', $x['title'])->first() ){
                WhUserUnit::where('uid', $x['uid'])
                ->update([
                    'title'=> $x['title']
                ]);
            } else if(WhUserUnit::where('uid', $x['uid'])->where('time_in','!=', $x['time_in'])->first() ){
                WhUserUnit::where('uid', $x['uid'])
                ->update([
                    'time_in'=> $x['time_in'],
                    'time_out'=> $x['time_out'],
                    'time_total'=> $x['time_total'],
                ]);
            }
        }
    }
}
