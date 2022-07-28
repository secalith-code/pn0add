<?php

namespace App\KanbanBoard;

interface ClientInterface
{
    public function getMilestones(string $repository): array;
    public function getIssues(string $repository, string $milestoneId): array;
}