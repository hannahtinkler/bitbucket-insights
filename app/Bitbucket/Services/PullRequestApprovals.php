<?php

namespace App\Bitbucket\Services;

use GuzzleHttp\Client;
use App\Models\PullRequestApproval;

class PullRequestApprovals
{
    public function fetchByPullRequest(string $repository, int $id)
    {
        $url = sprintf(
            'https://api.bitbucket.org/2.0/repositories/%s/pullrequests/%s',
            $repository,
            $id
        );

        $response = (new Client)->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(
                    sprintf('%s:%s', config('services.bitbucket.username'), config('services.bitbucket.password'))
                ),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return collect($data['participants'])->filter(function ($participant) {
            return $participant['approved'];
        });
    }
}
