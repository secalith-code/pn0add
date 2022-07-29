<?php

namespace App\KanbanBoard;

interface ClientInterface
{
    /**
     * @param string $repository
     *
     * @return array|null
     */
    public function getMilestones(string $repository): ?array;

    /**
     * @param string $repository
     * @param string $milestoneId
     *
     * @return array|null
     */
    public function getIssues(string $repository, int $milestoneId): ?array;
}
