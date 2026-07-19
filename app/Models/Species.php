<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Species extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_ph',
        'max_ph',
        'description',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'min_ph' => 'float',
        'max_ph' => 'float',
        'is_active' => 'boolean',
    ];

    public function tanks(): BelongsToMany
    {
        return $this->belongsToMany(Tank::class, 'tank_species')
            ->withTimestamps();
    }
}
