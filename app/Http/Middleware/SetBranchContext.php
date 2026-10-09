<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Helpers\BranchHelper;

class SetBranchContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // If user has a locked branch, enforce it in session
            if (!empty($user->branch_id)) {
                Session::put('active_branch_id', $user->branch_id);
            } elseif (!Session::has('active_branch_id')) {
                // Default for superadmin is 'all'
                Session::put('active_branch_id', 'all');
            }

            // Share context variables with all Blade views
            View::share('current_branch_id', BranchHelper::getActiveBranchId());
            View::share('is_all_branches', BranchHelper::isAllBranches());
            View::share('current_branch', BranchHelper::getActiveBranch());
            View::share('can_switch_branch', BranchHelper::canSwitchBranch($user));
            View::share('available_branches', BranchHelper::getAvailableBranches());
            View::share('is_restaurant_branch', BranchHelper::isRestaurantBranch());
        }

        return $next($request);
    }
}
