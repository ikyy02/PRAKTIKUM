<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $baseUrl = 'https://api.fonnte.com';

    public function isConfigured(): bool
    {
        return (bool) config('services.fonnte.token');
    }

    public function send(string $target, string $message): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('Fonnte token belum dikonfigurasi, notifikasi tidak dikirim.', compact('target'));

            return false;
        }

        $response = Http::withToken(config('services.fonnte.token'))
            ->asForm()
            ->post("{$this->baseUrl}/send", [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

        $body = $response->json();

        if ($response->successful() && ($body['status'] ?? false) === true) {
            return true;
        }

        Log::error('Fonnte gagal mengirim pesan.', [
            'target' => $target,
            'status_code' => $response->status(),
            'response' => $body,
        ]);

        return false;
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }
}
