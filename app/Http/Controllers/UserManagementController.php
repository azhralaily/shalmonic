<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    // Menampilkan halaman utama manajemen akun
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('settings', compact('users'));
    }

    // Menampilkan halaman form untuk mengedit user
    public function edit(User $user)
    {
        return view('settings-edit', compact('user'));
    }

    // Memproses update data user (termasuk role)
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Ubah baris di bawah ini
            'role' => ['required', 'in:user,admin,operator'], 
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        return redirect()->route('settings.index')->with('status', 'user-updated');
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Ubah baris di bawah ini
            'role' => ['required', 'in:user,admin,operator'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('status', 'user-created');
    }

    // Menghapus user
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('status', 'user-deleted');
    }
}