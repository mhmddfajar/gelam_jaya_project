<?php

namespace App\Models;

use App\Enums\Channel;
use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'registration_no',
        'letter_type_id',
        'resident_id',
        'submitted_by',
        'channel',
        'status',
        'purpose',
        'form_data',
        'status_note',
        'letter_number',
        'letter_date',
        'signer_id',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'channel' => Channel::class,
            'status' => RequestStatus::class,
            'form_data' => 'array',
            'letter_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Belongs to a letter type.
     */
    public function letterType(): BelongsTo
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    /**
     * Belongs to a resident (subject).
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    /**
     * Belongs to the user who submitted the request.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Belongs to the official signer.
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(Official::class, 'signer_id');
    }

    /**
     * Has many file attachments.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(RequestAttachment::class, 'letter_request_id');
    }

    /**
     * Has many status logs.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(RequestStatusLog::class, 'letter_request_id')->orderBy('created_at', 'desc');
    }

    /**
     * Scope query to only include active requests (not completed or rejected).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [RequestStatus::SELESAI->value, RequestStatus::DITOLAK->value]);
    }

    /**
     * Scope query for a specific resident or submitter.
     */
    public function scopeForWarga(Builder $query, int $residentId): Builder
    {
        return $query->where('resident_id', $residentId);
    }

    /**
     * Determine if request is still active.
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
