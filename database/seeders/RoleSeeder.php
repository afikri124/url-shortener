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
        //
        $data = [
            ["id" => "AD", "title" => "Admin"],
            ["id" => "ST", "title" => "Staff"],
            ["id" => "SD", "title" => "Student"],
            ["id" => "GS", "title" => "Guest"],
            ["id" => "HR", "title" => "HR & Finance"],
            ["id" => "DS", "title" => "Doc System"],
        ];

        foreach ($data as $x) {
            if(!Role::where('id', $x['id'])->first()){
                $m = new Role();
                $m->id = $x['id'];
                $m->title = $x['title'];
                $m->save();
            }
        }
    }
}
