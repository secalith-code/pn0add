<?php

namespace App\KanbanBoard\Domain;

use App\KanbanBoard\IssueModel;
use App\KanbanBoard\MilestoneModel;
use App\Utilities;

class RepositoryModel
{

    protected $client;

    protected IssueModel $issueModel;

    protected MilestoneModel $milestoneModel;

    protected array $repositories;

    public function __construct(
        IssueModel $issueModel,
        MilestoneModel $milestoneModel,
        array $repositories
    )
    {
        $this->issueModel=$issueModel;
        $this->milestoneModel=$milestoneModel;
        $this->repositories=$repositories;
    }

    public function getData($client): array
    {
        $this->client=$client;

        $data = [
            'milestones' => []
        ];

        if (! empty($this->repositories)) {
            foreach ($this->repositories as $repo) {
                $milestones = $this->client->getMilestones($repo);

                if (! empty($milestones)) {
                    foreach ($milestones as $ms) {
                        $issues = $this->client->getIssues(
                            $repo,
                            $ms['number']
                        );

                        $data['milestones'][] = [
                            'data' => $ms,
                            'issues' => $issues,
                        ];
                    }
                }
            }
        }

        return $data;
    }

}
