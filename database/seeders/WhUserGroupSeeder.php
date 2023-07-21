<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhUserGroup;

class WhUserGroupSeeder extends Seeder
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
            ["uid" => "JR", "title" => "JGU", "desc" => "Rektorat"],
            ["uid" => "JF", "title" => "JGU", "desc" => "Purna Waktu"],
            ["uid" => "JP", "title" => "JGU", "desc" => "Paruh Waktu"],
            ["uid" => "JE", "title" => "JGU", "desc" => "Eksternal"],
            ["uid" => "E1", "title" => "MSU", "desc" => null],
            ["uid" => "E2", "title" => "PNJ", "desc" => null],
            ["uid" => "XX", "title" => "Resign", "desc" => "Keluar"],
        ];

        foreach ($data as $x) {
            if(!WhUserGroup::where('uid', $x['uid'])->first()){
                $m = new WhUserGroup();
                $m->uid = $x['uid'];
                $m->title = $x['title'];
                $m->desc = $x['desc'];
                $m->save();
            }
        }
    }
}
