<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Setting;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function index()
    {
        return response()->json(
            $this->setting->values()
        );
    }
}
