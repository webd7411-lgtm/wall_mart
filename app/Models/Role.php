<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * Relationship: The branch this role is scoped to (null if global)
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Check if this role is global across all branches
     */
    public function isGlobal(): bool
    {
        return is_null($this->branch_id);
    }

    /**
     * Scope query to branch or global
     */
    public function scopeForBranch($query, $branchId = null)
    {
        if (is_null($branchId) || $branchId === 'all') {
            return $query;
        }

        return $query->where(function ($q) use ($branchId) {
            $q->whereNull('branch_id')
              ->orWhere('branch_id', $branchId);
        });
    }
}
