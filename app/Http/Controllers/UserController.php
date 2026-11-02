<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Helpers\BranchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $currentBranchId = BranchHelper::getActiveBranchId();
        $isBranchLocked = !BranchHelper::canSwitchBranch();

        $query = User::with('branch')
            ->where('email', '!=', 'superadmin@example.com')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'superadmin');
            });

        if ($isBranchLocked && $currentBranchId) {
            $query->where('branch_id', $currentBranchId);
        }

        $users = $query->get();
        $allRoles = Role::all();
        $branches = $isBranchLocked 
            ? Branch::where('id', $currentBranchId)->where('status', 1)->get()
            : Branch::where('status', 1)->get();

        return view('admin_panel.users.users', compact(['users', 'allRoles', 'branches']));
    }

    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
        $passwordRule = $editId ? 'nullable' : 'required';

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$request->edit_id,
            'password' => $passwordRule,
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if (! empty($editId)) {
            $user = User::findOrFail($editId);

            // If user is locked to a branch, verify they can only edit users of their branch
            if (!BranchHelper::canSwitchBranch()) {
                $myBranch = BranchHelper::getActiveBranchId();
                if ($user->branch_id != $myBranch) {
                    return response()->json(['errors' => ['branch_id' => ['Unauthorized to modify user from another branch.']]], 403);
                }
            }

            $msg = [
                'success' => 'User Updated Successfully',
                'reload' => true,
            ];
        } else {
            $user = new User;
            $msg = [
                'success' => 'User Created Successfully',
                'redirect' => route('users.index'),
            ];
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Branch assignment logic
        if (!BranchHelper::canSwitchBranch()) {
            $user->branch_id = BranchHelper::getActiveBranchId();
        } else {
            $user->branch_id = $request->filled('branch_id') ? $request->branch_id : null;
        }

        $user->save();

        // Sync roles under the user's assigned branch team context
        $currentSessionBranch = BranchHelper::getActiveBranchId();
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($user->branch_id);
        }

        $user->syncRoles($request->roles ?? []);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($currentSessionBranch);
        }

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
        $user = User::findOrFail($id);

        if (!BranchHelper::canSwitchBranch()) {
            $myBranch = BranchHelper::getActiveBranchId();
            if ($user->branch_id != $myBranch) {
                return redirect()->route('users.index')->with('error', 'Unauthorized action.');
            }
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');

    }

    public function updateRoles(Request $request)
    {
        $user = User::findOrFail($request->edit_id);

        if (!BranchHelper::canSwitchBranch()) {
            $myBranch = BranchHelper::getActiveBranchId();
            if ($user->branch_id != $myBranch) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
        }

        // Assign new roles (by name) under user's branch context
        $currentSessionBranch = BranchHelper::getActiveBranchId();
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($user->branch_id);
        }

        $user->syncRoles($request->roles ?? []);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($currentSessionBranch);
        }

        // Return JSON so AJAX handlers get a clear response
        return response()->json(['success' => 'User roles updated successfully!', 'reload' => true]);
    }
}
