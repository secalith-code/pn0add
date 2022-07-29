<?php

use App\KanbanBoard\Application;
use App\KanbanBoard\Authentication;
use App\KanbanBoard\GithubClient;
use App\Utilities;
use DI\ContainerBuilder;
use Github\AuthMethod;
use Github\Client as Client;
use Symfony\Component\Dotenv\Dotenv;

use function DI\env as env;

require __DIR__ . '/../vendor/autoload.php';

Utilities::loadEnv(__DIR__ . '/../.env');

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/di.php');
$containerBuilder->enableCompilation(__DIR__ . '/../var/tmp');
$containerBuilder->writeProxiesToFile(true, __DIR__ . '/../var/tmp/proxies');
$container = $containerBuilder->build();

/** @var \App\KanbanBoard\Authentication $authenticate */
$authenticate = $container->get('Authentication');
/** @var array $repositories */
$repositories = $container->get('gh.repositories');

$cacheClient = $container->get('CacheAdapter');

$repositoriesData = $container->get('RepositoryModel');

if('oauth'===strtolower(Utilities::env('GH_AUTH_METHOD'))) {
    // perform OAuth
    /** @var \App\KanbanBoard\Authentication $authenticate */
    $authenticate = $container->get('Authentication');
    $token = $authenticate->login();
} elseif( ! empty(Utilities::env('GH_TOKEN'))) {
    // .env data GH_TOKEN
    $token = Utilities::env('GH_TOKEN');
}

$client = new GithubClient(
    $token,
    AuthMethod::ACCESS_TOKEN,
    Utilities::env('GH_ACCOUNT'),
    $cacheClient
);

$app = new Application($client, $repositoriesData, $cacheClient, ['waiting-for-feedback','paused']);

$board=$app->board();

echo $app->display('index',$board);