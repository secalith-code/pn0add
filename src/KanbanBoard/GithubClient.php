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

    private $cacheClient;

    /**
     * @param        $tokenOrLogin
     * @param        $password
     * @param        $authMethod
     * @param string $account
     */
    public function __construct(
        $tokenOrLogin,
        $password = null,
        $authMethod,
        string $account,
        $cacheClient = null
    ) {
        $this->account = $account;

        $this->cacheClient = $cacheClient;

        $this->client = new Client();
        $this->client->authenticate($tokenOrLogin, $password, $authMethod);

        $this->issuesApi = $this->client->api('issues');

        $this->milestoneModel = new MilestoneModel();
        $this->issueModel = new IssueModel();
    }

    public function getClient()
    {
        return $this->client;
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
        $milestones = $this->cacheClient->get($repository . '.milestones.fetch.all');

        if (! $milestones) {
            $milestones = $this->issuesApi->milestones()->all($this->account, $repository);
            $milestones = $this->milestoneModel->fetchAll($milestones);

            $this->cacheClient->set($repository . '.milestones.fetch.all', $milestones);
        }

        return $milestones;
    }

    /**
     *  Get data from API and hydrate the result
     *
     * @param string $repository
     * @param string $milestoneId
     *
     * @return null|array
     */
    public function getIssues(string $repository, int $milestoneId): ?array
    {
        $cacheKey = $repository . '.milestone.' . $milestoneId . '.issues.fetch.all.by_status';

        $fetchByStatus = $this->cacheClient->get($cacheKey);

        if (! $fetchByStatus) {
            $issueParams = ['milestone' => $milestoneId, 'state' => 'all'];

            $issues = $this->issuesApi->all($this->account, $repository, $issueParams);

            $fetchIssues = $this->issueModel->fetchAll($issues);
            $fetchByStatus = $this->issueModel->fetchIssuesByStatus($fetchIssues);

            $this->cacheClient->set($cacheKey, $fetchByStatus);
        }

        return $fetchByStatus;
    }

    public function rateLimit()
    {
        var_dump($this->client->rateLimit());
        return $this->client->rateLimit();
    }
}
