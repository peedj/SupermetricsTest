<?php

require __DIR__ . '/vendor/autoload.php';

// 1. load enviroment data
$envReader = new \ApiConnect\helpers\DotEnvReader(__DIR__ . '/.env');
$envReader->load();

// 2. run app
$apiConnect = new \ApiConnect\ApiConnect();
$data = json_encode($apiConnect->getPostsStats(10));

if (PHP_SAPI !== 'cli') {
    header('Content-Type: application/json');
}
echo $data;
