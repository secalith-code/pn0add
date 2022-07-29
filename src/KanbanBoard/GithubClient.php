<?php

namespace App\KanbanBoard;

use App\KanbanBoard\Domain\IssueModel;
use App\KanbanBoard\Domain\MilestoneModel;
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
     * @param        $authMethod
     * @param string $account
     * @param        $cacheClient
     */
    public function __construct(
        $tokenOrLogin,
        $authMethod,
        string $account,
        $cacheClient = null
    ) {
        $this->account = $account;

        $this->cacheClient = $cacheClient;

        $this->client = new Client();
        $this->client->authenticate($tokenOrLogin, null, $authMethod);

        $this->issuesApi = $this->client->api('issues');

        $this->milestoneModel = new MilestoneModel();
        $this->issueModel = new IssueModel();
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     *  Get data from API or cache and hydrate the result
     *
     * @param string $repository
     *
     * @return null|array
     */
    public function getMilestones(string $repository): ?array
    {
        if ($this->cacheClient) {
            /** @var array $milestones  Try to reach data from cache */
            $milestones = $this->getMilestonesCached($repository);
        } else {
            // Call API
            $milestones = $this->issuesApi->milestones()->all($this->account, $repository);
            /** @var array $milestones  Hydrated Milestones */
            $milestones = $this->milestoneModel->fetchAll($milestones);
        }

        return $milestones;
    }

    /**
     * @param string $repository
     *
     * @return array|null
     */
    protected function getMilestonesCached(string $repository): ?array
    {
        $cacheKey = $repository . '.milestones.fetch.all';
        $milestones = $this->cacheClient->get($cacheKey);

        if (! $milestones) {
            // Call API
            $milestones = $this->issuesApi->milestones()->all($this->account, $repository);
            /** @var array $milestones  Hydrated Milestones */
            $milestones = $this->milestoneModel->fetchAll($milestones);

            $this->cacheClient->set($cacheKey, $milestones);
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
        $issueParams = ['milestone' => $milestoneId, 'state' => 'all'];

        if ($this->cacheClient) {
            /** @var array $milestones  Try to reach data from cache */
            $issues = $this->getIssuesCached($repository, $milestoneId, $issueParams);
        } else {
            // Call API
            $issues = $this->issuesApi->all($this->account, $repository, $issueParams);
            /** @var array $issues  Hydrate Issues */
            $issues = $this->issueModel->fetchAll($issues);
            // Fetch by status
            $issues = $this->issueModel->fetchIssuesByStatus($issues);
        }

        return $issues;
    }

    protected function getIssuesCached(string $repository, int $milestoneId, ?array $issueParams): ?array
    {
        $cacheKey = $repository . '.milestone.' . $milestoneId . '.issues.fetch.all.by_status';

        $issues = $this->cacheClient->get($cacheKey);

        if (! $issues) {
            // Call API
            $issues = $this->issuesApi->all($this->account, $repository, $issueParams);
            /** @var array $issues  Hydrate Issues */
            $issues = $this->issueModel->fetchAll($issues);
            // Fetch by status
            $issues = $this->issueModel->fetchIssuesByStatus($issues);

            $this->cacheClient->set($cacheKey, $issues);
        }

        return $issues;
    }
}
