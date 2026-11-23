<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestAttachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'letter_request_id',
        'requirement_id',
        'file_path',
        'original_name',
        'mime_type',
        'size_kb',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_kb' => 'integer',
        ];
    }

    /**
     * Belongs to letter request.
     */
    public function letterRequest(): BelongsTo
    {
        return $this->belongsTo(LetterRequest::class, 'letter_request_id');
    }

    /**
     * Belongs to requirement definition.
     */
    public function requirement(): BelongsTo
    {
        return $this->belongsTo(LetterTypeRequirement::class, 'requirement_id');
    }
}
