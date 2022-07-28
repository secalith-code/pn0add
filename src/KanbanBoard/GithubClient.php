<?php

namespace App\KanbanBoard;

use App\KanbanBoard\IssueModel;
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
    private IssueModel $issuesModel;
    private MilestoneModel $milestoneModel;

    public function __construct($token, $password,$method=null,$account=null)
    {
        $this->account = $account;
        $this->client = new Client();
        $this->client->authenticate($token, $password,$method);
        $this->issuesApi = $this->client->api('issues');
        $this->milestoneModel = new MilestoneModel();
        $this->issueModel = new IssueModel();
    }

    /**
     * Get data from API and hydrate the result
     *
     * @param string $repository
     * @return array
     */
    public function getMilestones(string $repository): array
    {
        $milestones = $this->issuesApi->milestones()->all($this->account, $repository);

        return $this->milestoneModel->fetchAll($milestones);
    }

    /**
     * Get data from API and hydrate the result
     *
     * @param string $repository
     * @param string $milestoneId
     * @return array
     */
    public function getIssues(string $repository, string $milestoneId): array
    {
        $issueParams = array('milestone' => $milestoneId, 'state' => 'all');
        $issues = $this->issuesApi->all($this->account, $repository, $issueParams);

        $fetchedIssues = $this->issueModel->fetchAll($issues);

        $issuesByStatus = $this->issueModel->fetchIssuesByStatus($fetchedIssues);

        return $issuesByStatus;
    }

}