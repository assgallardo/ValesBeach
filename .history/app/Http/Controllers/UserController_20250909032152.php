<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display the user management page with filters
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->has('role')) {
            $query->byRole($request->role);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Apply search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply sorting
        $sortField = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Get paginated results
        $users = $query->paginate(10)->withQueryString();

        // Get available roles and statuses for filter dropdowns
        $roles = ['admin', 'manager', 'staff', 'guest'];
        $statuses = ['active', 'inactive', 'blocked'];

        return view('admin.user-management-functional', compact('users', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|same:password_confirmation',
            'password_confirmation' => 'required',
            'role' => 'required|in:admin,manager,staff,guest',
            'status' => 'nullable|in:active,inactive,blocked',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'staff',
                'status' => $request->status ?? 'active',
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'User created successfully!',
                    'user' => $user
                ], 201);
            }

            return redirect()->route('admin.users')
                ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error creating user',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Error creating user: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified user
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified user in database
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Define validation rules based on what's being updated
            $rules = [];

            if ($request->has('name')) {
                $rules['name'] = 'required|string|max:255';
            }

            if ($request->has('email')) {
                $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $id;
            }

            if ($request->has('password') && $request->filled('password')) {
                $rules['password'] = 'string|min:6|confirmed';
            }

            if ($request->has('role')) {
                $rules['role'] = 'required|in:admin,manager,staff,guest';
            }

            if ($request->has('status')) {
                $rules['status'] = 'required|in:active,inactive,blocked';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->has('role')) {
                $updateData['role'] = $request->role;
            }

            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully!',
                    'user' => $user
                ]);
            }

            return redirect()->route('admin.users')
                ->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error updating user',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Error updating user: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified user from database
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting your own account
            if ($user->id === auth()->id()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot delete your own account!'
                    ], 403);
                }
                return back()->withErrors(['error' => 'You cannot delete your own account!']);
            }

            $user->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully!'
                ]);
            }

            return redirect()->route('admin.users')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error deleting user',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent toggling your own status
            if ($user->id === auth()->id()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot change your own status!'
                    ], 403);
                }
                return back()->withErrors(['error' => 'You cannot change your own status!']);
            }

            // Don't allow toggling blocked users
            if ($user->status === 'blocked') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot toggle status of blocked users!'
                    ], 403);
                }
                return back()->withErrors(['error' => 'Cannot toggle status of blocked users!']);
            }

            // Prevent non-admin users from modifying admin accounts
            if ($user->role === 'admin' && auth()->user()->role !== 'admin') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have permission to modify admin accounts!'
                    ], 403);
                }
                return back()->withErrors(['error' => 'You do not have permission to modify admin accounts!']);
            }

            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $user->update(['status' => $newStatus]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "User status changed to {$newStatus}!",
                    'user' => $user
                ]);
            }

            return redirect()->route('admin.users')
                ->with('success', "User status changed to {$newStatus}!");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error toggling user status',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Error toggling user status: ' . $e->getMessage()]);
        }
    }

    /**
     * Block a user
     */
    public function blockUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent blocking your own account
            if ($user->id === auth()->id()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot block your own account!'
                    ], 403);
                }
                return back()->withErrors(['error' => 'You cannot block your own account!']);
            }

            $user->update(['status' => 'blocked']);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User has been blocked successfully!',
                    'user' => $user
                ]);
            }

            return redirect()->route('admin.users')
                ->with('success', 'User has been blocked successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Error blocking user',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Error blocking user: ' . $e->getMessage()]);
        }
    }
}
