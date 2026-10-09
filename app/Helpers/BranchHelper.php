<?php

namespace App\Helpers;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BranchHelper
{
    /**
     * Get the currently active branch ID.
     * Returns null if 'all' branches or not set.
     */
    public static function getActiveBranchId(): ?int
    {
        $user = Auth::user();

        // If user is locked to a specific branch, always return their branch ID
        if ($user && !empty($user->branch_id)) {
            return (int) $user->branch_id;
        }

        $sessionBranch = Session::get('active_branch_id', 'all');

        if ($sessionBranch === 'all' || empty($sessionBranch)) {
            return null;
        }

        return (int) $sessionBranch;
    }

    /**
     * Check if 'All Branches' mode is currently selected.
     */
    public static function isAllBranches(): bool
    {
        return static::getActiveBranchId() === null;
    }

    /**
     * Get the active Branch model instance, if any.
     */
    public static function getActiveBranch(): ?Branch
    {
        $id = static::getActiveBranchId();
        if (!$id) {
            return null;
        }

        return Branch::find($id);
    }

    /**
     * Get all active branches available for switching.
     */
    public static function getAvailableBranches()
    {
        return Branch::where('status', 1)->get();
    }

    /**
     * Determine if the current user can switch branches.
     */
    public static function canSwitchBranch($user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return false;
        }

        // Super Admin / users with null branch_id can switch branches
        return empty($user->branch_id);
    }

    /**
     * Determine if the currently active branch is of type 'restaurant'.
     */
    public static function isRestaurantBranch(): bool
    {
        $branch = static::getActiveBranch();
        return $branch && strtolower(trim($branch->type ?? '')) === 'restaurant';
    }
}

