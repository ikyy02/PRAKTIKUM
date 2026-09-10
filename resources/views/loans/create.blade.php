@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
    <div class="mb-6">
        <a href="{{ route('loans.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">← Kembali ke peminjaman</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Ajukan Peminjaman Barang</h1>
        <p class="text-sm text-slate-500 mt-1">Isi form berikut, kemudian pengajuan Anda akan diverifikasi oleh admin.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 max-w-2xl">
        <form action="{{ route('loans.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="item_id" class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Barang <span class="text-red-500">*</span></label>
                <select id="item_id" name="item_id" required
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none bg-white">
                    <option value="" disabled selected>— Pilih barang —</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }} @disabled($item->availableStock() === 0)>
                            {{ $item->code }} · {{ $item->name }} (tersedia {{ $item->availableStock() }} {{ $item->unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="sm:col-span-1">
                    <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                    <input id="quantity" type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <div>
                    <label for="borrow_date" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Pinjam <span class="text-red-500">*</span></label>
                    <input id="borrow_date" type="date" name="borrow_date" value="{{ old('borrow_date', now()->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <div>
                    <label for="deadline" class="block text-sm font-medium text-slate-700 mb-1.5">Batas Kembali <span class="text-red-500">*</span></label>
                    <input id="deadline" type="date" name="deadline" value="{{ old('deadline', now()->addDays(3)->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1.5">Keperluan / Catatan <span class="text-slate-400">(opsional)</span></label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none"
                          placeholder="Contoh: untuk praktikum Basis Data kelas B ...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Kirim Pengajuan
                </button>
                <a href="{{ route('loans.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection