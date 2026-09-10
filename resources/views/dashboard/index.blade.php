@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">
            Selamat datang, {{ auth()->user()->name }}!
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            {{
                auth()->user()->isAdmin()
                    ? 'Ringkasan aktivitas peminjaman barang laboratorium.'
                    : 'Pantau status pengajuan peminjaman barang Anda di sini.'
            }}
        </p>
    </div>

    @if (auth()->user()->isAdmin())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-stat-card label="Total Jenis Barang" :value="$stats['totalItems']" icon="box" color="emerald" />
            <x-stat-card label="Total Stok Barang" :value="$stats['totalStock']" icon="archive" color="blue" />
            <x-stat-card label="Pengajuan Menunggu" :value="$stats['loansPending']" icon="clock" color="amber" />
            <x-stat-card label="Barang Sedang Dipinjam" :value="$stats['loansActive']" icon="arrow-out" color="violet" />
            <x-stat-card label="Selesai Dikembalikan" :value="$stats['loansReturned']" icon="check" color="teal" />
            <x-stat-card label="Peminjaman Terlambat" :value="$stats['loansOverdue']" icon="alert" color="red" />
        </div>

        @if ($lowStockItems->isNotEmpty())
            <div class="mb-8 rounded-xl bg-amber-50 border border-amber-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <h2 class="font-semibold text-amber-900">Perhatian: Stok Menipis</h2>
                </div>
                <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach ($lowStockItems as $item)
                        <li class="flex items-center justify-between text-sm bg-white rounded-lg px-3 py-2 border border-amber-100">
                            <span class="text-amber-900">{{ $item->name }}</span>
                            <span class="font-semibold {{ $item->availableStock() === 0 ? 'text-red-600' : 'text-amber-700' }}">
                                Stok {{ $item->availableStock() }} {{ $item->unit }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-stat-card label="Total Stok Lab" :value="$stats['totalStock']" icon="archive" color="emerald" />
            <x-stat-card label="Pengajuan Saya" :value="$stats['loansPending']" icon="clock" color="amber" />
            <x-stat-card label="Dipinjam" :value="$stats['loansActive']" icon="arrow-out" color="violet" />
            <x-stat-card label="Selesai Dikembalikan" :value="$stats['loansReturned']" icon="check" color="teal" />
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">
                {{ auth()->user()->isAdmin() ? 'Peminjaman Terbaru' : 'Riwayat Peminjaman Terbaru' }}
            </h2>
            <a href="{{ route('loans.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">
                Lihat semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold">Kode</th>
                        @if (auth()->user()->isAdmin())
                            <th class="text-left px-6 py-3 font-semibold">Peminjam</th>
                        @endif
                        <th class="text-left px-6 py-3 font-semibold">Barang</th>
                        <th class="text-left px-6 py-3 font-semibold">Jumlah</th>
                        <th class="text-left px-6 py-3 font-semibold">Tanggal Pinjam</th>
                        <th class="text-left px-6 py-3 font-semibold">Batas Kembali</th>
                        <th class="text-left px-6 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentLoans as $loan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3.5 font-mono text-xs font-semibold text-slate-600">{{ $loan->code }}</td>
                            @if (auth()->user()->isAdmin())
                                <td class="px-6 py-3.5 text-slate-700">{{ $loan->user->name }}</td>
                            @endif
                            <td class="px-6 py-3.5 text-slate-700">{{ $loan->item->name }}</td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $loan->quantity }} {{ $loan->item->unit }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $loan->borrow_date->format('d M Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $loan->deadline->format('d M Y') }}
                                @if ($loan->isOverdue())
                                    <span class="ml-1 text-xs font-semibold text-red-600">Terlambat</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <x-status-badge :status="$loan->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                Belum ada data peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection