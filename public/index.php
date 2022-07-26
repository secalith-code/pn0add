<?php

use App\KanbanBoard\Authentication;
use App\KanbanBoard\GithubClient;
use App\KanbanBoard\Utilities;
use Github\AuthMethod;
use Symfony\Component\Dotenv\Dotenv;
use App\KanbanBoard\Application;
use Laminas\Http\Client as ApiClient;
use Symfony\Component\HttpClient\HttplugClient;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
$authentication = new Authentication();
$token = $authentication->login();

$github = new \Github\Client();
$github->authenticate($token,null,AuthMethod::ACCESS_TOKEN);

$app = new Application($github, $repositories, array('waiting-for-feedback'));

$board=$app->board();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));

echo $m->render('index', $data);


//$issues= $github->api('issue')->milestones()->all(Utilities::env('GH_ACCOUNT'));
//var_dump($issues);die();
//$board=$app->board();
//die('elo');
foreach($repositories as $repository) {
    $milestones[]= $github->api('issue')->milestones()->all(Utilities::env('GH_ACCOUNT'),$repository);
//    $issues= $github->api('issues')->all(Utilities::env('GH_ACCOUNT'),$repository,['state'=>'all']);
}
echo '<pre>';
var_dump($milestones);die();
$data=[];
if( ! empty($issues)) {
    foreach($issues as $issue) {
        if(array_key_exists('milestone',$issue)) {
            $data['issues'][$issue['state']]['data'][]=$issue;
        }
    }
    if( array_key_exists('issues',$data) && ! empty($data['issues'])) {
        foreach($data['issues'] as $issueState=>$issue) {
            $data['issues'][$issueState]['length']=count($issue['data']);
        }
    }
}


echo "<pre>";var_dump($data);

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));

echo $m->render('index', $data);

