<?php

namespace App\Bitbucket\Services;

use Carbon\Carbon;

class Merges
{
    public $data;
    public $branches;

    public function __construct(Branches $branches)
    {
        $this->data = cache()->get('bitbucket_pull_requests_merged');
        $this->branches = $branches;
    }

    public function recent($count = 10)
    {
        return $this->data->sortByDesc(function ($merge) {
            return Carbon::parse($merge['updated_on']);
        })->slice(0, $count);
    }

    public function users($count = 10)
    {
        return $this->data->countBy(function ($merge) {
            return $merge['closed_by']['display_name'];
        })->sort()->reverse()->slice(0, $count);
    }

    public function flagged()
    {
        return $this->data->filter(function ($merge) {
            return !$this->hasEnoughApprovals($merge);
        });
    }

    public function hasEnoughApprovals(array $merge)
    {
        if ($this->branches->isExempt($merge['source']['branch']['name'])) {
            return count($merge['approvals']) >= 1;
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
