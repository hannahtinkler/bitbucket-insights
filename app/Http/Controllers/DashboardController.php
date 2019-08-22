<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Bitbucket\Client;
use App\Models\Setting;
use App\Services\BitbucketService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    private $exemptBranchTypes = [
        'candidate',
        'release',
        'revert',
    ];

    /**
     * @var Setting
     */
    public $setting;

    /**
     * @param Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function index()
    {
        $mergedPullRequests = cache()->get('bitbucket_pull_requests_merged');
        $unmergedPullRequests = cache()->get('bitbucket_pull_requests_unmerged');

        return view('dashboard.index', [
            'settings' => $this->setting,

            'recentMerges' => $mergedPullRequests->sortByDesc(function ($a) {
                return Carbon::parse($a['updated_on']);
            })->slice(0, 10),

            'mostMerges' => $mergedPullRequests->countBy(function ($pullRequest) {
                return $pullRequest['closed_by']['display_name'];
            })->sort()->reverse()->slice(0, 10),

            'flaggedMerges' => $mergedPullRequests->filter(function ($pullRequest) {
                $branchType = strtolower(explode('/', $pullRequest['source']['branch']['name'])[0]);

                $nonMergerApprovers = array_filter($pullRequest['approvals'], function ($approver) use ($pullRequest) {
                    return $approver->user->display_name !== $pullRequest['closed_by']['display_name'];
                });

                return !(
                    (count($pullRequest['approvals']) >= 1 && in_array($branchType, $this->exemptBranchTypes)) // Exempt branch
                        || (count($pullRequest['approvals']) >= 1 && preg_match('/^revert-pr-\d*$/', $branchType)) // Revert branch
                        || (count($nonMergerApprovers) >= 2 && in_array($pullRequest['closed_by']['display_name'], config('services.bitbucket.seniors'))) // Merged by senior and has 2 approvals
                        || (count($nonMergerApprovers) >= 3 && !in_array($pullRequest['closed_by']['display_name'], config('services.bitbucket.seniors'))) // Merged by senior and has 2 approvals
                );
            })
        ]);
    }
}
