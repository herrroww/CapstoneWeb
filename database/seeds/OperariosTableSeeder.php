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
            'nombreOperario' => 'Juan Carlos',
            'rutOperario' => '12.122.111-5',
            'correoOperario' => 'prueba@gmail.com',
            'tipoOperario' => 'Externo',
            'contraseniaOperario' => 'capstone',
            'contraseniaOperario2' => 'capstone',
            'contraseniaOperarioFTP' => 'capstone',
            'telefonoOperario' => '+56940444242',
            'empresa_id'=>'1',
        ]);

        DB::table('operarios')->insert([
            'nombreOperario' => 'Pedro Carlos',
            'rutOperario' => '12.122.111-1',
            'correoOperario' => 'prueba4@gmail.com',
            'tipoOperario' => 'Externo',
            'contraseniaOperario' => 'capstone',
            'contraseniaOperario2' => 'capstone',
            'contraseniaOperarioFTP' => 'capstone',
            'telefonoOperario' => '+56940444242',
            'empresa_id'=>'2',
        ]);

        DB::table('operarios')->insert([
            'nombreOperario' => 'Tony Stark',
            'rutOperario' => '12.122.111-2',
            'correoOperario' => 'prueba1@gmail.com',
            'tipoOperario' => 'Externo',
            'contraseniaOperario' => 'capstone',
            'contraseniaOperario2' => 'capstone',
            'contraseniaOperarioFTP' => 'capstone',
            'telefonoOperario' => '+56940441242',
            'empresa_id'=>'3',
        ]);

        DB::table('operarios')->insert([
            'nombreOperario' => 'Roberto Gomez',
            'rutOperario' => '12.122.111-9',
            'correoOperario' => 'prueba2@gmail.com',
            'tipoOperario' => 'Interno',
            'contraseniaOperario' => 'capstone',
            'contraseniaOperario2' => 'capstone',
            'contraseniaOperarioFTP' => 'capstone',
            'telefonoOperario' => '+56940442242',
            'empresa_id'=>'4',
        ]);

        DB::table('operarios')->insert([
            'nombreOperario' => 'Jorge Pizarro',
            'rutOperario' => '12.122.113-2',
            'correoOperario' => 'prueba5@gmail.com',
            'tipoOperario' => 'Interno',
            'contraseniaOperario' => 'capstone',
            'contraseniaOperario2' => 'capstone',
            'contraseniaOperarioFTP' => 'capstone',
            'telefonoOperario' => '+56940442242',
            'empresa_id'=>'4',
        ]);

        DB::table('operarios')->insert([
            'nombreOperario' => 'Martin Osorio',
            'rutOperario' => '12.122.411-9',
            'correoOperario' => 'prueba6@gmail.com',
            'tipoOperario' => 'Interno',
            'contraseniaOperario' => 'capstone',
            'contraseniaOperario2' => 'capstone',
            'contraseniaOperarioFTP' => 'capstone',
            'telefonoOperario' => '+56940442242',
            'empresa_id'=>'4',
        ]);

        DB::table('operarios')->insert([
            'nombreOperario' => 'Javier Zuleta',
            'rutOperario' => '12.632.111-9',
            'correoOperario' => 'prueba7@gmail.com',
            'tipoOperario' => 'Interno',
            'contraseniaOperario' => 'capstone',
            'contraseniaOperario2' => 'capstone',
            'contraseniaOperarioFTP' => 'capstone',
            'telefonoOperario' => '+56940442242',
            'empresa_id'=>'4',
        ]);
    }
}


