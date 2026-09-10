<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Daftar') }} · {{ config('app.name', 'Peminjaman Lab') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 min-h-screen antialiased text-slate-800">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <span class="inline-flex items-center justify-center size-14 rounded-2xl bg-white/10 backdrop-blur text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                    </svg>
                </span>
                <h1 class="text-2xl font-bold text-white">LabKOM</h1>
                <p class="text-emerald-100 text-sm mt-1">Sistem Peminjaman Barang Laboratorium<br>Jurusan Komputer dan Bisnis</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8">
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold mb-1">Mohon perbaiki kesalahan berikut:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h2 class="text-lg font-bold text-slate-900 mb-5">{{ __('Buat akun baru') }}</h2>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Nama Lengkap') }}</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                            {{ __('Alamat Email') }}
                            <span class="text-xs text-slate-400 normal-case">(wajib @mhs.politala.ac.id)</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               placeholder="contoh@mhs.politala.ac.id"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">
                            {{ __('Nomor WhatsApp') }}
                            <span class="text-xs text-slate-400 normal-case">(untuk notifikasi pengembalian)</span>
                        </label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="mis. 081234567890"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Konfirmasi Password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                    </div>
                    <button type="submit"
                            class="w-full rounded-lg bg-emerald-600 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                        {{ __('Daftar') }}
                    </button>
                </form>

                <p class="text-center text-sm text-slate-500 mt-6">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>