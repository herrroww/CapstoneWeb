<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'capstone',
            'userFTP' => 'capstone',
            'passFTP' => 'asdasd',
            'email' => 'capstone@gmail.com',
            'password' => bcrypt('capstone'),
        ]);        
    }
}