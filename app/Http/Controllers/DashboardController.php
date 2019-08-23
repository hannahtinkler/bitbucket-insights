<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use App\Bitbucket\Services\Merges;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    /**
     * @var Setting
     */
    public $setting;

    /**
     * @var Merges
     */
    public $merges;

    /**
     * @param Setting $setting
     * @param Merges  $merges
     */
    public function __construct(Setting $setting, Merges $merges)
    {
        $this->setting = $setting;
        $this->merges = $merges;
    }

    public function index()
    {
        return view('dashboard.index', [
            'settings' => $this->setting,
            'recentMerges' => $this->merges->recent(),
            'mostMerges' => $this->merges->users(),
            'flaggedMerges' => $this->merges->flagged(),
        ]);
    }
}
