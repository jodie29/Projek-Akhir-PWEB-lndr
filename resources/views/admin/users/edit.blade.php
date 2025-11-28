@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Edit Pengguna</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded shadow">Kembali ke Dashboard</a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white p-6 rounded shadow">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="w-full border px-3 py-2 rounded">
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="courier" {{ old('role', $user->role) == 'courier' ? 'selected' : '' }}>Kurir</option>
                    <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Pelanggan</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Update</button>
                <a href="{{ route('admin.users.index') }}" class="ml-3 px-3 py-2 bg-gray-200 rounded">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
