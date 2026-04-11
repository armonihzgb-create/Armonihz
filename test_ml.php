<?php
require 'vendor/autoload.php';
use Phpml\Clustering\KMeans;

$samples = [
    10 => [1, 2],
    12 => [1, 3],
    15 => [8, 8],
    18 => [9, 9]
];

$kmeans = new KMeans(2);
$clusters = $kmeans->cluster($samples);

print_r($clusters);
