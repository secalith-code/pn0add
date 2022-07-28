<?php

use App\KanbanBoard\Application;
use App\KanbanBoard\Authentication;
use App\KanbanBoard\GithubClient;
use App\Utilities;
use DI\ContainerBuilder;
use Github\AuthMethod;
use Github\Client as ApiClient;

require __DIR__ . '/../vendor/autoload.php';

Utilities::loadEnv(__DIR__ . '/../.env');

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/di.php');
$container = $containerBuilder->build();

$authenticate = $container->get('Authentication');

$jwt = $authenticate->getJWT();

$client = new GithubClient(
    Utilities::env('GH_TOKEN'),
    $jwt,
    AuthMethod::JWT,
    Utilities::env('GH_ACCOUNT')
);

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));

$app = new Application($client, $repositories, ['waiting-for-feedback','paused']);

$board=$app->board();

$app->display('index',$board);