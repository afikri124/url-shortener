<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
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
            ["user_id" => "1", "role_id" => "AD"],
            ["user_id" => "1", "role_id" => "ST"],
            ["user_id" => "1", "role_id" => "SD"],
        ];

        foreach ($data as $x) {
            if(!UserRole::where('user_id', $x['user_id'])
            ->where('role_id', $x['role_id'])->first()){
                $m = new UserRole();
                $m->user_id = $x['user_id'];
                $m->role_id = $x['role_id'];
                $m->save();
            }
        }
    }
}
