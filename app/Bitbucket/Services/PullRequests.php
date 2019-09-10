<?php

namespace App\Bitbucket\Services;

use Carbon\Carbon;
use Bitbucket\Client;
use App\Models\TeamMember;
use App\Models\PullRequest;
use InvalidArgumentException;

class PullRequests
{
    private $branches;

    private $status = 'any';

    private $validStatuses = [
        'any',
        'open',
        'merged',
    ];

    public function __construct(Branches $branches)
    {
        $this->data = collect([]);
        $this->branches = $branches;
    }

    public function fetchByUser(string $externalId, int $count = 10)
    {
        $response = app(Client::class)
            ->pullRequests()
            ->list($externalId, [
                'pagelen' => $count,
                'q' => 'state = "OPEN" OR state = "MERGED"',
            ]);

        return collect($response['values']);
    }

    public function status(string $status)
    {
        if (!in_array($status, $this->validStatuses)) {
            throw new InvalidArgumentException;
        }

        $this->status = $status;

        return $this;
    }

    public function recent()
    {
        return PullRequest::{$this->status}()->orderByDesc('merged_at')->get();
    }

    public function mergers()
    {
        return PullRequest::{$this->status}()
            ->get()
            ->countBy(function ($pullRequest) {
                return $pullRequest->mergedBy->name;
            })
            ->sort()
            ->reverse();
    }

    public function notReadyForMerge()
    {
        return PullRequest::{$this->status}()->get()
            ->filter(function ($pullRequest) {
                return !$this->hasEnoughApprovals($pullRequest);
            });
    }

    public function readyForMerge()
    {
        return PullRequest::{$this->status}()->get()
            ->filter(function ($pullRequest) {
                return $this->hasEnoughApprovals($pullRequest)
                    && !$this->branches->isExempt($pullRequest->branch);
            });
    }

    public function hasEnoughApprovals(PullRequest $pullRequest)
    {
        if ($this->branches->isExempt($pullRequest->branch)) {
            return true;
        } else if ($this->mergedBySenior($pullRequest)) {
            return $pullRequest->approvals->count() >= 3;
        } else if ($this->approvedBySenior($pullRequest)) {
            return $pullRequest->approvals->count() >= 3;
        }

        return false;
    }

    public function mergedBySenior(PullRequest $pullRequest)
    {
        return $pullRequest->merged_by_id
            ? in_array($pullRequest->mergedBy->name, config('services.bitbucket.seniors'))
            : null;
    }

    public function approvedBySenior(PullRequest $pullRequest)
    {
        return $pullRequest->approvals->reduce(function ($carry, $approval) {
            return in_array($approval->teamMember->name, config('services.bitbucket.seniors'))
                ? true
                : $carry;
        }, false);
    }

    public function reviewers()
    {
        return TeamMember::all()
            ->mapWithKeys(function ($teamMember) {
                return [$teamMember->name => $teamMember->approvals->count()];
            })
            ->sort()
            ->reverse();
    }
}
