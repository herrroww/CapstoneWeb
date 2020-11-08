<?php

use Illuminate\Database\Seeder;

class OperariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('operarios')->insert([
            'nombre' => 'juan',
            'rut' => '18.396.033-k',
            'email' => 'juane@gmail.com',
            'empresa' => 'q&s',
        ]);
        
    }
}