<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TankRequest extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending',
        'approved',
        'in_progress',
        'completed',
        'rejected',
    ];

    protected $fillable = [
        'user_id',
        'tank_id',
        'approved_by',
        'tank_name',
        'tank_size',
        'fish_species',
        'delivery_address',
        'phone_number',
        'additional_notes',
        'status',
        'tank_code',
        'admin_note',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
