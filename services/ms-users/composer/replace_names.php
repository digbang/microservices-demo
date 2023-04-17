<?php

use Dotenv\Dotenv;
use Symfony\Component\Finder\Finder;

require __DIR__.'/../vendor/autoload.php';

$basePath = dirname(__DIR__);

// Load .env variables into $_ENV super global variable
$env = Dotenv::createImmutable($basePath);
$env->load();

$projectName = strtolower($_ENV['PROJECT_NAME'] ?: basename($basePath)); // ex: project-name
$uppercaseProjectName = strtoupper(str_replace('-', '_', $projectName)); // ex: PROJECT_NAME

$files = Finder::create()
    ->files()
    ->in($basePath)
    ->depth(0)
    ->ignoreDotFiles(false)
    ->name([
        '.env',
        '.env.example',
        'docker-compose.yml',
    ])
    ->append(Finder::create()->files()->in([
        "$basePath/docker",
    ]));

foreach ($files as $file) {
    file_put_contents($file->getRealPath(), str_replace(
        ['project-name', 'PROJECT-NAME'],
        [$projectName, $uppercaseProjectName],
        $file->getContents(),
    ));
}
