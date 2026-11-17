<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Helpers\BranchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $currentBranchId = BranchHelper::getActiveBranchId();
        $isBranchLocked = !BranchHelper::canSwitchBranch();

        $query = User::with('branch')
            ->where('email', '!=', 'superadmin@example.com');

        // If an active branch is selected (or user is locked to branch), show only this branch's users
        if ($currentBranchId) {
            $query->where('branch_id', $currentBranchId);
            $allRoles = Role::with('branch')
                ->where(function ($q) use ($currentBranchId) {
                    $q->whereNull('branch_id')->orWhere('branch_id', $currentBranchId);
                })
                ->orderBy('name', 'ASC')
                ->get();
            $branches = Branch::where('id', $currentBranchId)->where('status', 1)->get();
        } else {
            // In All Branches mode, show all users and all roles
            $allRoles = Role::with('branch')->orderBy('name', 'ASC')->get();
            $branches = Branch::where('status', 1)->get();
        }

        $users = $query->get();
        $currentBranch = BranchHelper::getActiveBranch();

        return view('admin_panel.users.users', compact(['users', 'allRoles', 'branches', 'currentBranchId', 'currentBranch']));
    }

    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
        $passwordRule = $editId ? 'nullable' : 'required';
        $currentBranchId = BranchHelper::getActiveBranchId();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$editId,
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

        // Branch assignment logic:
        // 1. If currently in a specific branch (Restaurant or Mart): user is automatically locked to that branch!
        // 2. If in "All Branches" mode: user branch is chosen from dropdown
        if ($currentBranchId) {
            $user->branch_id = $currentBranchId;
        } else {
            $user->branch_id = $request->filled('branch_id') ? (int) $request->branch_id : null;
        }

        $user->save();

        // Safely sync roles under user's branch context
        $user->syncUserRoles($request->roles ?? [], $user->branch_id);

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
        $validator = Validator::make($request->all(), [
            'edit_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::findOrFail($request->edit_id);

        if (!BranchHelper::canSwitchBranch()) {
            $myBranch = BranchHelper::getActiveBranchId();
            if ($user->branch_id != $myBranch) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
        }

        // Safely sync roles under user's branch context
        $user->syncUserRoles($request->roles ?? [], $user->branch_id);

        return response()->json(['success' => 'User roles updated successfully!', 'reload' => true]);
    }
}
