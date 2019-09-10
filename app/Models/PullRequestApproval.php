<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class PullRequestApproval extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('activeTeamMember', function (Builder $builder) {
            $builder->has('teamMember');
        });
    }

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }
}
