<?php

namespace App\PhpTests;

use App\Utilities;
use KanbanBoard\Authentication;
use Github\AuthMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use App\KanbanBoard\GithubClient;
use function DI\env as env;

final class MilestonesTest extends TestCase
{

    public $envFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->envFilePath = __DIR__.'/../.env';

        $dotenv = new Dotenv();
        $dotenv->load($this->envFilePath);
    }

    public function testGetMilestonesIssuesFetched()
    {
        $repositories = Utilities::getRepositoriesNames();

        $client = new GithubClient(
            Utilities::env('GH_TOKEN'),
            AuthMethod::ACCESS_TOKEN,
            Utilities::env('GH_ACCOUNT')
        );

        foreach($repositories as $repo) {
            $ms = $client->getMilestones($repo);

            $this->assertIsArray($ms);

            $this->assertArrayHasKey('number', $ms[0]);

            $iss = $client->getIssues($repo, $ms[0]['number']);
            // test only first Issue
            $this->assertIsArray($iss);

            $this->assertArrayHasKey('queued', $iss);
            $this->assertArrayHasKey('active', $iss);
            $this->assertArrayHasKey('completed', $iss);
        }
    }

}