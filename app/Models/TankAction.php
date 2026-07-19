<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TankAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'user_id',
        'requested_by',
        'action',
        'action_type',
        'note',
        'status',
        'requested_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
