<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$path = 'musician_media/U6W2S7H3YMxlViChDF9yqZIwMe00LlDuFTPwAgkK.mp4';
$fullPath = storage_path('app/public/' . $path);
$base = realpath(storage_path('app/public'));
$resolved = realpath($fullPath);
echo "fullPath: " . $fullPath . "\n";
echo "base: " . $base . "\n";
echo "resolved: " . $resolved . "\n";
echo "starts_with: " . (str_starts_with($resolved ?: '', $base ?: '') ? 'true' : 'false') . "\n";
echo "exists: " . (file_exists($resolved ?: $fullPath) ? 'true' : 'false') . "\n";
