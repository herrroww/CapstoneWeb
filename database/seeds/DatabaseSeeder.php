<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(OperariosTableSeeder::class);
        $this->call(EmpresasTableSeeder::class);
        $this->call(ComponentesTableSeeder::class);
        $this->call(AsignarsTableSeeder::class);
        $this->call(ReporteProblemasTableSeeder::class);
    }
}
