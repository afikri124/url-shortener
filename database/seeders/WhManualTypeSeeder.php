<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhManualType;

class WhManualTypeSeeder extends Seeder
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
            ["id" => 1, "title" => "Dinas/Tugas Di luar", "absent" => false],
            ["id" => 2, "title" => "Sakit", "absent" => false],
            ["id" => 3, "title" => "Izin Cuti Tahunan", "absent" => false],
            ["id" => 4, "title" => "Izin Cuti tidak dibayar", "absent" => true],
        ];

        foreach ($data as $x) {
            if(!WhManualType::where('id', $x['id'])->first()){
                $m = new WhManualType();
                $m->id = $x['id'];
                $m->title = $x['title'];
                $m->absent = $x['absent'];
                $m->save();
            }
        }
    }
}
