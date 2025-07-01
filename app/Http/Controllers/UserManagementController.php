<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        $users = $query->get();
        return view('manage_accounts.index', compact('users'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->role && in_array($request->role, ['admin','operator','guest'])) {
            $user->role = $request->role;
        }
        $user->save();
        return back()->with('success', 'User updated!');
    }

    public function delete(Request $request)
    {
        $user = User::findOrFail($request->id);
        if ($user->id == auth()->id()) {
            return back()->with('error', 'Cannot delete yourself!');
        }
        $user->delete();
        return back()->with('success', 'User deleted!');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role && in_array($request->role, ['admin','operator','guest']) ? $request->role : 'guest',
        ]);
        return back()->with('success', 'User created!');
    }
} 