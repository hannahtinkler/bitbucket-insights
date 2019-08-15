<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use App\Services\BitbucketService;

class ImportBitbucketData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:bitbucket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports raw data from Bitbucket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BitbucketService $bitbucketService, Setting $setting)
    {
        parent::__construct();

        $this->bitbucketService = $bitbucketService;
        $this->setting = $setting;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setting->set(Setting::CURRENTLY_REFRESHING, true);

        $merged = $this->bitbucketService->getTeamPullRequests('MERGED');
        $unmerged = $this->bitbucketService->getTeamPullRequests('OPEN');

        cache()->put('bitbucket_pull_requests_merged', $merged);
        cache()->put('bitbucket_pull_requests_unmerged', $unmerged);

        $this->setting->set(Setting::CURRENTLY_REFRESHING, false);
        $this->setting->set(Setting::LAST_REFRESH, now()->format('Y-m-d H:i:s'));
    }
}
