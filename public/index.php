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

$cacheClient = $container->get('Memcached');

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
    null,
    AuthMethod::ACCESS_TOKEN,
    Utilities::env('GH_ACCOUNT'),
    $cacheClient
);

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


$app = new Application($client, $repositories, $cacheClient, ['waiting-for-feedback','paused']);

$board=$app->board();

$app->display('index',$board);