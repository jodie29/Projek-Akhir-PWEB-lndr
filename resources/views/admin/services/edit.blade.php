@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Ubah Layanan</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 mb-4">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-medium">Nama</label>
            <input type="text" name="name" value="{{ old('name', $service->name) }}" class="w-full border px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Harga per kg (angka)</label>
            <input type="number" step="0.01" name="price_per_kg" value="{{ old('price_per_kg', $service->price_per_kg) }}" class="w-full border px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="active" {{ $service->active ? 'checked' : '' }}>
                <span class="ml-2">Aktif</span>
            </label>
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-green-600 text-white rounded">Simpan Perubahan</button>
            <a href="{{ route('admin.services.index') }}" class="text-gray-600">Batal</a>
        </div>
    </form>
</div>
@endsection
