<?php

namespace App\KanbanBoard;

use Github\Client as ApiClient;
use Michelf\Markdown;

class Application
{
    protected ClientInterface $client;

    protected ApiClient $github;

    public ?array $paused_labels;

    protected array $repositories;

    /**
     * @param ClientInterface $client
     * @param array           $repositories
     * @param array|null      $paused_labels
     */
    public function __construct(
        ClientInterface $client,
        array $repositories,
        ?array $paused_labels = []
    ) {
        $this->client = $client;
        $this->repositories = $repositories;
        $this->paused_labels = $paused_labels;
    }

    /**
     * @return array[]
     */
    public function board(): array
    {
        $data = [];

        if (! empty($this->repositories)) {
            foreach ($this->repositories as $repositoryName) {
                $milestones = $this->client->getMilestones($repositoryName);

                if (! empty($milestones)) {
                    foreach ($milestones as $ms) {
                        $issues = $this->client->getIssues(
                            $repositoryName,
                            $ms['number']
                        );

                        $data[] = [
                            'data' => $ms,
                            'issues' => $issues,
                        ];
                    }
                }
            }
        }

        return ['milestones' => $data];
    }
}
