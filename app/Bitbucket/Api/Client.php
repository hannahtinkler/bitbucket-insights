<?php

namespace App\Bitbucket\Api;

use GuzzleHttp\Client as Guzzle;
use Bitbucket\Client as Bitbucket;
use Bitbucket\Exception\ClientErrorException;

class Client
{
    /**
     * @var Client
     */
    private $bitbucket;

    /**
     * @var Guzzle
     */
    private $guzzle;

    public function getTeamPullRequests($state = null)
    {
        $pullRequests = $this->getTeamMembers()
            ->map(function ($member) use ($state) {
                return $this->getUserPullRequests($member['account_id'], $state);
            })
            ->flatten(1)
            ->filter();

        return collect($pullRequests);
    }

    public function getUserPullRequests($user, $state = null)
    {
        $query = sprintf('updated_on > "%s"', now()->subDays(7)->format('Y-m-d'));

        if ($state) {
            $query = sprintf('state = "%s"  ', $state) ;
        }

        return collect(
            $this->bitbucket()->pullRequests()->list($user, [
                'pagelen' => 10,
                'q' => $query,
            ])['values']
        )->map(function ($pullRequest) {
            $pullRequest['approvals'] = $this->getPullRequestApprovals($pullRequest);
            return $pullRequest;
        });
    }

    public function getPullRequestApprovals($pullRequest)
    {
        $guzzle = new Guzzle([
            'base_uri' => 'https://api.bitbucket.org',
        ]);

        $url = sprintf(
            '/2.0/repositories/%s/pullrequests/%s',
            $pullRequest['source']['repository']['full_name'],
            $pullRequest['id']
        );

        $response = json_decode(
            $guzzle->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(
                        sprintf('%s:%s', config('services.bitbucket.username'), config('services.bitbucket.password'))
                    ),
                ],
            ])->getBody()
        );

        return array_filter($response->participants, function ($participant) {
            return $participant->approved;
        });
    }

    public function getTeamMembers()
    {
        return collect(
            $this->bitbucket()->teams('e3creative')->members()->list(['pagelen' => 100])['values']
        );
    }

    private function bitbucket()
    {
        if (!$this->bitbucket) {
            $this->bitbucket = new Bitbucket;

            $this->bitbucket()->authenticate(
                Bitbucket::AUTH_HTTP_PASSWORD,
                config('services.bitbucket.username'),
                config('services.bitbucket.password')
            );
        }

        return $this->bitbucket;
    }
}
