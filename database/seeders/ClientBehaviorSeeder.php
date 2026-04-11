<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\HiringRequest;
use App\Models\MusicianProfile;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClientBehaviorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Asegurarnos que haya al menos un músico en el sistema para asociar las requests
        $musician = MusicianProfile::first();
        if (!$musician) {
            $userMusician = User::factory()->create(['role' => 'musico', 'name' => 'Demo Musico', 'email' => 'musico' . rand(1, 1000) . '@demo.com', 'password' => bcrypt('password')]);
            $musician = MusicianProfile::create([
                'user_id' => $userMusician->id,
                'firebase_uid' => Str::random(28),
                'nombre_artistico' => 'Demo Mariachi',
                'biografia' => 'Mariachi de prubea',
                'tarifa_base' => 500,
                'slug' => 'demo-mariachi-' . Str::random(5)
            ]);
        }

        // 2. Definir los grupos
        $totalClients = 100;
        
        $groups = [
            'casual' => ['count' => 70, 'freq_min' => 1, 'freq_max' => 1, 'budget_min' => 100, 'budget_max' => 300],
            'organizador' => ['count' => 20, 'freq_min' => 4, 'freq_max' => 8, 'budget_min' => 300, 'budget_max' => 800],
            'premium' => ['count' => 10, 'freq_min' => 1, 'freq_max' => 2, 'budget_min' => 2000, 'budget_max' => 5000],
        ];

        // 3. Crear los registros
        foreach ($groups as $label => $rules) {
            $this->command->info("Generando {$rules['count']} clientes del tipo: {$label}");

            for ($i = 0; $i < $rules['count']; $i++) {
                // Crear Usuario 
                $firebase_uid = Str::random(28);
                $user = User::create([
                    'name' => "Cliente $label $i",
                    'email' => "$label.$i." . Str::random(5) . "@test.com",
                    'password' => bcrypt('password'),
                    'role' => 'cliente',
                    'firebase_uid' => $firebase_uid,
                    'is_verified' => true,
                    'is_active' => true,
                ]);

                // Crear Perfil Cliente
                $client = Client::create([
                    'user_id' => $user->id,
                    'firebase_uid' => $firebase_uid,
                    'nombre' => "Cliente",
                    'apellido' => ucfirst($label),
                    'email' => $user->email,
                    'telefono' => '1234567890'
                ]);

                // Cuántos eventos organizó en el último año este cliente
                $numEvents = rand($rules['freq_min'], $rules['freq_max']);

                for ($e = 0; $e < $numEvents; $e++) {
                    // Distribuidos a lo largo del año pasado
                    $date = Carbon::now()->subDays(rand(1, 365));
                    
                    HiringRequest::create([
                        'client_id' => $client->id,
                        'musician_profile_id' => $musician->id,
                        'event_date' => $date,
                        'end_time' => $date->copy()->addHours(3),
                        'event_location' => 'CDMX, Evento de prueba',
                        'description' => "Evento simulado perfil $label",
                        'budget' => rand($rules['budget_min'], $rules['budget_max']),
                        'status' => 'completed',
                    ]);
                }
            }
        }
    }
}
