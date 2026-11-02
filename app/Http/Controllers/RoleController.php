<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Helpers\BranchHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
     public function index()
    {
        $activeBranchId = BranchHelper::getActiveBranchId();
        $rolesQuery = Role::with('branch')->orderBy('name', "ASC");

        if (!BranchHelper::canSwitchBranch() && $activeBranchId) {
            $rolesQuery->where(function ($q) use ($activeBranchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $activeBranchId);
            });
        }

        $roles = $rolesQuery->get();
        $branches = Branch::where('status', 1)->get();
        $allPermissions = Permission::orderBy('name', 'ASC')->get();

        return view('admin_panel.roles.role', compact(['roles', 'allPermissions', 'branches']));
    }

    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(function ($query) use ($branchId) {
                    return $branchId ? $query->where('branch_id', $branchId) : $query->whereNull('branch_id');
                })->ignore($editId)
            ],
            'branch_id' => 'nullable|exists:branches,id',
        ], [
            'name.unique' => 'A role with this name already exists for the selected branch/global scope.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if (!empty($editId)) {
            $role = Role::findOrFail($editId);
            $msg = [
                'success' => 'Role Updated Successfully',
                'reload' => true
            ];
        } else {
            $role = new Role();
            $role->guard_name = 'web';
            $msg = [
                'success' => 'Role Created Successfully',
                'redirect' => route('roles.index')
            ];
        }

        $role->name = $request->name;
        $role->branch_id = $branchId;
        $role->save();

        return response()->json($msg);
    }

    /**
     * Display the specified resource.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');

    }

    public function updatePermissions(Request $request)
    {
        // $request->validate([
        //     'edit_id' => 'required|exists:roles,id',
        //     'permissions' => 'array',
        //     'permissions.*' => 'exists:permissions,name'
        // ]);
        // dd($request->toArray());
        $role = Role::findOrFail($request->edit_id);

        // Assign new roles (by name)
        $role->syncPermissions($request->permissions ?? []);

        return back()->with('success', 'Role permissions updated successfully!');
    }
}
