@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Manajemen Kurir</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('admin.couriers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah Kurir</a>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Kembali ke Dashboard</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-3 py-2 text-left">Nama</th>
                <th class="border px-3 py-2">Email</th>
                <th class="border px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($couriers as $courier)
                <tr>
                    <td class="border px-3 py-2">{{ $courier->name }}</td>
                    <td class="border px-3 py-2">{{ $courier->email }}</td>
                    <td class="border px-3 py-2">
                        <a href="{{ route('admin.couriers.edit', $courier->id) }}" class="text-blue-600 mr-2">Edit</a>
                        <form action="{{ route('admin.couriers.destroy', $courier->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus kurir ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="border px-3 py-2">Belum ada kurir.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $couriers->links() }}
    </div>
</div>
@endsection
