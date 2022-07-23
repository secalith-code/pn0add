<?php

use App\KanbanBoard\Authentication;
use App\KanbanBoard\GithubClient;
use App\KanbanBoard\Utilities;
use Symfony\Component\Dotenv\Dotenv;
use App\KanbanBoard\Application;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
$authentication = new Authentication();
$token = $authentication->login();
$github = new GithubClient($token, Utilities::env('GH_ACCOUNT'));

$board = new Application($github, $repositories, array('waiting-for-feedback'));
$data = $board->board();

var_dump($data);die();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));
echo $m->render('index', array('milestones' => $data));
