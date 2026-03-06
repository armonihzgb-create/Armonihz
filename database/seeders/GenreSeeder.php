<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Banda',
            'Blues',
            'Cumbia',
            'DJ / Electrónica',
            'Folclore',
            'Jazz',
            'Mariachi',
            'Norteño',
            'Pop',
            'Regional Mexicano',
            'Rock',
            'Salsa / Tropical',
            'Trova / Acústico',
            'Versátil / Grupos',
        ];

        $now = now();

        foreach ($genres as $genre) {
            DB::table('genres')->updateOrInsert(
            ['name' => $genre],
            ['name' => $genre, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
