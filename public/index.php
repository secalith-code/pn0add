<?php

use App\KanbanBoard\Application;
use App\KanbanBoard\Authentication;
use App\KanbanBoard\GithubClient;
use App\Utilities;
use DI\ContainerBuilder;
use Github\AuthMethod;
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
$client = new GithubClient(
    Utilities::env('GH_TOKEN'),
    null,
    AuthMethod::ACCESS_TOKEN,
    Utilities::env('GH_ACCOUNT')
);

//$client->authenticate(Utilities::env('GH_TOKEN'), AuthMethod::ACCESS_TOKEN);
//$ms = $client->api('issues')->milestones()->all(Utilities::env('GH_ACCOUNT'), "pn0add");
//var_dump($ms);
//die();


// JWT
// https://github.com/KnpLabs/php-github-api/blob/master/doc/security.md
//use Github\HttpClient\Builder;
//use Lcobucci\JWT\Configuration;
//use Lcobucci\JWT\Encoding\ChainedFormatter;
//use Lcobucci\JWT\Signer\Key\LocalFileReference;
//use Lcobucci\JWT\Signer\Rsa\Sha256;
//
//$builder = new Builder();
//
//$client = new Github\Client($builder);
//
//$config = Configuration::forSymmetricSigner(
//    new Sha256(),
//    LocalFileReference::file(Utilities::env('GH_PEMKEY'))
//);
//
//$now = new \DateTimeImmutable();
//$jwt = $config->builder(ChainedFormatter::withUnixTimestampDates())
//    ->issuedBy(Utilities::env('GH_APP_ID'))
//    ->issuedAt($now)
//    ->expiresAt($now->modify('+10 minute'))
//    ->getToken($config->signer(), $config->signingKey())
//;
//
//$client->authenticate($jwt->toString(), null, Github\AuthMethod::JWT);
//$ms = $client->api('issues')->milestones()->all(Utilities::env('GH_ACCOUNT'), "pn0add");
//
//var_dump($ms);
//die();

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));

$app = new Application($client, $repositories, ['waiting-for-feedback','paused']);

$board=$app->board();

$app->display('index',$board);