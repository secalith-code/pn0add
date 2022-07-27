<?php
namespace App\KanbanBoard;

use Exception;
use Michelf\Markdown;

class Utilities
{
	public static function env($name, $default = NULL)
    {
        try {
            $value = getenv($name);
            if( ! $value && isset($_SERVER[$name])) {
                return $_SERVER[$name];
            } elseif($default !== NULL) {
                return $default;
            } else {
                throw new Exception(
                    sprintf(
                        "Environment variable %s not found or has no value",
                        $name
                    )
                );
            }
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

	public static function hasValue($array, $key) {
		return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
	}

    public static function calcProgress($complete,$remaining): null|array
    {
        $total = $complete + $remaining;
        if($total > 0)
        {
            $percent = ($complete || $remaining) ? round($complete / $total * 100) : 0;

            return array(
                'total' => $total,
                'complete' => $complete,
                'remaining' => $remaining,
                'percent' => $percent
            );
        }
        return null;
    }

    /**
     * Fetch
     * Milestone may have no issues.
     *
     * @param array|null $issues
     * @return array
     */
    public static function fetchIssuesByStatus(?array $issues): array
    {
        $data=[];
        if( ! empty($issues)) {
            foreach($issues as $issue) {
                $state = self::getIssueState($issue);

                $data[$state]['data'][]=Utilities::fetchIssue($issue);
            }

            if( ! empty($data)) {
                foreach($data as $state=>$issues) {
                    $data[$state]['length']=count($issues['data']);
                }
            }
        }

        return $data;
    }

    public static function getIssueState( array $issue): string
    {
        if ($issue['state']==='closed') {
            $state='completed';
        } elseif( ! empty($issue['assignee'])) {
            $state='active';
        } else {
            $state='queued';
        }

        return $state;
    }

    public static function fetchIssue(array $issue): array
    {
        $data = [
                'id' => $issue['id'],
                'number' => $issue['number'],
                'title' => $issue['title'],
                'body' => trim(Markdown::defaultTransform(strip_tags(trim(nl2br($issue['body'],false))))),
                'url' => $issue['html_url'],
                'progress' => self::calcProgress(
                    substr_count(strtolower($issue['body']), '[x]'),
                    substr_count(strtolower($issue['body']), '[ ]')
                ),
                'paused' => self::isIssuePaused($issue['labels']),
                'state' => $issue['state'],
            ];

        return $data;
    }

    protected static function isIssuePaused($issueLabels): bool
    {
        foreach($issueLabels as $issueLabel) {
            if(in_array($issueLabel['name'],$issueLabels)) {
                return true;
            }
        }

        return false;
    }
}