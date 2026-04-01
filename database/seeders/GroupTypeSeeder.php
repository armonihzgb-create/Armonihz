<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Solista', 'Dúo', 'Trío', 'Cuarteto', 'Quinteto', 'Grupo', 
            'Grupo Versátil', 'Banda', 'Mariachi', 'DJ', 'Orquesta', 
            'Estudiantina', 'Rondalla'
        ];

        $now = now();
        foreach ($types as $type) {
            DB::table('group_types')->updateOrInsert(
                ['name' => $type],
                ['name' => $type, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
