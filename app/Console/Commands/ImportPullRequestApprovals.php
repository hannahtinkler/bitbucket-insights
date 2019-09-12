<?php

namespace App\Console\Commands;

use App\Models\TeamMember;
use App\Models\PullRequest;
use Illuminate\Console\Command;
use App\Console\Traits\Skippable;
use Illuminate\Support\Collection;
use App\Models\PullRequestApproval;
use App\Bitbucket\Services\PullRequestApprovals;

class ImportPullRequestApprovals extends Command
{
    use Skippable;

    /**
     * @var string
     */
    protected $signature = 'import:pull-request-approvals {--state=} {--skip=}';

    /**
     * @var string
     */
    protected $description = 'Imports pull request approvals from Bitbucket and saves them to the database';

    /**
     * @return void
     */
    public function __construct(PullRequestApprovals $pullRequestApprovals)
    {
        parent::__construct();
        $this->pullRequestApprovals = $pullRequestApprovals;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $pullRequests = new PullRequest;

        if ($this->option('state') === 'merged') {
            $pullRequests = $pullRequests->merged();
        } else {
            $pullRequests = $pullRequests->open();
        }

        if (!$pullRequests = $pullRequests->get()) {
            return;
        }

        $this->bar = $this->output->createProgressBar($pullRequests->count());
        $this->bar->start();

        $pullRequests->each(function ($pullRequest, $i) {
            if (!$this->shouldSkip($i)) {
                $this->updatePullRequestApprovalData(
                    $pullRequest,
                    $this->pullRequestApprovals->fetchByPullRequest(
                        $pullRequest['repository'],
                        $pullRequest['external_id']
                    )
                );
            }

            $this->bar->advance();
        });

        $this->bar->finish();
    }

    private function updatePullRequestApprovalData(PullRequest $pullRequest, Collection $pullRequestApprovals)
    {
        $pullRequest->approvals->map->delete();

        $pullRequestApprovals->each(function ($pullRequestApproval) use ($pullRequest) {
            $teamMember = TeamMember::withoutGlobalScope('active')->whereName($pullRequestApproval['user']['display_name'])->first();

            PullRequestApproval::create([
                'pull_request_id' => $pullRequest->id,
                'team_member_id' => optional($teamMember)->id,
            ]);
        });
    }
}
