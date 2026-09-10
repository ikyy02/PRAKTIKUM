<?php

namespace App\Http\Controllers;

use App\Models\LabItem;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LabItemController extends Controller
{
    public function index()
    {
        $items = LabItem::withCount(['loans as active_qty' => fn ($q) => $q->where('status', Loan::STATUS_APPROVED)])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        LabItem::create($data);

        return redirect()->route('items.index')
            ->with('success', 'Barang lab berhasil ditambahkan.');
    }

    public function edit(LabItem $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, LabItem $item)
    {
        $data = $request->validate($this->rules($item));

        $item->update($data);

        return redirect()->route('items.index')
            ->with('success', 'Data barang lab berhasil diperbarui.');
    }

    public function destroy(LabItem $item)
    {
        if ($item->loans()->exists()) {
            return redirect()->route('items.index')
                ->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang lab berhasil dihapus.');
    }

    protected function rules(?LabItem $item = null): array
    {
        $codeRule = Rule::unique('lab_items', 'code');
        if ($item) {
            $codeRule->ignore($item);
        }

        return [
            'code' => ['required', 'string', 'max:30', $codeRule],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }
}
