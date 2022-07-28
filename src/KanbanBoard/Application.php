<?php

namespace App\KanbanBoard;

use Github\Client;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class Application
{
    protected ClientInterface $client;

    protected Client $github;

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

    /**
     * @param $template
     * @param $data
     *
     * @return string
     */
    public function display($template, $data): string
    {
        $m = new Mustache_Engine([
            'loader' => new Mustache_Loader_FilesystemLoader('../views'),
        ]);

        echo $m->render($template, $data);
    }
}
