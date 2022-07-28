<?php

use App\KanbanBoard\Application;
use App\KanbanBoard\Authentication;
use App\KanbanBoard\GithubClient;
use App\Utilities;
use DI\ContainerBuilder;
use Github\AuthMethod;
use Github\Client as ApiClient;
use Github\Client as Client;

require __DIR__ . '/../vendor/autoload.php';

Utilities::loadEnv(__DIR__ . '/../.env');

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/di.php');
$container = $containerBuilder->build();

/** @var \App\KanbanBoard\Authentication $authenticate */
$authenticate = $container->get('Authentication');

// .env data OAUTH_APPLICATION
// not good for testing
//$token = $authenticate->login();
//$client = new Client();
//$client->authenticate($token, AuthMethod::ACCESS_TOKEN);
//$ms = $client->api('issues')->milestones()->all(Utilities::env('GH_ACCOUNT'), "pn0add");
//var_dump($ms);
//die();

// .env data GH_TOKEN
//$client = new Client();
//$client->authenticate(Utilities::env('GH_TOKEN'), AuthMethod::ACCESS_TOKEN);
//$ms = $client->api('issues')->milestones()->all(Utilities::env('GH_ACCOUNT'), "pn0add");
//var_dump($ms);
//die();

// JWT
//$client = new Client();

$jwt = $authenticate->getJWT();

print($jwt);
die();

$client = new GithubClient(
    Utilities::env('GH_TOKEN'),
    $jwt,
    AuthMethod::ACCESS_TOKEN,
    Utilities::env('GH_ACCOUNT')
);

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));

$app = new Application($client, $repositories, ['waiting-for-feedback','paused']);

$board=$app->board();

$app->display('index',$board);