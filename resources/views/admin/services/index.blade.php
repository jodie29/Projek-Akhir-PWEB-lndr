@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Manajemen Layanan</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('admin.services.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah Layanan</a>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Kembali ke Dashboard</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-3 py-2 text-left">Nama</th>
                <th class="border px-3 py-2 text-right">Harga / kg</th>
                <th class="border px-3 py-2">Aktif</th>
                <th class="border px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
                <tr>
                    <td class="border px-3 py-2">{{ $service->name }}</td>
                    <td class="border px-3 py-2 text-right">Rp {{ number_format($service->price_per_kg, 2) }}</td>
                    <td class="border px-3 py-2 text-center">{{ $service->active ? 'Ya' : 'Tidak' }}</td>
                    <td class="border px-3 py-2">
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="text-blue-600 mr-2">Edit</a>
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus layanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="border px-3 py-2">Belum ada layanan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $services->links() }}
    </div>
</div>
@endsection
