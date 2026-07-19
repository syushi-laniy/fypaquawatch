<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'status',
        'control_mode',
        'tank_height_cm',
        'ph_sensor',
        'turbidity_sensor',
        'water_level_sensor',
        'dosing_device',
        'topup_device',
        'feeder_device',
    ];

    protected $casts = [
        'tank_height_cm' => 'float',
    ];

    public function tankHeightCm(): float
    {
        return is_numeric($this->tank_height_cm) && (float) $this->tank_height_cm > 0
            ? (float) $this->tank_height_cm
            : 20.3;
    }

    public function actualWaterLevelFromDistance(?float $distance): ?float
    {
        if ($distance === null) {
            return null;
        }

        return max($this->tankHeightCm() - $distance, 0);
    }

    public function waterLevelRange(?float $minValue = null, ?float $maxValue = null): array
    {
        $height = $this->tankHeightCm();
        $defaultMin = round($height * 0.75, 2);

        $min = is_numeric($minValue)
            ? (float) $minValue
            : $defaultMin;
        $max = is_numeric($maxValue)
            ? (float) $maxValue
            : $height;

        $min = max(0, min($min, $height));
        $max = max($min, min($max, $height));

        return [
            'min' => $min,
            'max' => $max,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'tank_species')
            ->withTimestamps();
    }

    public function tankRequest(): HasOne
    {
        return $this->hasOne(TankRequest::class);
    }
}
