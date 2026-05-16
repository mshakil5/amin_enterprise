<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function checkPermission($permissionId)
    {
        $user = auth()->user();
        if (!$user || !$user->role) {
            return false;
        }
        
        if ($user->role->name === "All Access") {
            return true;
        }
        
        $permissions = json_decode($user->role->permission, true) ?? [];
        return in_array($permissionId, $permissions);
    }

    public function getAdmin()
    {
        if (!$this->checkPermission('2')) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if (Auth::user()->role->name == "All Access") {
            $admins = User::where('is_type', '1')->orderby('id', 'DESC')->get();
        } else {
            $admins = User::where('is_type', '1')->where('id', Auth::user()->id)->orderby('id', 'DESC')->get();
        }

        $roles = Role::orderby('id', 'DESC')->get();
        
        return view('admin.admin.index', compact('admins', 'roles'));
    }

    public function adminStore(Request $request)
    {
        if (!$this->checkPermission('2')) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:4|confirmed',
            'role_id' => 'required|exists:roles,id',
            'house_number' => 'nullable|string|max:50',
            'street_name' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'is_type' => '1',
                'house_number' => $validated['house_number'] ?? null,
                'street_name' => $validated['street_name'] ?? null,
                'town' => $validated['town'] ?? null,
                'postcode' => $validated['postcode'] ?? null,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Admin created successfully.',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminEdit($id)
    {
        if (!$this->checkPermission('2')) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission.'
            ], 403);
        }

        $user = User::findOrFail($id);
        
        return response()->json([
            'status' => 200,
            'data' => $user
        ]);
    }

    public function adminUpdate(Request $request, $id)
    {
        if (!$this->checkPermission('2')) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:4|confirmed',
            'role_id' => 'required|exists:roles,id',
            'house_number' => 'nullable|string|max:50',
            'street_name' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
        ]);

        try {
            $user->update([
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role_id' => $validated['role_id'],
                'house_number' => $validated['house_number'] ?? null,
                'street_name' => $validated['street_name'] ?? null,
                'town' => $validated['town'] ?? null,
                'postcode' => $validated['postcode'] ?? null,
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Admin updated successfully.',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminDelete($id)
    {
        if (!$this->checkPermission('2')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        // Prevent self-deletion
        if (Auth::id() == $id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}