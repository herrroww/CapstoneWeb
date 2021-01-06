<?php

use Illuminate\Database\Seeder;

class ComponentesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('componentes')->insert([
            'nombreComponente' => 'componente1',
            'idComponente' => '11111',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);

        DB::table('componentes')->insert([
            'nombreComponente' => 'componente2',
            'idComponente' => '11112',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);

        DB::table('componentes')->insert([
            'nombreComponente' => 'componente3',
            'idComponente' => '11113',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);

        DB::table('componentes')->insert([
            'nombreComponente' => 'componente4',
            'idComponente' => '11411',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);

        DB::table('componentes')->insert([
            'nombreComponente' => 'componente5',
            'idComponente' => '11511',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);

        DB::table('componentes')->insert([
            'nombreComponente' => 'componente6',
            'idComponente' => '11161',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);

        DB::table('componentes')->insert([
            'nombreComponente' => 'componente7',
            'idComponente' => '17161',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);

        DB::table('componentes')->insert([
            'nombreComponente' => 'componente8',
            'idComponente' => '11181',
            'codigoQR' => '11111',
            'codigoIdentificador' => '11111',
            'codigoNFC' => '11111',
            'linkMemoriaDeCalculo' => '11111',
            
            
        ]);
    }
   
}
