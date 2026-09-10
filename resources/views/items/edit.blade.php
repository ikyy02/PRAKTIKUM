@extends('layouts.app')

@section('title', 'Ubah Barang Lab')

@section('content')
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">← Kembali ke data barang</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Ubah Barang Lab</h1>
        <p class="text-sm text-slate-500 mt-1">Perbarui informasi barang {{ $item->code }}.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 max-w-2xl">
        <form action="{{ route('items.update', $item) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form-input name="code" label="Kode Barang" placeholder="mis. LC-001" value="{{ old('code', $item->code) }}" required />
                <x-form-input name="category" label="Kategori" placeholder="mis. Hardware" value="{{ old('category', $item->category) }}" required />
            </div>

            <x-form-input name="name" label="Nama Barang" placeholder="mis. Laptop Acer TravelMate" value="{{ old('name', $item->name) }}" required />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form-input name="stock" label="Jumlah Stok" type="number" min="0" value="{{ old('stock', $item->stock) }}" required />
                <x-form-input name="unit" label="Satuan" placeholder="unit" value="{{ old('unit', $item->unit) }}" required />
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi <span class="text-slate-400">(opsional)</span></label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none"
                          placeholder="Keterangan singkat tentang barang...">{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Simpan Perubahan
                </button>
                <a href="{{ route('items.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection