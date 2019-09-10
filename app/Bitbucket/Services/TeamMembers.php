<?php

namespace App\Bitbucket\Services;

use Bitbucket\Client;
use App\Models\TeamMember;

class TeamMembers
{
    public function fetch()
    {
        $teamMembers = app(Client::class)
            ->teams('e3creative')
            ->members()
            ->list(['pagelen' => 100]);

        return collect($teamMembers['values']);
    }
}
