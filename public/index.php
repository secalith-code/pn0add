<?php

use App\KanbanBoard\Authentication;
//use App\KanbanBoard\GithubClient;
use App\KanbanBoard\Utilities;
use Github\AuthMethod;
use Symfony\Component\Dotenv\Dotenv;
use App\KanbanBoard\Application;
use Github\Client as ApiClient;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$privateKey = openssl_pkey_get_private(
    file_get_contents(Utilities::env('GH_PEMKEY')),
    Utilities::env('GH_PEMKEY_PASSPHRASE')
);

$payload = [
    'iss' => Utilities::env('GH_APP_ID'),
    'iat' => time()-10,
    'exp' => time()+(10*60)
];

$jwt = JWT::encode($payload, $privateKey, 'RS256');

$publicKey = openssl_pkey_get_details($privateKey)['key'];

$github = new ApiClient();

$github->authenticate(Utilities::env('GH_TOKEN'),$jwt,AuthMethod::JWT);

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));

$app = new Application($github, $repositories, ['waiting-for-feedback','paused']);

$board=$app->board();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));

echo $m->render('index', $board);