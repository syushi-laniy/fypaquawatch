<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TankDeviceState extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'device_key',
        'state',
    ];

    protected $casts = [
        'state' => 'boolean',
    ];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
