<?php

use Illuminate\Database\Seeder;

class ReporteProblemasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reporteproblemas')->insert([
            'rutOperario' => '19.232.211-1',
            'nombreOperario' => 'Martin Osorio',
            'correoOperario' => 'prueba6@gmail.com',
            'numeroOperario' => '+324232222',
            'prioridad' => 'alta',
            'estado' => 'Pendiente',
            'fechaReporteProblema' => '15-01-1995',
            'tituloReporteProblema' => 'Problema1',
            'contenidoReporteProblema' => 'Problemas',
            
            
        ]);

        DB::table('reporteproblemas')->insert([
            'rutOperario' => '19.212.211-1',
            'nombreOperario' => 'Jorge Pizarro',
            'correoOperario' => 'prueba3@gmail.com',
            'numeroOperario' => '+324232292',
            'prioridad' => 'alta',
            'estado' => 'Pendiente',
            'fechaReporteProblema' => '15-01-2021',
            'tituloReporteProblema' => 'Problema2',
            'contenidoReporteProblema' => 'Problemas',
            
            
        ]);

        DB::table('reporteproblemas')->insert([
            'rutOperario' => '13.512.211-1',
            'nombreOperario' => 'Pedro Perez',
            'correoOperario' => 'prueba4@gmail.com',
            'numeroOperario' => '+324232422',
            'prioridad' => 'alta',
            'estado' => 'Pendiente',
            'fechaReporteProblema' => '15-01-2021',
            'tituloReporteProblema' => 'Problema4',
            'contenidoReporteProblema' => 'Problemas',
            
            
        ]);

        DB::table('reporteproblemas')->insert([
            'rutOperario' => '19.515.211-1',
            'nombreOperario' => 'Lucas Barrios',
            'correoOperario' => 'prueba5@gmail.com',
            'numeroOperario' => '+324532222',
            'prioridad' => 'alta',
            'estado' => 'Pendiente',
            'fechaReporteProblema' => '15-01-2021',
            'tituloReporteProblema' => 'Problema5',
            'contenidoReporteProblema' => 'Problemas',
            
            
        ]);

        DB::table('reporteproblemas')->insert([
            'rutOperario' => '19.512.611-1',
            'nombreOperario' => 'Pedro Perez',
            'correoOperario' => 'prueba6@gmail.com',
            'numeroOperario' => '+324262222',
            'prioridad' => 'alta',
            'estado' => 'Pendiente',
            'fechaReporteProblema' => '16-01-2021',
            'tituloReporteProblema' => 'Problema6',
            'contenidoReporteProblema' => 'Problemas',
            
            
        ]);

        DB::table('reporteproblemas')->insert([
            'rutOperario' => '19.512.217-1',
            'nombreOperario' => 'Tony Stark',
            'correoOperario' => 'prueba7@gmail.com',
            'numeroOperario' => '+324232222',
            'prioridad' => 'alta',
            'estado' => 'Pendiente',
            'fechaReporteProblema' => '17-01-2021',
            'tituloReporteProblema' => 'Problema7',
            'contenidoReporteProblema' => 'Problemas',
            
            
        ]);

        DB::table('reporteproblemas')->insert([
            'rutOperario' => '19.518.211-1',
            'nombreOperario' => 'Alexis Sanchez',
            'correoOperario' => 'prueba8@gmail.com',
            'numeroOperario' => '+324232822',
            'prioridad' => 'alta',
            'estado' => 'Pendiente',
            'fechaReporteProblema' => '15-01-2021',
            'tituloReporteProblema' => 'Problema8',
            'contenidoReporteProblema' => 'Problemas',
            
            
        ]);


    }
}
