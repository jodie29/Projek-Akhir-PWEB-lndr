@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">

    <div class="flex flex-col md:flex-row items-center gap-8 mb-4">
        <x-header-illustration title="Kelola Pengguna" :image="'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png'"/>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded shadow">Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-3 py-2">Nama</th>
                <th class="border px-3 py-2">Email</th>
                <th class="border px-3 py-2">Role</th>
                <th class="border px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="border px-3 py-2">{{ $user->name }}</td>
                    <td class="border px-3 py-2">{{ $user->email }}</td>
                    <td class="border px-3 py-2">{{ $user->role }}</td>
                    <td class="border px-3 py-2">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Lihat</a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="px-3 py-1 bg-yellow-400 text-white rounded">Edit</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-600 text-white rounded" onclick="return confirm('Hapus pengguna ini?')">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="border px-3 py-2">Tidak ada pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection

