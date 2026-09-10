<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'item_id',
        'quantity',
        'borrow_date',
        'deadline',
        'returned_at',
        'overdue_notified_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'deadline' => 'date',
            'returned_at' => 'datetime',
        ];
    }

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_RETURNED = 'returned';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu Persetujuan',
        self::STATUS_APPROVED => 'Dipinjam',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_RETURNED => 'Dikembalikan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(LabItem::class, 'item_id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->deadline->startOfDay()->lt(now()->startOfDay());
    }
}
