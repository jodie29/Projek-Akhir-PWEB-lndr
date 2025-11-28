@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Detail Pengguna</h1>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Detail Pengguna</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded shadow">Kembali ke Dashboard</a>
    </div>
    <div class="bg-white p-6 rounded shadow">
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ $user->role }}</p>
        <p><strong>Telepon:</strong> {{ $user->phone ?? '-' }}</p>
        <p><strong>Alamat:</strong> {{ $user->address ?? '-' }}</p>
        <div class="mt-4">
            <a href="{{ route('admin.users.index') }}" class="px-3 py-1 bg-blue-500 text-white rounded">Kembali</a>
        </div>
    </div>
</div>
@endsection
