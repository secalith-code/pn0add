<?php

use App\KanbanBoard\Authentication;
//use App\KanbanBoard\GithubClient;
use App\KanbanBoard\Utilities;
use Github\AuthMethod;
use Symfony\Component\Dotenv\Dotenv;
use App\KanbanBoard\Application;
use Github\Client as ApiClient;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
$authentication = new Authentication();
$token = $authentication->login();

$github = new ApiClient();
$github->authenticate($token,null,AuthMethod::ACCESS_TOKEN);

$app = new Application($github, $repositories, ['waiting-for-feedback','paused']);

$board=$app->board();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));

echo $m->render('index', $board);