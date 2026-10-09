<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Helpers\BranchHelper;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        $branchId = BranchHelper::getActiveBranchId();

        // If a specific branch is active, filter by that branch
        if ($branchId !== null) {
            $builder->where($model->getTable() . '.branch_id', $branchId);
        }
        // If $branchId is null, it means 'All Branches' mode is active, so no filter is applied.
    }
}
