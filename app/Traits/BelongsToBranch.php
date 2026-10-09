<?php

namespace App\Traits;

use App\Scopes\BranchScope;
use App\Models\Branch;
use App\Helpers\BranchHelper;

trait BelongsToBranch
{
    /**
     * Boot the BelongsToBranch trait for a model.
     */
    public static function bootBelongsToBranch()
    {
        static::addGlobalScope(new BranchScope());

        static::creating(function ($model) {
            if (empty($model->branch_id)) {
                $branchId = BranchHelper::getActiveBranchId();
                if ($branchId !== null) {
                    $model->branch_id = $branchId;
                } else {
                    $user = auth()->user();
                    $model->branch_id = ($user && !empty($user->branch_id)) ? $user->branch_id : 1;
                }
            }
        });
    }

    /**
     * Relationship to Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Scope a query to ignore the branch scope.
     */
    public function scopeWithoutBranch($query)
    {
        return $query->withoutGlobalScope(BranchScope::class);
    }
}
