<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class PullRequest extends Model
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
            $builder->has('author');
        });
    }

    public function approvals()
    {
        return $this->hasMany(PullRequestApproval::class);
    }

    public function author()
    {
        return $this->belongsTo(TeamMember::class, 'author_id');
    }

    public function mergedBy()
    {
        return $this->belongsTo(TeamMember::class, 'merged_by_id');
    }

    public function scopeAny(Builder $query)
    {
        return $query;
    }

    public function scopeOpen(Builder $query)
    {
        return $query->whereNull('merged_by_id');
    }

    public function scopeMerged(Builder $query)
    {
        return $query->whereNotNull('merged_by_id');
    }
}
