<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'investor_id',
        'parent_account_id',
        'account_code',
        'name',
        'account_type',
        'normal_balance',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'parent_account_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            Account::class,
            'parent_account_id'
        );
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }
}