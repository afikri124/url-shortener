<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        if(!User::where('username', 'admin')->first()){
            User::create([
                'name' => 'admin',
                'username' => 'admin',
                'email' => 'no-reply@jgu.ac.id',
                'password' => bcrypt('adminadmin'),
            ]);
        }
    }
}
