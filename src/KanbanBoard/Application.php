<?php
namespace App\KanbanBoard;

use Michelf\Markdown;
use Github\Client as ApiClient;

class Application {

    protected ApiClient $github;
    protected array $repositories;
    public ?array $paused_labels;

	public function __construct( ApiClient $github, array $repositories, ?array $paused_labels = array())
	{
		$this->github = $github;
		$this->repositories = $repositories;
		$this->paused_labels = $paused_labels;
	}

	public function board(): array
	{
        // read Milestones for every Repository
        $data['milestones'] = $this->getMilestones();

        $issues = $this->getIssues();
        $data['issues']=$this->fetchIssuesByStatus($issues);

		return $data;
	}

    protected function getMilestones(): array
    {
        $ms=[];
        if( ! empty($this->repositories)) {
            foreach ($this->repositories as $repository)
            {
                $msByRepo = $this->github->api('issue')->milestones()->all(Utilities::env('GH_ACCOUNT'),$repository);
                if( ! empty($msByRepo)) {
                    foreach($msByRepo as $data) {
                        $ms[$data['id']]=[
                            'id'=>filter_var($data['id'],FILTER_SANITIZE_NUMBER_INT),
                            'number'=>filter_var($data['number'],FILTER_SANITIZE_NUMBER_INT),
                            'repository_name'=>$repository,
                            'title'=>htmlspecialchars($data['title']),
                            'description'=>Markdown::defaultTransform(
                                htmlspecialchars($data['description'])
                            ),
                            'url'=>filter_var($data['url'],FILTER_SANITIZE_URL)
                        ];
                    }
                }
            }

            ksort($ms);
        }

        return $ms;
    }

    protected function getIssues(): array
    {
        $data=[];
        if( ! empty($this->repositories)) {
            foreach ($this->repositories as $repository) {
                $issuesByRepo = $this->github->api('issues')->all(Utilities::env('GH_ACCOUNT'),$repository,['state'=>'all']);
                if( ! empty($issuesByRepo)) {
                    $data = array_merge($data,$issuesByRepo);
                }
            }
        }

        return $data;
    }

    protected function fetchIssuesByStatus(array $issues): array
    {
        $issuesByStatus=[];
        if( ! empty($issues)) {
            foreach($issues as $issue) {
                if ($issue['state']==='closed') {
                    $state='completed';
                } elseif( ! empty($issue['assignee'])) {
                    $state='active';
                } else {
                    $state='queued';
                }
                $issuesByStatus[$state]['data'][]=[
                    'id' => $issue['id'],
                    'number' => $issue['number'],
                    'title' => $issue['title'],
				    'body' => Markdown::defaultTransform($issue['body']),
                    'url' => $issue['html_url'],
                    'progress' => $this->calcIssueProgress($issue['body']),
                    'paused' => $this->isIssuePaused($issue['labels'])
                ];
            }

            if( ! empty($issuesByStatus)) {
                foreach($issuesByStatus as $state=>$issues) {
                    $issuesByStatus[$state]['length']=count($issues['data']);
                }
            }
        }

        return $issuesByStatus;
    }

    protected function calcIssueProgress(): int
    {
        return 0;
    }

    protected function isIssuePaused($issueLabels)
    {
        return false;
    }

	protected function issues($repository, $milestone_id)
	{
		$i = $this->github->issues($repository, $milestone_id);
		foreach ($i as $ii)
		{
			if (isset($ii['pull_request']))
				continue;
			$issues[$ii['state'] === 'closed' ? 'completed' : (($ii['assignee']) ? 'active' : 'queued')][] = array(
				'id' => $ii['id'], 'number' => $ii['number'],
				'title'            	=> $ii['title'],
				'body'             	=> Markdown::defaultTransform($ii['body']),
     'url' => $ii['html_url'],
				'assignee'         	=> (is_array($ii) && array_key_exists('assignee', $ii) && !empty($ii['assignee'])) ? $ii['assignee']['avatar_url'].'?s=16' : NULL,
				'paused'			=> self::labels_match($ii, $this->paused_labels),
				'progress'			=> self::_percent(
											substr_count(strtolower($ii['body']), '[x]'),
											substr_count(strtolower($ii['body']), '[ ]')),
				'closed'			=> $ii['closed_at']
			);
		}
		usort($issues['active'], function ($a, $b) {
			return count($a['paused']) - count($b['paused']) === 0 ? strcmp($a['title'], $b['title']) : count($a['paused']) - count($b['paused']);
		});
		return $issues;
	}
//
//	private static function _state($issue)
//	{
//		if ($issue['state'] === 'closed')
//			return 'completed';
//		else if (Utilities::hasValue($issue, 'assignee') && count($issue['assignee']) > 0)
//			return 'active';
//		else
//			return 'queued';
//	}

//	private static function labels_match($issue, $needles)
//	{
//		if(Utilities::hasValue($issue, 'labels')) {
//			foreach ($issue['labels'] as $label) {
//				if (in_array($label['name'], $needles)) {
//					return array($label['name']);
//				}
//			}
//		}
//		return array();
//
//	}

//	private static function _percent($complete, $remaining)
//	{
//		$total = $complete + $remaining;
//		if($total > 0)
//		{
//			$percent = ($complete OR $remaining) ? round($complete / $total * 100) : 0;
//			return array(
//				'total' => $total,
//				'complete' => $complete,
//				'remaining' => $remaining,
//				'percent' => $percent
//			);
//		}
//		return array();
//	}
}
