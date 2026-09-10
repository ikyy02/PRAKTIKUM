@extends('layouts.app')

@section('title', 'Data Barang Lab')

@section('content')
    <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Barang Laboratorium</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar barang yang tersedia di laboratorium.</p>
        </div>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('items.create') }}"
               class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Barang
            </a>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold">Kode</th>
                        <th class="text-left px-6 py-3 font-semibold">Nama Barang</th>
                        <th class="text-left px-6 py-3 font-semibold">Kategori</th>
                        <th class="text-left px-6 py-3 font-semibold">Total Stok</th>
                        <th class="text-left px-6 py-3 font-semibold">Dipinjam</th>
                        <th class="text-left px-6 py-3 font-semibold">Tersedia</th>
                        @if (auth()->user()->isAdmin())
                            <th class="text-left px-6 py-3 font-semibold">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3.5 font-mono text-xs font-semibold text-slate-600">{{ $item->code }}</td>
                            <td class="px-6 py-3.5">
                                <p class="font-medium text-slate-900">{{ $item->name }}</p>
                                @if ($item->description)
                                    <p class="text-xs text-slate-400 line-clamp-1">{{ $item->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $item->stock }} {{ $item->unit }}</td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $item->active_qty }}</td>
                            <td class="px-6 py-3.5">
                                @if ($item->availableStock() === 0)
                                    <span class="inline-flex items-center gap-1 font-semibold text-red-600">
                                        <span class="size-2 rounded-full bg-red-500"></span> Habis
                                    </span>
                                @elseif ($item->availableStock() <= 2)
                                    <span class="inline-flex items-center gap-1 font-semibold text-amber-600">
                                        <span class="size-2 rounded-full bg-amber-500"></span> {{ $item->availableStock() }} {{ $item->unit }}
                                    </span>
                                @else
                                    <span class="font-semibold text-emerald-600">{{ $item->availableStock() }} {{ $item->unit }}</span>
                                @endif
                            </td>
                            @if (auth()->user()->isAdmin())
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('items.edit', $item) }}"
                                           class="inline-flex items-center gap-1 rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">
                                            Ubah
                                        </a>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Hapus barang {{ $item->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Belum ada barang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>
@endsection