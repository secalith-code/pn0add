<?php

namespace App\KanbanBoard;

use Github\Client;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class Application
{
    protected ClientInterface $client;

    protected Client $github;

    protected $cacheClient;

    public ?array $paused_labels;

    protected $repositoryModel;

    public function __construct(
        ClientInterface $client,
        $repositoryModel,
        $cacheClient = null,
        ?array $paused_labels = []
    ) {
        $this->client = $client;
        $this->repositoryModel = $repositoryModel;
        $this->cacheClient = $cacheClient;
        $this->paused_labels = $paused_labels;
    }

    /**
     * @return array[]
     */
    public function board(): array
    {
        return $this->repositoryModel->getData($this->client);
    }

    /**
     * @param $template
     * @param $data
     *
     * @return string
     */
    public function display($template, $data): ?string
    {
        $m = new Mustache_Engine([
            'loader' => new Mustache_Loader_FilesystemLoader('../views'),
        ]);

        return $m->render($template, $data);
    }
}
