<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            // Populares / Globales
            ['name' => 'Pop', 'category' => 'Populares / Globales'],
            ['name' => 'Rock', 'category' => 'Populares / Globales'],
            ['name' => 'Metal', 'category' => 'Populares / Globales'],
            ['name' => 'Indie', 'category' => 'Populares / Globales'],
            ['name' => 'Alternativo', 'category' => 'Populares / Globales'],
            ['name' => 'Hip-Hop', 'category' => 'Populares / Globales'],
            ['name' => 'Rap', 'category' => 'Populares / Globales'],
            ['name' => 'Trap', 'category' => 'Populares / Globales'],
            ['name' => 'R&B', 'category' => 'Populares / Globales'],
            ['name' => 'Electrónica', 'category' => 'Populares / Globales'],
            ['name' => 'House', 'category' => 'Populares / Globales'],
            ['name' => 'Techno', 'category' => 'Populares / Globales'],
            
            // Regional Mexicano
            ['name' => 'Regional Mexicano', 'category' => 'Regional Mexicano'],
            ['name' => 'Norteño', 'category' => 'Regional Mexicano'],
            ['name' => 'Banda', 'category' => 'Regional Mexicano'],
            ['name' => 'Banda Sinaloense', 'category' => 'Regional Mexicano'],
            ['name' => 'Mariachi', 'category' => 'Regional Mexicano'],
            ['name' => 'Sierreño', 'category' => 'Regional Mexicano'],
            ['name' => 'Grupero', 'category' => 'Regional Mexicano'],
            ['name' => 'Duranguense', 'category' => 'Regional Mexicano'],
            ['name' => 'Corridos', 'category' => 'Regional Mexicano'],
            ['name' => 'Corridos Tumbados', 'category' => 'Regional Mexicano'],
            ['name' => 'Huapango', 'category' => 'Regional Mexicano'],
            ['name' => 'Son Jarocho', 'category' => 'Regional Mexicano'],

            // Latinos / Tropicales
            ['name' => 'Cumbia', 'category' => 'Latinos / Tropicales'],
            ['name' => 'Salsa / Tropical', 'category' => 'Latinos / Tropicales'],
            ['name' => 'Bachata', 'category' => 'Latinos / Tropicales'],
            ['name' => 'Merengue', 'category' => 'Latinos / Tropicales'],
            ['name' => 'Reggaetón', 'category' => 'Latinos / Tropicales'],
            ['name' => 'Latin Pop', 'category' => 'Latinos / Tropicales'],
            ['name' => 'Reggae', 'category' => 'Latinos / Tropicales'],
            ['name' => 'Ska', 'category' => 'Latinos / Tropicales'],

            // Otros Géneros
            ['name' => 'Jazz', 'category' => 'Otros Géneros'],
            ['name' => 'Blues', 'category' => 'Otros Géneros'],
            ['name' => 'Clásica', 'category' => 'Otros Géneros'],
            ['name' => 'Ópera', 'category' => 'Otros Géneros'],
            ['name' => 'Funk', 'category' => 'Otros Géneros'],
            ['name' => 'Soul', 'category' => 'Otros Géneros'],
            ['name' => 'Big Band', 'category' => 'Otros Géneros'],
            ['name' => 'Instrumental', 'category' => 'Otros Géneros'],
            ['name' => 'Trova / Acústico', 'category' => 'Otros Géneros'],
            ['name' => 'Folclore', 'category' => 'Otros Géneros'],
            ['name' => 'Versátil / Grupos', 'category' => 'Otros Géneros'], // Keeping this just in case from legacy
            ['name' => 'DJ / Electrónica', 'category' => 'Otros Géneros'], // Legacy
        ];

        $now = now();

        foreach ($genres as $genre) {
            DB::table('genres')->updateOrInsert(
                ['name' => $genre['name']],
                [
                    'name' => $genre['name'], 
                    'category' => $genre['category'],
                    'created_at' => $now, 
                    'updated_at' => $now
                ]
            );
        }
    }
}
