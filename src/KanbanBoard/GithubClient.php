<?php

namespace App\KanbanBoard;

use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient;

class GithubRepository
{
    private $client;
    private $milestone_api;
    private $account;

    public function __construct($token, $account)
    {
        $this->account = $account;
        $this->client = new GithubClient(new CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache')));
        $this->client->authenticate($token, GithubClient::AUTH_HTTP_TOKEN);
        $this->milestone_api = $this->client->api('issues')->milestones();
    }

    public function milestones($repository)
    {
        return $this->milestone_api->all($this->account, $repository);
    }

    public function issues($repository, $milestone_id)
    {
        $issue_parameters = array('milestone' => $milestone_id, 'state' => 'all');
        return $this->client->api('issue')->all($this->account, $repository, $issue_parameters);
    }
}