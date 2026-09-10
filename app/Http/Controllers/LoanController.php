<?php

namespace App\Http\Controllers;

use App\Models\LabItem;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $loans = Loan::with(['user', 'item'])
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('user_id', $request->user()->id))
            ->when($status && in_array($status, array_keys(Loan::STATUS_LABELS), true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('loans.index', compact('loans', 'status'));
    }

    public function create()
    {
        $items = LabItem::orderBy('name')->get();

        return view('loans.create', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => ['required', 'exists:lab_items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'borrow_date' => ['required', 'date'],
            'deadline' => ['required', 'date', 'after_or_equal:borrow_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $item = LabItem::findOrFail($data['item_id']);

        if ($item->availableStock() < $data['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => "Stok \"{$item->name}\" tersedia hanya {$item->availableStock()} {$item->unit}.",
            ]);
        }

        $loan = Loan::create([
            'user_id' => $request->user()->id,
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
            'borrow_date' => $data['borrow_date'],
            'deadline' => $data['deadline'],
            'notes' => $data['notes'] ?? null,
            'status' => Loan::STATUS_PENDING,
        ]);

        $loan->update([
            'code' => 'PMJ-'.str_pad((string) $loan->id, 4, '0', STR_PAD_LEFT),
        ]);

        return redirect()->route('loans.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim, menunggu persetujuan admin.');
    }

    public function approve(Loan $loan)
    {
        $this->authorizeAdmin();

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Hanya pengajuan berstatus menunggu yang dapat disetujui.');
        }

        try {
            DB::transaction(function () use ($loan) {
                $item = LabItem::whereKey($loan->item_id)->lockForUpdate()->firstOrFail();

                if ($item->availableStock() < $loan->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stok \"{$item->name}\" tidak mencukupi (tersedia {$item->availableStock()} {$item->unit}).",
                    ]);
                }

                $loan->update(['status' => Loan::STATUS_APPROVED]);
            });
        } catch (ValidationException $e) {
            return back()->with('error', $e->getMessage())->withErrors($e->errors());
        }

        return back()->with('success', "Peminjaman {$loan->code} disetujui.");
    }

    public function reject(Loan $loan)
    {
        $this->authorizeAdmin();

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Hanya pengajuan berstatus menunggu yang dapat ditolak.');
        }

        $loan->update(['status' => Loan::STATUS_REJECTED]);

        return back()->with('success', "Peminjaman {$loan->code} ditolak.");
    }

    public function return(Loan $loan)
    {
        $this->authorizeAdmin();

        if ($loan->status !== Loan::STATUS_APPROVED) {
            return back()->with('error', 'Hanya peminjaman berstatus dipinjam yang dapat dikembalikan.');
        }

        $loan->update([
            'status' => Loan::STATUS_RETURNED,
            'returned_at' => now(),
        ]);

        return back()->with('success', "Peminjaman {$loan->code} telah dikembalikan.");
    }

    public function destroy(Request $request, Loan $loan)
    {
        if ($request->user()->isAdmin()) {
            $loan->delete();

            return back()->with('success', "Peminjaman {$loan->code} dihapus.");
        }

        if ($loan->user_id !== $request->user()->id || $loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Anda hanya dapat membatalkan pengajuan yang belum disetujui.');
        }

        $loan->delete();

        return back()->with('success', 'Pengajuan peminjaman dibatalkan.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(request()->user()->isAdmin(), 403, 'Akses khusus admin.');
    }
}
