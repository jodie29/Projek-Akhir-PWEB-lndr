<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'role' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        // Jika password diberikan, hash sebelum update
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
