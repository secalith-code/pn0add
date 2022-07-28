<?php

namespace App\KanbanBoard;

use App\KanbanBoard\CommonModel;
use App\Utilities;
use Michelf\Markdown;

class IssueModel extends CommonModel
{

    /**
     *  Hydrates Issue data
     *
     * @param  $item
     * @return array
     */
    public function fetchOne($item): array
    {
        return [
            'id' => (int) $item['id'],
            'title' => (string) $item['title'],
            'body' => trim($item['body']),
            'html_url' => filter_var($item['html_url'], FILTER_SANITIZE_URL),
            'user_avatar' => $item['user']['avatar_url'],
            'progress' => Utilities::calcProgress(
                substr_count(strtolower($item['body']), '[x]'),
                substr_count(strtolower($item['body']), '[ ]')
            ),
            'paused' => $this->isIssuePaused($item['labels']),
            'state' => $item['state'],
            'assignee' => $this->fetchAssignee($item['assignee']),
        ];
    }

    /**
     * @param  $assignee
     * @return array|null
     */
    protected function fetchAssignee($assignee): ?array
    {
        if (! empty($assignee)) {
            return [
                'login' => $assignee['login'],
                'avatar_url' => $assignee['avatar_url'],
            ];
        }

        return null;
    }

    /**
     * @todo   Investigate if nullable argument is needed, maybe it should be an array?
     * @param  array|null $issues
     * @return array
     */
    public function fetchIssuesByStatus(?array $issues): array
    {
        $data = [];

        if (! empty($issues)) {
            foreach ($issues as $iss) {
                $state = $this->getIssueState($iss);
                $data[$state]['data'][] = $iss;
            }

            // count how many issues
            if (! empty($data)) {
                foreach ($data as $state => $issues) {
                    $data[$state]['length'] = count($issues['data']);
                }
            }
        }

        return $data;
    }

    /**
     * @param  array $issue
     * @return string
     */
    protected function getIssueState(array $issue): string
    {
        if ($issue['state'] === 'closed') {
            $state = 'completed';
        } elseif (! empty($issue['assignee'])) {
            $state = 'active';
        } else {
            $state = 'queued';
        }

        return $state;
    }

    /**
     * @param  $issueLabels
     * @return bool
     */
    protected function isIssuePaused($issueLabels): bool
    {
        if (! empty($issueLabels)) {
            foreach ($issueLabels as $issueLabel) {
                if (in_array($issueLabel['name'], ['paused'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
