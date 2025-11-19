<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = User::where('role', 'courier')->orderBy('name')->paginate(15);
        return view('admin.couriers.index', compact('couriers'));
    }

    public function create()
    {
        return view('admin.couriers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'courier',
        ]);

        return redirect()->route('admin.couriers.index')->with('success', 'Kurir berhasil dibuat.');
    }

    public function edit($id)
    {
        $courier = User::where('role', 'courier')->findOrFail($id);
        return view('admin.couriers.edit', compact('courier'));
    }

    public function update(Request $request, $id)
    {
        $courier = User::where('role', 'courier')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $courier->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $courier->name = $data['name'];
        $courier->email = $data['email'];
        if (!empty($data['password'])) {
            $courier->password = Hash::make($data['password']);
        }
        $courier->save();

        return redirect()->route('admin.couriers.index')->with('success', 'Kurir berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $courier = User::where('role', 'courier')->findOrFail($id);
        $courier->delete();
        return redirect()->route('admin.couriers.index')->with('success', 'Kurir berhasil dihapus.');
    }

    public function show($id)
    {
        return redirect()->route('admin.couriers.edit', $id);
    }
}
