<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Services\FonnteService;
use Illuminate\Console\Command;

class NotifyOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:notify-overdue {--force : Kirim notifikasi ulang meskipun sudah pernah dikirim}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim notifikasi WhatsApp ke peminjam yang barangnya melebihi batas waktu pengembalian';

    public function handle(FonnteService $fonnte): int
    {
        if (! $fonnte->isConfigured()) {
            $this->error('Fonnte token belum dikonfigurasi. Isi FONNTE_TOKEN di file .env terlebih dahulu.');

            return self::FAILURE;
        }

        $query = Loan::query()
            ->where('status', Loan::STATUS_APPROVED)
            ->whereDate('deadline', '<', now()->toDateString())
            ->whereHas('user', fn ($q) => $q->whereNotNull('phone'))
            ->with(['item', 'user']);

        if (! $this->option('force')) {
            $query->whereNull('overdue_notified_at');
        }

        $loans = $query->get();

        if ($loans->isEmpty()) {
            $this->info('Tidak ada peminjaman yang melebihi batas waktu.');

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($loans as $loan) {
            $phone = $fonnte->normalizePhone($loan->user->phone);
            $message = $this->buildMessage($loan);

            if ($fonnte->send($phone, $message)) {
                $loan->update(['overdue_notified_at' => now()]);
                $this->info("Notifikasi terkirim untuk {$loan->code} ke {$phone}");
                $sent++;
            } else {
                $this->error("Notifikasi GAGAL untuk {$loan->code}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Selesai: {$sent} terkirim, {$failed} gagal.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function buildMessage(Loan $loan): string
    {
        return implode("\n", [
            '*LabKOM - Jurusan Komputer & Bisnis*',
            '',
            "Halo *{$loan->user->name}*,",
            '',
            'Barang yang Anda pinjam telah *MELEBIHI BATAS* waktu pengembalian.',
            '',
            "Kode Peminjaman: {$loan->code}",
            "Barang: {$loan->item->name}",
            "Jumlah: {$loan->quantity} {$loan->item->unit}",
            'Tanggal Pinjam: '.$loan->borrow_date->format('d M Y'),
            'Batas Pengembalian: '.$loan->deadline->format('d M Y'),
            '',
            'Mohon segera kembalikan barang ke laboratorium. Terima kasih.',
        ]);
    }
}
