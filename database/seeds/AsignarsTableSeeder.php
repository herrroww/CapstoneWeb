<?php

use Illuminate\Database\Seeder;

class AsignarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('asignars')->insert([
            'operario_id' => '1',
            'componente_id' => '1',
            'empresa_id' => '1',
            
        ]);

        DB::table('asignars')->insert([
            'operario_id' => '2',
            'componente_id' => '2',
            'empresa_id' => '2',
            
        ]);

        DB::table('asignars')->insert([
            'operario_id' => '3',
            'componente_id' => '3',
            'empresa_id' => '3',
            
        ]);

        DB::table('asignars')->insert([
            'operario_id' => '4',
            'componente_id' => '4',
            'empresa_id' => '4',
            
        ]);

        DB::table('asignars')->insert([
            'operario_id' => '6',
            'componente_id' => '6',
            'empresa_id' => '6',
            
        ]);

        DB::table('asignars')->insert([
            'operario_id' => '2',
            'componente_id' => '5',
            'empresa_id' => '5',
            
        ]);

        DB::table('asignars')->insert([
            'operario_id' => '3',
            'componente_id' => '5',
            'empresa_id' => '5',
            
        ]);

        DB::table('asignars')->insert([
            'operario_id' => '5',
            'componente_id' => '5',
            'empresa_id' => '5',
            
        ]);
    }

   

}
