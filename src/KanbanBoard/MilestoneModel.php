<?php
namespace App\KanbanBoard;

use App\KanbanBoard\CommonModel;
use App\KanbanBoard\Utilities;
use Michelf\Markdown;

class MilestoneModel extends CommonModel
{

    /**
     * Hydrates Milestone data
     */
    public function fetchOne($item): array
    {
        return [
            'title' => $item['title'],
            'number' => (int) $item['number'],
            'description' => trim($item['description']),
            'open_issues' => (int) $item['open_issues'],
            'closed_issues' => (int) $item['closed_issues'],
            'html_url' => filter_var($item['html_url'],FILTER_SANITIZE_URL),
            'progress' => Utilities::calcProgress(
                $item['closed_issues'],
                $item['open_issues']
            )
        ];
    }
}