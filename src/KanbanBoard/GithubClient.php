<?php

namespace App\KanbanBoard;

use Github\Client as Client;
use Github\HttpClient\CachedHttpClient;
use App\KanbanBoard\ClientInterface;
use Github\Api\Issue;
use App\KanbanBoard\MilestoneModel;

class GithubClient implements ClientInterface
{
    private Client $client;
    private Issue $issuesApi;
    private ?string $account;
    private $issuesModel;
    private MilestoneModel $milestoneModel;

    public function __construct($token, $password,$method=null,$account=null)
    {
        $this->account = $account;
        $this->client = new Client();
        $this->client->authenticate($token, $password,$method);
        $this->issuesApi = $this->client->api('issues');
        $this->milestoneModel = new MilestoneModel();
    }

    public function getMilestones(string $repository): array
    {
        $milestones = $this->issuesApi->milestones()->all($this->account, $repository);

        return $this->milestoneModel->fetchAll($milestones);
    }

    public function getIssues(string $repository, string $milestoneId): array
    {
        $issueParams = array('milestone' => $milestoneId, 'state' => 'all');

        return $this->issuesApi->all($this->account, $repository, $issueParams);
    }
}