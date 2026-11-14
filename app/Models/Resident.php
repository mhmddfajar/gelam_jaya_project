<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nik',
        'family_card_no',
        'name',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'marital_status',
        'occupation',
        'education',
        'citizenship',
        'address',
        'hamlet_id',
        'rt',
        'rw',
        'family_relation',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'gender' => Gender::class,
            'is_verified' => 'boolean',
        ];
    }

    public function hamlet(): BelongsTo
    {
        return $this->belongsTo(Hamlet::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}

