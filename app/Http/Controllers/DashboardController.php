<?php

namespace App\Http\Controllers;

use App\Models\LabItem;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $stats = [
            'totalItems' => LabItem::count(),
            'totalStock' => LabItem::sum('stock'),
            'loansPending' => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'loansActive' => Loan::where('status', Loan::STATUS_APPROVED)->count(),
            'loansReturned' => Loan::where('status', Loan::STATUS_RETURNED)->count(),
            'loansOverdue' => Loan::where('status', Loan::STATUS_APPROVED)
                ->whereDate('deadline', '<', now()->toDateString())->count(),
        ];

        $lowStockItems = LabItem::withCount('loans as active_qty')
            ->get()
            ->filter(fn (LabItem $item) => $item->availableStock() <= 2)
            ->sortBy(fn (LabItem $item) => $item->availableStock())
            ->take(6);

        if ($user->isAdmin()) {
            $recentLoans = Loan::with(['user', 'item'])
                ->latest()
                ->take(6)
                ->get();
        } else {
            $recentLoans = $user->loans()
                ->with('item')
                ->latest()
                ->take(6)
                ->get();
        }

        return view('dashboard.index', compact('stats', 'lowStockItems', 'recentLoans'));
    }
}
