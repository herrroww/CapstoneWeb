<?php

use Illuminate\Database\Seeder;

class EmpresasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('empresas')->insert([
            'rutEmpresa' => '12.122.111-9',
            'nombreEmpresa' => 'Empresa1',
            'compania' => 'Compañia1',
            
        ]);

        DB::table('empresas')->insert([
            'rutEmpresa' => '12.121.111-9',
            'nombreEmpresa' => 'Empresa2',
            'compania' => 'Compañia2',
            
        ]);

        DB::table('empresas')->insert([
            'rutEmpresa' => '12.132.111-9',
            'nombreEmpresa' => 'Empresa2',
            'compania' => 'Compañia2',
            
        ]);
        
        DB::table('empresas')->insert([
            'rutEmpresa' => '12.422.111-9',
            'nombreEmpresa' => 'Empresa1',
            'compania' => 'Compañia3',
            
        ]);

        DB::table('empresas')->insert([
            'rutEmpresa' => '12.122.411-9',
            'nombreEmpresa' => 'Empresa4',
            'compania' => 'Compañia4',
            
        ]);

        DB::table('empresas')->insert([
            'rutEmpresa' => '12.152.111-9',
            'nombreEmpresa' => 'Empresa5',
            'compania' => 'Compañia5',
            
        ]);

        DB::table('empresas')->insert([
            'rutEmpresa' => '12.162.111-9',
            'nombreEmpresa' => 'Empresa6',
            'compania' => 'Compañia6',
            
        ]);
        
        DB::table('empresas')->insert([
            'rutEmpresa' => '12.152.171-9',
            'nombreEmpresa' => 'Empresa7',
            'compania' => 'Compañia7',
            
        ]);

        DB::table('empresas')->insert([
            'rutEmpresa' => '12.182.111-9',
            'nombreEmpresa' => 'Empresa8',
            'compania' => 'Compañia8',
            
        ]);

        
            
    }
}
