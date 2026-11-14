<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'photo_path',
        'period_start',
        'period_end',
        'can_sign',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'can_sign' => 'boolean',
            'is_active' => 'boolean',
            'period_start' => 'integer',
            'period_end' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCanSign(Builder $query): Builder
    {
        return $query->where('can_sign', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }
}

