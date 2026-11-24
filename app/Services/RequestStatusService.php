<?php

namespace App\Services;

use App\Enums\Channel;
use App\Enums\RequestStatus;
use App\Models\LetterRequest;
use App\Models\RequestStatusLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestStatusService
{
    /**
     * Transition a letter request to a new status.
     *
     * @throws ValidationException
     */
    public function transition(
        LetterRequest $letterRequest,
        RequestStatus|string $toStatus,
        User $user,
        ?string $note = null
    ): LetterRequest {
        $fromStatus = $letterRequest->status;

        // Terminal states cannot transition further
        if ($fromStatus === RequestStatus::SELESAI || $fromStatus === RequestStatus::DITOLAK) {
            throw ValidationException::withMessages([
                'status' => "Pengajuan sudah berada pada status akhir ({$fromStatus->label()}) dan tidak dapat diubah lagi.",
            ]);
        }

        // Parse target status
        $targetStatus = $toStatus instanceof RequestStatus ? $toStatus : RequestStatus::tryFrom($toStatus);
        if (!$targetStatus) {
            throw ValidationException::withMessages([
                'status' => 'Status target tidak valid.',
            ]);
        }

        if ($fromStatus === $targetStatus) {
            throw ValidationException::withMessages([
                'status' => "Pengajuan sudah berada pada status {$targetStatus->label()}.",
            ]);
        }

        // Validate allowed transitions based on user role and state machine
        $this->validateTransition($letterRequest, $fromStatus, $targetStatus, $user, $note);

        return DB::transaction(function () use ($letterRequest, $fromStatus, $targetStatus, $user, $note) {
            $letterRequest->status = $targetStatus;

            $trimmedNote = $note !== null && trim($note) !== '' ? trim($note) : null;
            if ($trimmedNote !== null) {
                $letterRequest->status_note = $trimmedNote;
            }

            if ($targetStatus === RequestStatus::SELESAI && empty($letterRequest->completed_at)) {
                $letterRequest->completed_at = now();
            }

            $letterRequest->save();

            RequestStatusLog::create([
                'letter_request_id' => $letterRequest->id,
                'from_status' => $fromStatus->value,
                'to_status' => $targetStatus->value,
                'note' => $trimmedNote,
                'changed_by' => $user->id,
                'created_at' => now(),
            ]);

            return $letterRequest->fresh();
        });
    }

    /**
     * Validate the status transition according to PRD Bagian 6 rules.
     *
     * @throws ValidationException
     */
    protected function validateTransition(
        LetterRequest $letterRequest,
        RequestStatus $fromStatus,
        RequestStatus $targetStatus,
        User $user,
        ?string $note
    ): void {
        $trimmedNote = $note !== null ? trim($note) : '';

        // If user is Warga (citizen)
        if ($user->isWarga()) {
            if ($letterRequest->resident_id !== $user->resident_id && $letterRequest->submitted_by !== $user->id) {
                throw ValidationException::withMessages([
                    'status' => 'Anda tidak memiliki hak akses untuk mengubah pengajuan ini.',
                ]);
            }

            // Citizen can only transition from PERLU_PERBAIKAN to DIAJUKAN (via resubmit)
            if ($fromStatus === RequestStatus::PERLU_PERBAIKAN && $targetStatus === RequestStatus::DIAJUKAN) {
                return;
            }

            throw ValidationException::withMessages([
                'status' => 'Warga hanya dapat mengajukan ulang permohonan yang memerlukan perbaikan.',
            ]);
        }

        // If user is Staf or Admin
        if ($user->isStaf() || $user->isAdmin()) {
            // Check mandatory note for PERLU_PERBAIKAN and DITOLAK
            if (in_array($targetStatus, [RequestStatus::PERLU_PERBAIKAN, RequestStatus::DITOLAK], true)) {
                if ($trimmedNote === '') {
                    throw ValidationException::withMessages([
                        'note' => 'Catatan atau alasan wajib diisi saat mengubah status menjadi ' . $targetStatus->label() . '.',
                    ]);
                }
            }

            // Allowed transitions for staff/admin
            $isAllowed = match ($fromStatus) {
                RequestStatus::DIAJUKAN => in_array($targetStatus, [
                    RequestStatus::DIVERIFIKASI,
                    RequestStatus::PERLU_PERBAIKAN,
                    RequestStatus::DITOLAK,
                ], true),

                RequestStatus::DIVERIFIKASI => in_array($targetStatus, [
                    RequestStatus::DIPROSES,
                    RequestStatus::DITOLAK,
                ], true),

                RequestStatus::DIPROSES => (
                    $targetStatus === RequestStatus::SIAP_DIAMBIL ||
                    ($targetStatus === RequestStatus::SELESAI && $letterRequest->channel === Channel::LURING && !empty($letterRequest->letter_number))
                ),

                RequestStatus::SIAP_DIAMBIL => $targetStatus === RequestStatus::SELESAI,

                default => false,
            };

            // Provide specific helpful error messages if diproses -> selesai fails conditions
            if ($fromStatus === RequestStatus::DIPROSES && $targetStatus === RequestStatus::SELESAI) {
                if ($letterRequest->channel !== Channel::LURING) {
                    throw ValidationException::withMessages([
                        'status' => 'Hanya pengajuan jalur luring yang dapat langsung diselesaikan dari status diproses.',
                    ]);
                }

                if (empty($letterRequest->letter_number)) {
                    throw ValidationException::withMessages([
                        'status' => 'Surat naskah dinas harus dibuat/digenerate terlebih dahulu sebelum status diselesaikan.',
                    ]);
                }
            }

            if (!$isAllowed) {
                throw ValidationException::withMessages([
                    'status' => "Transisi dari status {$fromStatus->label()} ke {$targetStatus->label()} tidak diizinkan.",
                ]);
            }

            return;
        }

        throw ValidationException::withMessages([
            'status' => 'Peran pengguna tidak diizinkan mengubah status pengajuan.',
        ]);
    }

    /**
     * Get allowed next statuses for a given request and user.
     *
     * @return array<int, array{value: string, label: string, badgeColor: string, requireNote: bool}>
     */
    public function getAllowedNextStatuses(LetterRequest $letterRequest, User $user): array
    {
        $current = $letterRequest->status;
        $targets = [];

        if ($user->isWarga()) {
            if ($current === RequestStatus::PERLU_PERBAIKAN) {
                $targets = [RequestStatus::DIAJUKAN];
            }
        } elseif ($user->isStaf() || $user->isAdmin()) {
            $targets = match ($current) {
                RequestStatus::DIAJUKAN => [
                    RequestStatus::DIVERIFIKASI,
                    RequestStatus::PERLU_PERBAIKAN,
                    RequestStatus::DITOLAK,
                ],
                RequestStatus::DIVERIFIKASI => [
                    RequestStatus::DIPROSES,
                    RequestStatus::DITOLAK,
                ],
                RequestStatus::DIPROSES => array_filter([
                    RequestStatus::SIAP_DIAMBIL,
                    ($letterRequest->channel === Channel::LURING && !empty($letterRequest->letter_number)) ? RequestStatus::SELESAI : null,
                ]),
                RequestStatus::SIAP_DIAMBIL => [
                    RequestStatus::SELESAI,
                ],
                default => [],
            };
        }

        return array_map(function (RequestStatus $status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'badgeColor' => $status->badgeColor(),
                'requireNote' => in_array($status, [RequestStatus::PERLU_PERBAIKAN, RequestStatus::DITOLAK], true),
            ];
        }, array_values($targets));
    }

    /**
     * Record the initial creation status log.
     */
    public function createInitialLog(LetterRequest $letterRequest, User $user, ?string $note = null): RequestStatusLog
    {
        return RequestStatusLog::create([
            'letter_request_id' => $letterRequest->id,
            'from_status' => null,
            'to_status' => $letterRequest->status->value,
            'note' => $note,
            'changed_by' => $user->id,
            'created_at' => now(),
        ]);
    }
}
