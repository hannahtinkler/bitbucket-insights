<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use App\Bitbucket\Services\TeamMembers;
use App\Bitbucket\Services\PullRequests;

class ReviewsController extends Controller
{

    /**
     * @var Setting
     */
    public $setting;

    /**
     * @var PullRequests
     */
    public $pullRequests;

    /**
     * @param Setting $setting
     * @param PullRequests  $pullRequests
     */
    public function __construct(
        Setting $setting,
        PullRequests $pullRequests,
        TeamMembers $teamMembers
    ) {
        $this->setting = $setting;
        $this->pullRequests = $pullRequests;
        $this->teamMembers = $teamMembers;
    }

    public function index()
    {
        return view('reviews.index', [
            'settings' => $this->setting,
            'mostReviewed' => $this->pullRequests->reviewers()->slice(0, 10),
            'readyForMerge' => $this->pullRequests->status('open')->readyForMerge()->slice(0, 10),
            'notReadyForMerge' => $this->pullRequests->status('open')->notReadyForMerge()->slice(0, 10),
        ]);
    }
}
