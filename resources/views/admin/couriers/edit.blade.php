@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Ubah Kurir</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 mb-4">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.couriers.update', $courier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-medium">Nama</label>
            <input type="text" name="name" value="{{ old('name', $courier->name) }}" class="w-full border px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $courier->email) }}" class="w-full border px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Password (kosong = tidak diubah)</label>
            <input type="password" name="password" class="w-full border px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block font-medium">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full border px-3 py-2">
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-green-600 text-white rounded">Simpan Perubahan</button>
            <a href="{{ route('admin.couriers.index') }}" class="text-gray-600">Batal</a>
        </div>
    </form>
</div>
@endsection
