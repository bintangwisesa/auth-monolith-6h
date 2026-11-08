<?php

namespace Modules\Circulation\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_number', 'member_id', 'status', 'borrowed_at',
        'due_date', 'returned_at', 'fine_amount',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date'    => 'date',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'borrowed');
    }

    public function scopeOverdue(Builder $q): Builder
    {
        return $q->active()->whereDate('due_date', '<', now());
    }
}