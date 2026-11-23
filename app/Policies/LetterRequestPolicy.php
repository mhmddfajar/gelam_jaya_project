<?php

namespace App\Policies;

use App\Models\LetterRequest;
use App\Models\RequestAttachment;
use App\Models\User;

class LetterRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LetterRequest $letterRequest): bool
    {
        if ($user->isAdmin() || $user->isStaf()) {
            return true;
        }

        // Warga only can view their own request
        return $user->isWarga() && (
            $letterRequest->resident_id === $user->resident_id ||
            $letterRequest->submitted_by === $user->id
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isWarga() && !empty($user->resident_id);
    }

    /**
     * Determine whether the user can download an attachment.
     */
    public function downloadAttachment(User $user, LetterRequest $letterRequest, RequestAttachment $attachment): bool
    {
        // Must belong to this letter request
        if ($attachment->letter_request_id !== $letterRequest->id) {
            return false;
        }

        if ($user->isAdmin() || $user->isStaf()) {
            return true;
        }

        return $user->isWarga() && (
            $letterRequest->resident_id === $user->resident_id ||
            $letterRequest->submitted_by === $user->id
        );
    }
}
