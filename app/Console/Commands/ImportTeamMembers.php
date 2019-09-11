<?php

namespace App\Console\Commands;

use App\Models\TeamMember;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use App\Bitbucket\Services\TeamMembers;

class ImportTeamMembers extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import:team-members';

    /**
     * @var string
     */
    protected $description = 'Imports team members from Bitbucket and saves them to the database';

    /**
     * @return void
     */
    public function __construct(TeamMembers $teamMembers)
    {
        parent::__construct();
        $this->teamMembers = $teamMembers;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $apiTeamMembers = $this->teamMembers->fetch();

        $this->addNewTeamMembers($apiTeamMembers);
        $this->deleteOldTeamMembers($apiTeamMembers);
    }

    private function addNewTeamMembers(Collection $apiTeamMembers)
    {
        $apiTeamMembers->each(function ($teamMember) {
            TeamMember::updateOrCreate(
                ['external_id' => $teamMember['account_id']],
                ['name' => $teamMember['display_name']]
            );
        });
    }

    private function deleteOldTeamMembers(Collection $apiTeamMembers)
    {
        TeamMember::withoutGlobalScope('active')->get()->filter(function ($teamMember) use ($apiTeamMembers) {
            return !$apiTeamMembers->pluck('account_id')->contains($teamMember['external_id']);
        })->each(function ($teamMember) {
            $teamMember->delete();
        });
    }
}
