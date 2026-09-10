<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'stock',
        'description',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'item_id');
    }

    public function activeQuantity(): int
    {
        return (int) $this->loans()
            ->where('status', 'approved')
            ->sum('quantity');
    }

    public function availableStock(): int
    {
        return max(0, $this->stock - $this->activeQuantity());
    }
}
