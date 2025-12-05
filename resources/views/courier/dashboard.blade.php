@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-linear-to-br from-white via-blue-50 to-blue-100 shadow-2xl rounded-2xl p-10">
        <x-header-illustration title="Dashboard Kurir" :subtitle="'Selamat datang, ' . (Auth::user()->name ?? 'Kurir')" :image="'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png'" titleClass="text-gray-800"/>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-stat-card label="Menunggu Penjemputan" :value="count($pendingPickups ?? [])" bgClass="bg-indigo-50/80" textClass="text-indigo-700"/>
            <x-stat-card label="Sedang Proses" :value="$inProcessOrders ?? 0" bgClass="bg-yellow-50/80" textClass="text-yellow-700"/>
            <x-stat-card label="Selesai" :value="$completedOrders ?? 0" bgClass="bg-green-50/80" textClass="text-green-700"/>
        </div>
    </div> <!-- end gradient wrapper -->

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <x-stat-card label="Menunggu Penjemputan" :value="count($pendingPickups ?? [])" bgClass="bg-indigo-50/80" textClass="text-indigo-700"/>
                <x-stat-card label="Sedang Proses" :value="$inProcessOrders ?? 0" bgClass="bg-yellow-50/80" textClass="text-yellow-700"/>
                <x-stat-card label="Selesai" :value="$completedOrders ?? 0" bgClass="bg-green-50/80" textClass="text-green-700"/>
        </div>

    <!-- Pending pickups list -->
    <div class="bg-white rounded shadow p-4">
        <h2 class="text-xl font-semibold mb-4">Daftar Penjemputan (Menunggu)</h2>

        @if(count($pendingPickups ?? []) === 0)
            <div class="text-gray-600">Tidak ada penjemputan untuk Anda saat ini.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="border px-3 py-2">No</th>
                            <th class="border px-3 py-2">No Order</th>
                            <th class="border px-3 py-2">Customer</th>
                            <th class="border px-3 py-2">No. HP</th>
                            <th class="border px-3 py-2">Layanan</th>
                            <th class="border px-3 py-2">Alamat</th>
                            <th class="border px-3 py-2">Berat</th>
                            <th class="border px-3 py-2">Status</th>
                            <th class="border px-3 py-2 min-w-max">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingPickups as $index => $order)
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <td class="border px-3 py-2">{{ $index + 1 }}</td>
                                <td class="border px-3 py-2">{{ $order->order_number }}</td>
                                <td class="border px-3 py-2">{{ $order->customer->name ?? ($order->customer_name ?? '-') }}</td>
                                <td class="border px-3 py-2">@if($order->customer->phone ?? $order->customer_phone) <a href="tel:{{ $order->customer->phone ?? $order->customer_phone }}" class="text-blue-600">{{ $order->customer->phone ?? $order->customer_phone }}</a> @else - @endif</td>
                                <td class="border px-3 py-2">{{ $order->service->name ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $order->customer->address ?? $order->pickup_address ?? ($order->address ?? '-') }}</td>
                                <td class="border px-3 py-2">{{ $order->actual_weight ?? '-' }} kg</td>
                                <td class="border px-3 py-2">
                                    @if($order->status === 'menunggu_jemput')
                                        <span class="px-2 py-1 rounded text-xs font-semibold text-white bg-blue-600">Menunggu Jemput</span>
                                    @elseif($order->status === 'dijemput')
                                        <span class="px-2 py-1 rounded text-xs font-semibold text-white bg-yellow-600">Dijemput</span>
                                    @elseif($order->status === 'diantar')
                                        <span class="px-2 py-1 rounded text-xs font-semibold text-white bg-red-600">Diantar</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-semibold text-white bg-gray-600">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2"> 
                                    <div class="flex gap-2 flex-wrap">
                                        @php
                                            // Allow couriers assigned to the order to record payment when
                                            // the order is either picked up ('dijemput') or already out
                                            // for delivery ('diantar'). This enables cash-on-delivery flows.
                                            $canCollect = ($order->courier_id === Auth::id()) && in_array($order->status, ['approved','awaiting_collection','ready_for_delivery','dijemput','diantar']);
                                            $canPickUp = ($order->courier_id === Auth::id()) && in_array($order->status, ['pending','approved','awaiting_collection','ready_for_delivery','menunggu_jemput']);
                                        @endphp
                                        
                                        {{-- Button: Jemput (Pick Up) --}}
                                        @if($canPickUp && !in_array($order->status, ['dijemput', 'diantar']))
                                            <form method="POST" action="{{ route('courier.orders.picked_up', $order->id) }}" onsubmit="return confirm('Konfirmasi bahwa Anda telah menjemput pesanan ini?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-yellow-400 text-white rounded text-sm whitespace-nowrap hover:bg-yellow-500">Jemput</button>
                                            </form>
                                        @endif
                                        
                                        {{-- Button: Catat Pembayaran --}}
                                        @if($canCollect && in_array($order->status, ['dijemput','diantar']))
                                            <a href="{{ route('courier.orders.pickup', $order->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm whitespace-nowrap hover:bg-blue-700">Catat Pembayaran</a>
                                        @endif

                                        {{-- Button: Tandai Diantar --}}
                                        @if(in_array($order->status, ['dijemput']))
                                            <form method="POST" action="{{ route('courier.orders.delivered', $order->id) }}" onsubmit="return confirm('Konfirmasi bahwa pesanan sedang dalam perjalanan ke pelanggan?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded text-sm whitespace-nowrap hover:bg-red-600">Tandai Diantar</button>
                                            </form>
                                        @endif
                                        
                                        {{-- Button: Tandai Selesai --}}
                                        @if(in_array($order->status, ['diantar']))
                                            <form method="POST" action="{{ route('courier.orders.selesai', $order->id) }}" onsubmit="return confirm('Konfirmasi bahwa pesanan telah tiba di pelanggan dan SELESAI?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded text-sm whitespace-nowrap hover:bg-green-600">Tandai Selesai</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- In Process pickups list -->
        <div class="bg-white rounded shadow p-4 mt-6">
        <h2 class="text-xl font-semibold mb-4">Sedang Proses</h2>

        @if(count($inProcessOrdersList ?? []) === 0)
            <div class="text-gray-600">Tidak ada pesanan yang sedang dalam proses.</div>
        @else
            <div class="space-y-3">
                @foreach($inProcessOrdersList as $index => $order)
                    <!-- Mobile-first responsive card: flex-col (mobile) → flex-row (desktop) -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-4 bg-white shadow-md rounded-lg border-l-4 border-blue-500">
                        
                        <!-- Kiri: Info Order -->
                        <div class="w-full md:w-3/5 mb-3 md:mb-0">
                            <h4 class="text-lg font-semibold text-gray-800">Order #{{ $order->order_number }}</h4>
                            <p class="text-sm text-gray-500 truncate md:whitespace-normal mt-1">
                                {{ $order->customer->name ?? '-' }} | {{ $order->customer->address ?? $order->address ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->service->name ?? '-' }}</p>
                        </div>
                        
                        <!-- Kanan: Status Badge + Tombol Aksi -->
                        <div class="w-full md:w-2/5 flex items-center justify-start md:justify-end space-x-2 flex-wrap gap-2">
                            
                            <!-- Status Badge (warna dinamis) -->
                            @php
                                $statusColors = [
                                    'dijemput' => 'bg-yellow-100 text-yellow-800',
                                    'diantar' => 'bg-red-100 text-red-800',
                                    'di_laundry' => 'bg-purple-100 text-purple-800',
                                    'in_laundry' => 'bg-purple-100 text-purple-800',
                                ];
                                $badgeClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            
                            <!-- Tombol Aksi Inline -->
                            @php
                                $canCollect = ($order->courier_id === Auth::id()) && in_array($order->status, ['approved','awaiting_collection','ready_for_delivery','dijemput','diantar']);
                            @endphp
                            
                            @if($canCollect && in_array($order->status, ['dijemput','diantar']))
                                <a href="{{ route('courier.orders.pickup', $order->id) }}" class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 whitespace-nowrap">Catat</a>
                            @endif
                            
                            @if(in_array($order->status, ['dijemput']))
                                <form method="POST" action="{{ route('courier.orders.delivered', $order->id) }}" class="inline" onsubmit="return confirm('Konfirmasi pesanan sedang diantar?')">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 whitespace-nowrap">Diantar</button>
                                </form>
                            @endif
                            
                            @if(in_array($order->status, ['diantar']))
                                <form method="POST" action="{{ route('courier.orders.selesai', $order->id) }}" class="inline" onsubmit="return confirm('Konfirmasi pesanan SELESAI?')">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600 whitespace-nowrap">Selesai</button>
                                </form>
                            @endif
                            
                            @if($order->status === 'ready_for_delivery' && $order->courier_id === null)
                                <form method="POST" action="{{ route('courier.orders.claim', $order->id) }}" class="inline" onsubmit="return confirm('Ambil tugas?')">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700 whitespace-nowrap">Ambil</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Unassigned pickups - allow courier to claim -->
    <div class="bg-white rounded shadow p-4 mt-6">
        <h2 class="text-xl font-semibold mb-4">Pesanan Belum Ditugaskan</h2>
        @if(count($unassignedPickups ?? []) === 0)
            <div class="text-gray-600">Tidak ada pesanan yang belum ditugaskan.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">No Order</th>
                            <th class="px-3 py-2">Customer</th>
                            <th class="px-3 py-2">No. HP</th>
                            <th class="px-3 py-2">Layanan</th>
                            <th class="px-3 py-2">Alamat</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unassignedPickups as $index => $order)
                            <tr class="odd:bg-white even:bg-gray-50">
                                <td class="px-3 py-2">{{ $index + 1 }}</td>
                                <td class="px-3 py-2">{{ $order->order_number }}</td>
                                <td class="px-3 py-2">{{ $order->customer->name ?? '-' }}</td>
                                <td class="px-3 py-2">@if($order->customer->phone ?? $order->customer_phone) <a href="tel:{{ $order->customer->phone ?? $order->customer_phone }}" class="text-blue-600">{{ $order->customer->phone ?? $order->customer_phone }}</a> @else - @endif</td>
                                <td class="px-3 py-2">{{ $order->service->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $order->customer->address ?? $order->address ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    <form method="POST" action="{{ route('courier.orders.claim', $order->id) }}" onsubmit="return confirm('Ambil tugas pesanan {{ $order->order_number }}?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Ambil Tugas</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Transaction history -->
    <div class="bg-white rounded shadow p-4 mt-6">
        <h2 class="text-xl font-semibold mb-4">Riwayat Transaksi (Pembayaran yang Dicatat)</h2>
        @if(count($transactionHistory ?? []) === 0)
            <div class="text-gray-600">Belum ada transaksi yang dicatat oleh Anda.
            <div class="text-sm text-gray-500 mt-2">Catatan: Riwayat menampilkan pembayaran yang dicatat oleh Anda (kurir) atau pembayaran yang tercatat untuk pesanan yang ditugaskan kepada Anda (mis. dicatat oleh admin/kasir). Jika belum ada, maka tidak ada entri yang ditampilkan.</div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">No Order</th>
                            <th class="px-3 py-2">Customer</th>
                            <th class="px-3 py-2">Metode</th>
                            <th class="px-3 py-2">Jumlah</th>
                            <th class="px-3 py-2">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach($transactionHistory as $t)
                            <tr class="odd:bg-white even:bg-gray-50">
                                <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2">{{ $t->order ? ($t->order->order_number ?? 'N/A') : 'N/A' }}</td>
                                <td class="px-3 py-2">{{ $t->order && $t->order->customer ? $t->order->customer->name : '-' }}</td>
                                <td class="px-3 py-2">{{ $t->method ?? '-' }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($t->amount ?? 0, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">{{ $t->collected_at ? $t->collected_at->format('Y-m-d H:i') : $t->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
