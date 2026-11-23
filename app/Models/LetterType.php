<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'requirements',
        'form_fields',
        'template',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'form_fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope query to only include active letter types.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Requirements list relation.
     */
    public function typeRequirements(): HasMany
    {
        return $this->hasMany(LetterTypeRequirement::class, 'letter_type_id');
    }

    /**
     * Alias accessor for form_schema to match PRD convention.
     */
    public function getFormSchemaAttribute(): ?array
    {
        return $this->form_fields;
    }

    /**
     * Mutator to set code uppercase and trimmed.
     */
    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }
}
