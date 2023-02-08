<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocStatus;

class DocStatusSeeder extends Seeder
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
            ["id" => "S1", "name" => "Belum Tersedia"],
            ["id" => "S2", "name" => "Menunggu Review"],
            ["id" => "S3", "name" => "Revisi"],
            ["id" => "S4", "name" => "Tervalidasi"],
        ];

        foreach ($data as $x) {
            if(!DocStatus::where('id', $x['id'])->first()){
                $m = new DocStatus();
                $m->id = $x['id'];
                $m->name = $x['name'];
                $m->save();
            }
        }
    }
}
