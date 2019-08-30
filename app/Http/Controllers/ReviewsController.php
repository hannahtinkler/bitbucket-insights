<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use App\Http\Controllers\Controller;
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
    public function __construct(Setting $setting, PullRequests $pullRequests)
    {
        $this->setting = $setting;
        $this->pullRequests = $pullRequests;

        $this->pullRequests->status(PullRequests::STATUS_OPEN);
    }

    public function index()
    {
        return view('reviews.index', [
            'settings' => $this->setting,
            'mostReviewed' => $this->pullRequests->reviewers()->slice(0, 10),
            'readyForMerge' => $this->pullRequests->readyForMerge()->slice(0, 10),
            'notReadyForMerge' => $this->pullRequests->notReadyForMerge()->slice(0, 10),
        ]);
    }
}
