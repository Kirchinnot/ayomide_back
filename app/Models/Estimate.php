<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estimate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'items',
        'total_price',
        'status',
        'valid_until',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'items' => 'json',
        'total_price' => 'decimal:2',
        'valid_until' => 'date',
    ];

    /**
     * Get the user that owns this estimate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only pending estimates
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    /**
     * Scope: Get only accepted estimates
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope: Filter by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('client_email', $email);
    }

    /**
     * Check if estimate is expired
     */
    public function isExpired(): bool
    {
        if (!$this->valid_until) {
            return false;
        }

        return now()->isAfter($this->valid_until);
    }

    /**
     * Get the total count of items in the estimate
     */
    public function getItemCountAttribute(): int
    {
        return is_array($this->items) ? count($this->items) : 0;
    }
}
