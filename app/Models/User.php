<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the branch this user is assigned to (null means all branches/global access)
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    /**
     * Get the employee profile associated with this user
     */
    public function employee()
    {
        return $this->hasOne(\App\Models\Hr\Employee::class);
    }

    /**
     * Get the rider profile associated with this user, if any
     */
    public function rider()
    {
        return $this->hasOne(\App\Models\Rider::class, 'user_id');
    }

    /**
     * Check if user's employee profile is active (can login)
     * Returns true if user has no employee profile (admin/non-employee users)
     * Returns false if employee status is non-active or terminated
     */
    public function isEmployeeActive()
    {
        $employee = $this->employee;

        // If no employee profile, user can login (admin users, etc.)
        if (! $employee) {
            return true;
        }

        // Only active employees can login
        return $employee->status === 'active';
    }

    /**
     * Retrieve all roles assigned to this user across teams/branches
     */
    public function getAssignedRoles()
    {
        return \App\Models\Role::join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', get_class($this))
            ->select('roles.*', 'model_has_roles.branch_id as pivot_branch_id')
            ->get();
    }

    /**
     * Get names of all assigned roles
     */
    public function getAssignedRoleNames(): array
    {
        return $this->getAssignedRoles()->pluck('name')->unique()->values()->toArray();
    }

    /**
     * Get IDs of all assigned roles
     */
    public function getAssignedRoleIds(): array
    {
        return $this->getAssignedRoles()->pluck('id')->unique()->values()->toArray();
    }

    /**
     * Safely sync roles under the user's branch context (accepts Role IDs or Role Names)
     */
    public function syncUserRoles(array $roleInputs, $targetBranchId = null)
    {
        \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->delete();

        if (empty($roleInputs)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            return;
        }

        $roles = \App\Models\Role::where(function ($q) use ($roleInputs) {
            $q->whereIn('id', $roleInputs)->orWhereIn('name', $roleInputs);
        })->get();

        foreach ($roles as $role) {
            \Illuminate\Support\Facades\DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'branch_id' => $targetBranchId,
            ]);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
