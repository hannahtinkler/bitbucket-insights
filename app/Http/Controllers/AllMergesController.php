<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Bitbucket\Services\PullRequests;

class AllMergesController extends Controller
{
    private $titles = [
        'mergers' => 'Most merges',
        'recent' => 'Recent merges',
        'not-ready-for-merge' => 'Flagged merges',
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

        $this->pullRequests->status(PullRequests::STATUS_MERGED);
    }

    public function index($type)
    {
        abort_if(!array_key_exists($type, $this->titles), 404);

        $method = Str::camel($type);

        return view('listing.index', [
            'settings' => $this->setting,
            'type' => 'Merged',
            'title' => $this->titles[$type] ?? 'Unknown',
            'data' => $this->pullRequests->$method(),
        ]);
    }
}
