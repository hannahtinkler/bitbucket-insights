<?php

namespace App\Http\Controllers\Ajax;

use Artisan;
use App\Models\Setting;
use App\Http\Controllers\Controller;

class BitbucketDataController extends Controller
{
    public $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function index()
    {
        if (!$this->setting->value(Setting::CURRENTLY_REFRESHING)) {
            Artisan::queue('import:pull-requests');
            Artisan::queue('import:pull-request-approvals');
        }

        return response()->json(['success' => true]);
    }
}
