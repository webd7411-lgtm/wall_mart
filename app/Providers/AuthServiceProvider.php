<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->id === 1 || $user->email === 'superadmin@example.com' || empty($user->branch_id)) {
                $hasAdminRole = \Illuminate\Support\Facades\DB::table('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_has_roles.model_id', $user->id)
                    ->whereIn('roles.name', ['Super Admin', 'superAdmin', 'admin'])
                    ->exists();

                if ($hasAdminRole || $user->id === 1 || $user->email === 'superadmin@example.com') {
                    return true;
                }
            }
            return null;
        });
    }
}
