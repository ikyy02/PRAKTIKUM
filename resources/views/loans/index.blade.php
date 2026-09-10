@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Pengelolaan Peminjaman' : 'Peminjaman Saya')

@section('content')
    <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                {{ auth()->user()->isAdmin() ? 'Pengelolaan Peminjaman' : 'Riwayat Peminjaman Saya' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ auth()->user()->isAdmin() ? 'Setujui, tolak, dan catat pengembalian barang.' : 'Pantau status pengajuan peminjaman barang Anda.' }}
            </p>
        </div>
        @if (! auth()->user()->isAdmin())
            <a href="{{ route('loans.create') }}"
               class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Ajukan Peminjaman
            </a>
        @endif
    </div>

    <div class="mb-6 flex items-center gap-2 flex-wrap">
        <a href="{{ route('loans.index') }}"
           class="inline-flex rounded-full border px-3.5 py-1.5 text-xs font-semibold {{ ! $status ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50' }}">
            Semua
        </a>
        @foreach (\App\Models\Loan::STATUS_LABELS as $key => $label)
            <a href="{{ route('loans.index', ['status' => $key]) }}"
               class="inline-flex rounded-full border px-3.5 py-1.5 text-xs font-semibold {{ $status === $key ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
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
                        <th class="text-left px-6 py-3 font-semibold">Tgl Kembali</th>
                        <th class="text-left px-6 py-3 font-semibold">Status</th>
                        <th class="text-left px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($loans as $loan)
                        <tr class="hover:bg-slate-50 align-top">
                            <td class="px-6 py-3.5 font-mono text-xs font-semibold text-slate-600">{{ $loan->code }}</td>
                            @if (auth()->user()->isAdmin())
                                <td class="px-6 py-3.5 text-slate-700">{{ $loan->user->name }}</td>
                            @endif
                            <td class="px-6 py-3.5">
                                <p class="font-medium text-slate-900">{{ $loan->item->name }}</p>
                                @if ($loan->notes)
                                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-2">{{ $loan->notes }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $loan->quantity }} {{ $loan->item->unit }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $loan->borrow_date->format('d M Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">
                                {{ $loan->deadline->format('d M Y') }}
                                @if ($loan->isOverdue())
                                    <span class="ml-1 text-xs font-semibold text-red-600">Terlambat</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">
                                {{ $loan->returned_at?->format('d M Y') ?? '–' }}
                            </td>
                            <td class="px-6 py-3.5">
                                <x-status-badge :status="$loan->status" />
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if (auth()->user()->isAdmin() && $loan->status === \App\Models\Loan::STATUS_PENDING)
                                        <form action="{{ route('loans.approve', $loan) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex rounded-md bg-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('loans.reject', $loan) }}" method="POST"
                                              onsubmit="return confirm('Tolak peminjaman {{ $loan->code }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex rounded-md border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                    @if (auth()->user()->isAdmin() && $loan->status === \App\Models\Loan::STATUS_APPROVED)
                                        <form action="{{ route('loans.return', $loan) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex rounded-md bg-blue-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                                Terima Kembali
                                            </button>
                                        </form>
                                    @endif
                                    @if (! auth()->user()->isAdmin() && $loan->status === \App\Models\Loan::STATUS_PENDING)
                                        <form action="{{ route('loans.destroy', $loan) }}" method="POST"
                                              onsubmit="return confirm('Batalkan pengajuan {{ $loan->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                                Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                                Belum ada data peminjaman
                                @if ($status)
                                    dengan status ini.
                                @else
                                    .
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $loans->links() }}
    </div>
@endsection