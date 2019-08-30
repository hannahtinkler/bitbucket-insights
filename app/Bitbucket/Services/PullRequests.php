<?php

namespace App\Bitbucket\Services;

use Carbon\Carbon;

class PullRequests
{
    const STATUS_OPEN = 'bitbucket_pull_requests_unmerged';
    const STATUS_MERGED = 'bitbucket_pull_requests_merged';

    public $data;
    public $branches;

    public function __construct(Branches $branches)
    {
        $this->data = cache()->get(self::STATUS_OPEN);
        $this->branches = $branches;
    }

    public function status(string $status)
    {
        $this->data = cache()->get($status);
    }

    public function recent($count = 10)
    {
        return $this->data->sortByDesc(function ($merge) {
            return Carbon::parse($merge['updated_on']);
        });
    }

    public function reviewers()
    {
        return $this->data
            ->map(function ($merge) {
                return $merge['approvals'];
            })
            ->flatten(1)
            ->countBy(function ($approval) {
                return $approval->user->display_name;
            })
            ->sort()
            ->reverse();
    }

    public function mergers($count = 10)
    {
        return $this->data->countBy(function ($merge) {
            return $merge['closed_by']['display_name'];
        })->sort()->reverse();
    }

    public function notReadyForMerge()
    {
        return $this->data->filter(function ($merge) {
            return !$this->hasEnoughApprovals($merge);
        });
    }

    public function readyForMerge()
    {
        return $this->data->filter(function ($merge) {
            return $this->hasEnoughApprovals($merge)
                && !$this->branches->isExempt($merge['source']['branch']['name']);
        });
    }

    public function hasEnoughApprovals(array $merge)
    {
        if ($this->branches->isExempt($merge['source']['branch']['name'])) {
            return true;
        } else if ($this->mergedBySenior($merge)) {
            return count($merge['approvals']) >= 3;
        } else if ($this->approvedBySenior($merge)) {
            return count($merge['approvals']) >= 3;
        }

        return false;
    }

    public function mergedBySenior(array $merge)
    {
        return in_array($merge['closed_by']['display_name'], config('services.bitbucket.seniors'));
    }

    public function approvedBySenior(array $merge)
    {
        return array_reduce($merge['approvals'], function ($carry, $approval) {
            return in_array($approval->user->display_name, config('services.bitbucket.seniors'))
                ? true
                : $carry;
        }, false);
    }
}
