<?php
namespace App\KanbanBoard;

use Michelf\Markdown;
use Github\Client as ApiClient;

class Application {

    protected ApiClient $github;
    protected ClientInterface $client;
    protected array $repositories;
    public ?array $paused_labels;

	public function __construct( ClientInterface $client, array $repositories, ?array $paused_labels = array())
	{
		$this->client = $client;
		$this->repositories = $repositories;
		$this->paused_labels = $paused_labels;
	}

	public function board(): array
	{
        $data=[];
        if( ! empty($this->repositories)) {
            foreach ($this->repositories as $repositoryName) {

                $milestones = $this->client->getMilestones($repositoryName);

                if( ! empty($milestones)) {
                    foreach($milestones as $ms) {
                        $issues = $this->client->getIssues($repositoryName,$ms['number']);
                        $data[] = [
                            'data'=>$ms,
                            'issues'=>Utilities::fetchIssuesByStatus($issues)
                        ];
                    }
                }

            }
        }

		return ['milestones'=>$data];
	}
//
//    protected function _getMilestonesByRepo(string $repositoryName): ?array
//    {
//        $ms=[];
//
//        $msByRepo = $this->github->api('issue')->milestones()->all(
//            Utilities::env('GH_ACCOUNT'),
//            $repositoryName
//        );
//
//        if( ! empty($msByRepo)) {
//            foreach($msByRepo as $data) {
//                $title=htmlspecialchars($data['title']);
//                $ms[$title]=[
//                    'title'=>$title,
//                    'number'=>$data['number'],
//                    'description'=>Markdown::defaultTransform(
//                        htmlspecialchars($data['description'])
//                    ),
//                    'open_issues'=>$data['open_issues'],
//                    'closed_issues'=>$data['closed_issues'],
//                    'url'=>filter_var($data['html_url'],FILTER_SANITIZE_URL),
//                    'progress' => Utilities::calcProgress($data['closed_issues'],$data['open_issues'])
//                ];
//            }
//        }
//
//        ksort($ms);
//
//        return array_values($ms);
//    }
//
//    protected function getMilestonesWithIssues(): array
//    {
//        $milestones = $this->getMilestones();
//
//        foreach($milestones as $msKey=>$milestone) {
//            $milestones[$msKey]['issues'] = $this->getIssues($milestone['number']);
//        }
//
//        return $milestones;
//    }
//
//    protected function getMilestones(): array
//    {
//        $ms=[];
//        if( ! empty($this->repositories)) {
//            foreach ($this->repositories as $repository)
//            {
//                $msByRepo = $this->github->api('issue')->milestones()->all(
//                    Utilities::env('GH_ACCOUNT'),
//                    $repository
//                );
//
//                if( ! empty($msByRepo)) {
//                    foreach($msByRepo as $data) {
//                        $title=htmlspecialchars($data['title']);
//                        $ms[$title]=[
//                            'milestone'=>$title,
//                            'number'=>$data['number'],
//                            'node_id'=>$data['node_id'],
//                            'id'=>htmlspecialchars($data['id']),
//                            'description'=>Markdown::defaultTransform(
//                                htmlspecialchars($data['description'])
//                            ),
//                            'open_issues'=>$data['open_issues'],
//                            'closed_issues'=>$data['closed_issues'],
//                            'url'=>filter_var($data['html_url'],FILTER_SANITIZE_URL),
//                            'progress' => Utilities::calcProgress($data['closed_issues'],$data['open_issues'])
//                        ];
//                    }
//                }
//            }
//        }
//
//        ksort($ms);
//
//        return array_values($ms);
//    }
//
//    protected function getIssues($milestoneNumber=null): array
//    {
//        $data=[];
//
//        if( ! empty($this->repositories)) {
//            foreach ($this->repositories as $repository) {
//                $params['state']='all';
//                $params['milestone']=$milestoneNumber;
//                $issuesByRepo = $this->github->api('issues')->all(Utilities::env('GH_ACCOUNT'),$repository,$params);
//                if( ! empty($issuesByRepo)) {
//                    $data = array_merge($data,$issuesByRepo);
//                }
//            }
//        }
//
//        return $data;
//    }
//
//    protected function isIssuePaused($issueLabels)
//    {
//        foreach($issueLabels as $issueLabel) {
//            if(in_array($issueLabel['name'],$this->paused_labels)) {
//                return true;
//            }
//        }
//
//        return false;
//    }
}
