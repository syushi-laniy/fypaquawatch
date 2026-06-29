<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'status',
        'control_mode',
        'ph_sensor',
        'turbidity_sensor',
        'water_level_sensor',
        'dosing_device',
        'topup_device',
        'feeder_device',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'tank_species')
            ->withTimestamps();
    }
}
