<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\TeamMember;
use App\Models\PullRequest;
use Illuminate\Console\Command;
use App\Console\Traits\Skippable;
use Illuminate\Support\Collection;
use App\Bitbucket\Services\PullRequests;

class ImportPullRequests extends Command
{
    use Skippable;

    /**
     * @var string
     */
    protected $signature = 'import:pull-requests {--count=} {--skip=}';

    /**
     * @var string
     */
    protected $description = 'Imports pull requests from Bitbucket and saves them to the database';

    /**
     * @return void
     */
    public function __construct(PullRequests $pullRequests)
    {
        parent::__construct();
        $this->pullRequests = $pullRequests;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $teamMembers = TeamMember::all();

        if (!$teamMembers) {
            return;
        }

        $this->bar = $this->output->createProgressBar($teamMembers->count());
        $this->bar->start();

        $teamMembers->each(function ($teamMember, $i) {
            if (!$this->shouldSkip($i)) {
                $this->updatePullRequestData(
                    $this->pullRequests->fetchByUser(
                        $teamMember['external_id'],
                        $this->option('count') ?: 10
                    )
                );
            }

            $this->bar->advance();
        });

        $this->bar->finish();
    }

    private function updatePullRequestData(Collection $pullRequests)
    {
        $pullRequests->each(function ($pullRequest) {
            $mergedBy = TeamMember::whereName($pullRequest['closed_by']['display_name'])->first();
            $author = TeamMember::whereName($pullRequest['author']['display_name'])->first();

            if (!$author) {
                return;
            }

            PullRequest::updateOrCreate(
                [
                    'external_id' => $pullRequest['id'],
                    'repository' => $pullRequest['destination']['repository']['full_name'],
                ],
                [
                    'title' => $pullRequest['title'],
                    'comment_count' => $pullRequest['comment_count'],
                    'branch' => $pullRequest['source']['branch']['name'],
                    'task_count' => $pullRequest['task_count'],
                    'author_id' => $author->id,
                    'merged_by_id' => $pullRequest['state'] === 'MERGED' ? optional($mergedBy)->id : null,
                    'merged_at' => $pullRequest['state'] === 'MERGED' ? Carbon::parse($pullRequest['updated_on']) : null,
                    'url' => $pullRequest['links']['html']['href'],
                ]
            );
        });
    }
}
