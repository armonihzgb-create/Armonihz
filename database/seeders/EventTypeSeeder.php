<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Bodas', 'XV años', 'Cumpleaños', 'Eventos corporativos', 
            'Fiestas privadas', 'Bares / antros', 'Serenatas', 'Eventos religiosos'
        ];

        $now = now();
        foreach ($types as $type) {
            DB::table('event_types')->updateOrInsert(
                ['name' => $type],
                ['name' => $type, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
