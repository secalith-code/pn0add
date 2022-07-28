<?php
namespace App\KanbanBoard;

use Michelf\Markdown;
use App\KanbanBoard\Utilities;

class MilestoneModel
{
    public function fetchAll($milestones): ?array
    {
        $data=null;
        if( ! empty($milestones)) {
            foreach($milestones as $ms) {
                $title=htmlspecialchars($ms['title']);
                // return array will be sorted alphabetically by the milestone title.
                $ms['title'] = $title;
                // fetch single milestone
                $data[$title] = $this->fetchOne($ms);
            }

            ksort($data);

            return array_values($data);
        }

        return $data;
    }

    /**
     * Hydrates milestone data
     */
    public function fetchOne($milestone): array
    {
        $data = [
            'title' => $milestone['title'],
            'number' => (int) $milestone['number'],
            'description' => trim($milestone['description']),
            'open_issues' => (int) $milestone['open_issues'],
            'closed_issues' => (int) $milestone['closed_issues'],
            'html_url' => filter_var($milestone['html_url'],FILTER_SANITIZE_URL),
            'progress' => Utilities::calcProgress(
                $milestone['closed_issues'],
                $milestone['open_issues']
            )
        ];

        return $data;
    }
}