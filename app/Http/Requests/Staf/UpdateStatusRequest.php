<?php

namespace App\Http\Requests\Staf;

use App\Enums\RequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isStaf() || $user->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(RequestStatus::class)],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Pilih status baru pengajuan.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status yang dipilih tidak valid.',
            'note.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
