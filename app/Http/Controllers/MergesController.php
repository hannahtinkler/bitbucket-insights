<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use App\Bitbucket\Services\PullRequests;

class MergesController extends Controller
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

        $this->pullRequests->status(PullRequests::STATUS_MERGED);
    }

    public function index()
    {
        return view('merges.index', [
            'settings' => $this->setting,
            'recentMerges' => $this->pullRequests->recent()->slice(0, 10),
            'mostMerges' => $this->pullRequests->mergers()->slice(0, 10),
            'flaggedMerges' => $this->pullRequests->notReadyForMerge()->slice(0, 10),
        ]);
    }
}
