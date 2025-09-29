<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Reservation extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'workshop_session_id',
        'full_name',
        'email',
        'phone',
        'status',
        'cancel_token',
        'confirm_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(WorkshopSession::class, 'workshop_session_id');
    }

    /** Normalise l’e-mail (trim + lowercase) pour respecter l’unique index */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value === null ? null : mb_strtolower(trim($value))
        );
    }
}
