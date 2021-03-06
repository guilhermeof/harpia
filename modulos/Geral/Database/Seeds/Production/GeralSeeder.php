<?php
namespace Modulos\Geral\Database\Seeds\Production;

use Illuminate\Database\Seeder;

class GeralSeeder extends Seeder
{
    public function run()
    {
        $this->call(TiposDocumentoSeeder::class);
        $this->command->info('Tipos de Documentos Table seeded');

        $this->call(TitulacaoTableSeeder::class);
        $this->command->info('Titulacoes Table seeded');
    }
}
