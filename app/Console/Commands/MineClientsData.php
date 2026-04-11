<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Phpml\Clustering\KMeans;

class MineClientsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:mine-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutar clasificador K-Means (Min-Max) para segmentar clientes en perfiles de gasto.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando Minería de Datos: Algoritmo K-Means...");

        // 1. EXTRACCIÓN Y LIMPIEZA
        $clients = Client::with('clientRequests')->get();
        $dataset = [];
        $samples = [];
        $mapping = [];

        $minFreq = PHP_INT_MAX; $maxFreq = 0;
        $minBudget = PHP_INT_MAX; $maxBudget = 0;

        foreach ($clients as $client) {
            $requests = $client->clientRequests->where('status', 'completed');
            $freq = $requests->count();
            
            // Limpieza: descartamos inactivos por completo
            if ($freq == 0) continue; 
            
            $budget = $requests->sum('budget');
            
            if ($freq < $minFreq) $minFreq = $freq;
            if ($freq > $maxFreq) $maxFreq = $freq;
            if ($budget < $minBudget) $minBudget = $budget;
            if ($budget > $maxBudget) $maxBudget = $budget;

            $dataset[] = [
                'client_id' => $client->id,
                'real_freq' => $freq,
                'real_budget' => $budget
            ];
        }

        if (count($dataset) < 3) {
            $this->error("No hay suficientes datos limpios para ejecutar el KMeans (Mínimo 3 requeridos, encontramos ".count($dataset).").");
            return;
        }

        $this->info("Paso 1 Completado: " . count($dataset) . " clientes válidos extraídos.");
        
        // 2. NORMALIZACIÓN MIN-MAX
        foreach ($dataset as $idx => $data) {
            $normFreq = ($maxFreq - $minFreq) > 0 ? ($data['real_freq'] - $minFreq) / ($maxFreq - $minFreq) : 0;
            $normBudget = ($maxBudget - $minBudget) > 0 ? ($data['real_budget'] - $minBudget) / ($maxBudget - $minBudget) : 0;
            
            $samples[$idx] = [(float)$normFreq, (float)$normBudget];
            $mapping[$idx] = $data; // Guardamos index para luego
        }

        $this->info("Paso 2 Completado: Variables Frecuencia y Presupuesto normalizadas [0.0 - 1.0].");

        // 3. ENTRENAMIENTO K-MEANS
        $kmeans = new KMeans(3); 
        $clusters = $kmeans->cluster($samples);

        $this->info("Paso 3 Completado: K-Means logró segmentar en 3 clusters.");

        // 4. IDENTIFICACIÓN DE CENTROIDES (Etiquetado Euclidiano)
        // Calcularemos el promedio real que quedó en cada cluster
        $clusterProfiles = [];
        
        foreach ($clusters as $clusterId => $clusterSamples) {
            $sumFreq = 0;
            $sumBudget = 0;
            $membersIds = [];
            
            foreach ($clusterSamples as $idx => $sample) {
                // Recuperar los datos reales para el análisis del negocio
                $realData = $mapping[$idx]; 
                $sumFreq += $realData['real_freq'];
                $sumBudget += $realData['real_budget'];
                $membersIds[] = $realData['client_id'];
            }
            
            $count = count($clusterSamples);
            $avgFreq = $count > 0 ? ($sumFreq / $count) : 0;
            $avgBudget = $count > 0 ? ($sumBudget / $count) : 0;
            
            $clusterProfiles[$clusterId] = [
                'clusterId' => $clusterId,
                'members' => $membersIds,
                'avgFreq' => $avgFreq,
                'avgBudget' => $avgBudget
            ];
        }

        // Determinar dinámicamente quién es Premium, Organizador y Casual
        // Premium: Tiene el presupuesto promedio más alto, pero frecuencia menor a organizador
        // Organizador: Tiene la frecuencia más alta
        // Casual: Tiene el presupuesto más bajo y frecuencia baja
        
        // Ordenamos por Frecuencia primero
        usort($clusterProfiles, function($a, $b) {
            return $b['avgFreq'] <=> $a['avgFreq'];
        });
        
        // El de mayor frecuencia es el organizador
        $clusterProfiles[0]['label'] = 'Organizador';
        
        // De los 2 restantes, el de mayor presupuesto promedio es el Premium
        if ($clusterProfiles[1]['avgBudget'] > $clusterProfiles[2]['avgBudget']) {
            $clusterProfiles[1]['label'] = 'Premium VIP';
            $clusterProfiles[2]['label'] = 'Casual';
        } else {
            $clusterProfiles[2]['label'] = 'Premium VIP';
            $clusterProfiles[1]['label'] = 'Casual';
        }

        // 5. GUARDAR Y REPORTAR RESULTADOS DE NEGOCIO
        $this->info("\n--- RESULTADOS DEL ALGORITMO MINERO ---");
        foreach ($clusterProfiles as $profile) {
            $this->info("Clúster [{$profile['label']}]: " . count($profile['members']) . " clientes.");
            $this->info("   -> Frecuencia prom.: " . round($profile['avgFreq'], 2) . " eventos/año.");
            $this->info("   -> Gasto promedio : $" . round($profile['avgBudget'], 2) . "\n");

            // Persistir en base de datos
            Client::whereIn('id', $profile['members'])->update([
                'ai_cluster_id' => $profile['clusterId'],
                'ai_cluster_label' => $profile['label']
            ]);
        }

        $this->info("Etiquetas de K-Means guardadas exitosamente en la columna ai_cluster_label de los Clientes.");
    }
}
