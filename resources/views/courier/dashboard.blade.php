@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 shadow-2xl rounded-2xl p-10">
        <x-header-illustration title="Dashboard Kurir" :subtitle="'Selamat datang, ' . (Auth::user()->name ?? 'Kurir')" :image="'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png'" titleClass="text-gray-800"/>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-stat-card label="Menunggu Penjemputan" :value="count($pendingPickups ?? [])" bgClass="bg-indigo-50/80" textClass="text-indigo-700"/>
            <x-stat-card label="Sedang Proses" :value="$inProcessOrders ?? 0" bgClass="bg-yellow-50/80" textClass="text-yellow-700"/>
            <x-stat-card label="Selesai" :value="$completedOrders ?? 0" bgClass="bg-green-50/80" textClass="text-green-700"/>
        </div>
    </div> <!-- end gradient wrapper -->
    <!-- Chart: Courier earnings last 6 months -->
    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-3">Penghasilan 6 Bulan Terakhir</h3>
        <div class="bg-white p-6 rounded-2xl shadow-md">
            <canvas id="courierEarningsChart" class="w-full h-48"></canvas>
        </div>
    </div>

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
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">No Order</th>
                            <th class="px-3 py-2">Customer</th>
                            <th class="px-3 py-2">Layanan</th>
                            <th class="px-3 py-2">Alamat</th>
                            <th class="px-3 py-2">Berat</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingPickups as $index => $order)
                            <tr class="odd:bg-white even:bg-gray-50">
                                <td class="px-3 py-2">{{ $index + 1 }}</td>
                                <td class="px-3 py-2">{{ $order->order_number }}</td>
                                <td class="px-3 py-2">{{ $order->customer->name ?? ($order->customer_name ?? '-') }}</td>
                                <td class="px-3 py-2">{{ $order->service->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $order->customer->address ?? $order->pickup_address ?? ($order->address ?? '-') }}</td>
                                <td class="px-3 py-2">{{ $order->actual_weight ?? '-' }} kg</td>
                                <td class="px-3 py-2">{{ $order->status }}</td>
                                <td class="px-3 py-2"> 
                                    <div class="flex gap-2">
                                        @php
                                            $canCollect = ($order->courier_id === Auth::id()) && in_array($order->status, ['approved','awaiting_collection','ready_for_delivery','dijemput']);
                                        @endphp
                                        @if($canCollect)
                                            <a href="{{ route('courier.orders.pickup', $order->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Catat Pembayaran</a>
                                        @else
                                            <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm">Catat Pembayaran</span>
                                        @endif
                                        {{-- Button to mark as picked up --}}
                                        <form method="POST" action="{{ route('courier.orders.picked_up', $order->id) }}" onsubmit="return confirm('Konfirmasi bahwa Anda telah menjemput pesanan ini?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-yellow-400 text-white rounded text-sm">Jemput</button>
                                        </form>
                                        {{-- NOTE: 'Tandai Diantar' shouldn't be available from 'Pending' list since the
                                             courier must pick up the order first. The In-Process list below
                                             will show the 'Tandai Diantar' button after the courier marks it as picked up. --}}
                                        {{-- Button to finish and collect payment as an alternate path --}}
                                        <form method="POST" action="{{ route('courier.orders.pickup.store', $order->id) }}" onsubmit="return confirm('Konfirmasi jemput dan catat pembayaran untuk pesanan ini?')">
                                            @csrf
                                            <input type="hidden" name="collection_method" value="Tunai">
                                            <input type="hidden" name="collected_amount" value="{{ $order->total_price ?? 0 }}">
                                            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Selesaikan & Tagih</button>
                                        </form>
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
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">No Order</th>
                            <th class="px-3 py-2">Customer</th>
                            <th class="px-3 py-2">Layanan</th>
                            <th class="px-3 py-2">Alamat</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inProcessOrdersList as $index => $order)
                            <tr class="odd:bg-white even:bg-gray-50">
                                <td class="px-3 py-2">{{ $index + 1 }}</td>
                                <td class="px-3 py-2">{{ $order->order_number }}</td>
                                <td class="px-3 py-2">{{ $order->customer->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $order->service->name ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $order->customer->address ?? $order->address ?? '-' }}</td>
                                <td class="px-3 py-2">{{ ucfirst($order->status) }}</td>
                                <td class="px-3 py-2"> 
                                    <div class="flex gap-2">
                                        @php
                                            $canCollect = ($order->courier_id === Auth::id()) && in_array($order->status, ['approved','awaiting_collection','ready_for_delivery','dijemput']);
                                        @endphp
                                        @if($canCollect)
                                            <a href="{{ route('courier.orders.pickup', $order->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Catat Pembayaran</a>
                                        @else
                                            <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm">Catat Pembayaran</span>
                                        @endif

                                        {{-- Add 'Tandai Diantar' here only when the order has been marked as 'dijemput' --}}
                                        @if(in_array($order->status, ['dijemput','diantar']))
                                            <form method="POST" action="{{ route('courier.orders.delivered', $order->id) }}" onsubmit="return confirm('Konfirmasi bahwa pesanan telah tiba di pelanggan dan SELESAI?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded text-sm">Tandai Selesai</button>
                                            </form>
                                        @endif
                                        {{-- Show 'Ambil Tugas' for ready_for_delivery orders if not assigned to this courier --}}
                                        @if($order->status === 'ready_for_delivery' && $order->courier_id === null)
                                            <form method="POST" action="{{ route('courier.orders.claim', $order->id) }}" onsubmit="return confirm('Ambil tugas dan kirim pesanan ini?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Ambil Tugas & Antar</button>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function(){
        const ctx = document.getElementById('courierEarningsChart');
        if (!ctx) return;
        const labels = JSON.parse('@json($months ?? [])'.replace(/&quot;/g, '"'));
        const data = JSON.parse('@json($courierMonthlyEarnings ?? [])'.replace(/&quot;/g, '"'));
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penghasilan (Rp)',
                    backgroundColor: '#10b981',
                    borderColor: '#10b981',
                    data: data,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    })();
</script>
