<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use SoftDeletes;

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', true);
        });
    }

    /**
     * @var array
     */
    protected $guarded = [];

    public function approvals()
    {
        return $this->hasMany(PullRequestApproval::class);
    }
}
