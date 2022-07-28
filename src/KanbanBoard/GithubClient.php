<?php

namespace App\KanbanBoard;

use App\KanbanBoard\ClientInterface;
use App\KanbanBoard\IssueModel;
use App\KanbanBoard\MilestoneModel;
use App\Utilities;
use Github\Api\Issue;
use Github\Client as Client;

class GithubClient implements ClientInterface
{
    private Client $client;

    private Issue $issuesApi;

    private ?string $account;

    private IssueModel $issueModel;

    private MilestoneModel $milestoneModel;

    /**
     * @param $token
     * @param $password
     * @param $method
     * @param $account
     */
    public function __construct($token, $password, $method = null, $account = null)
    {
        $this->account = $account;

        $this->client = new Client();
        $this->client->authenticate($token, $method);

        $this->issuesApi = $this->client->api('issues');

        $this->milestoneModel = new MilestoneModel();
        $this->issueModel = new IssueModel();
    }

    /**
     *  Get data from API and hydrate the result
     *
     * @param string $repository
     *
     * @return null|array
     */
    public function getMilestones(string $repository): ?array
    {
        $milestones = $this->issuesApi->milestones()->all($this->account, $repository);

        return $this->milestoneModel->fetchAll($milestones);
    }

    /**
     *  Get data from API and hydrate the result
     *
     * @param string $repository
     * @param string $milestoneId
     *
     * @return null|array
     */
    public function getIssues(string $repository, string $milestoneId): ?array
    {
        $issueParams = ['milestone' => $milestoneId, 'state' => 'all'];
        $issues = $this->issuesApi->all($this->account, $repository, $issueParams);

        $fetchedIssues = $this->issueModel->fetchAll($issues);

        return $this->issueModel->fetchIssuesByStatus($fetchedIssues);
    }
}
