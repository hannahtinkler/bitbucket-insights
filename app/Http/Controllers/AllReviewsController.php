<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Bitbucket\Services\PullRequests;

class AllReviewsController extends Controller
{
    private $titles = [
        'reviewers' => 'Most reviewed',
        'ready-for-merge' => 'Ready for merge',
        'not-ready-for-merge' => 'Require more reviews',
    ];

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

    public function index($type)
    {
        abort_if(!array_key_exists($type, $this->titles), 404);

        $method = Str::camel($type);

        return view('listing.index', [
            'settings' => $this->setting,
            'type' => 'Open',
            'title' => $this->titles[$type] ?? 'Unknown',
            'data' => $this->pullRequests->$method(),
        ]);
    }
}
