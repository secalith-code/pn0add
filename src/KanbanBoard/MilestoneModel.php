<?php

namespace App\KanbanBoard;

use App\KanbanBoard\CommonModel;
use App\Utilities;

class MilestoneModel extends CommonModel
{

    /**
     *  Hydrates Milestone data
     *
     * @param $item
     *
     * @return array
     */
    public function fetchOne($item): array
    {
        return [
            'title' => (string) $item['title'],
            'number' => (int) $item['number'],
            'description' => trim($item['description']),
            'open_issues' => (int) $item['open_issues'],
            'closed_issues' => (int) $item['closed_issues'],
            'html_url' => filter_var($item['html_url'], FILTER_SANITIZE_URL),
            'progress' => Utilities::calcProgress(
                (int) $item['closed_issues'],
                (int)  $item['open_issues']
            )
        ];
    }
}
