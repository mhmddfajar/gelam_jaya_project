<?php

namespace App\Http\Requests\Warga;

use App\Enums\RequestStatus;
use App\Models\LetterRequest;
use Illuminate\Foundation\Http\FormRequest;

class ResubmitLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user || !$user->isWarga()) {
            return false;
        }

        /** @var LetterRequest|null $letterRequest */
        $letterRequest = $this->route('letter_request');
        if (!$letterRequest) {
            return false;
        }

        return ($letterRequest->resident_id === $user->resident_id || $letterRequest->submitted_by === $user->id)
            && $letterRequest->status === RequestStatus::PERLU_PERBAIKAN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'purpose' => ['required', 'string', 'max:255'],
            'form_data' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'purpose.required' => 'Keperluan permohonan surat wajib diisi.',
            'purpose.max' => 'Keperluan surat maksimal 255 karakter.',
            'attachments.max' => 'Maksimal 5 berkas lampiran.',
            'attachments.*.mimes' => 'Format berkas harus JPG, JPEG, PNG, atau PDF.',
            'attachments.*.max' => 'Ukuran berkas maksimal 2 MB per dokumen.',
        ];
    }

    /**
     * Validate business rules: dynamic form schema and mandatory requirements.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            /** @var LetterRequest $letterRequest */
            $letterRequest = $this->route('letter_request');
            if (!$letterRequest) {
                return;
            }

            $letterType = $letterRequest->letterType()->with('typeRequirements')->first();
            if (!$letterType) {
                return;
            }

            // Validate dynamic form fields
            $formData = $this->input('form_data', []);
            $formFields = $letterType->form_fields ?? $letterType->form_schema ?? [];

            foreach ($formFields as $field) {
                $fieldName = $field['name'] ?? $field['key'] ?? '';
                $fieldLabel = $field['label'] ?? $fieldName;
                $isRequired = (bool) ($field['required'] ?? false);
                $value = $formData[$fieldName] ?? null;

                if ($isRequired && (is_null($value) || trim((string) $value) === '')) {
                    $v->errors()->add("form_data.{$fieldName}", "Kolom '{$fieldLabel}' wajib diisi.");
                }

                if (!is_null($value) && trim((string) $value) !== '') {
                    $type = $field['type'] ?? 'text';
                    if ($type === 'number' && !is_numeric($value)) {
                        $v->errors()->add("form_data.{$fieldName}", "Kolom '{$fieldLabel}' harus berupa angka.");
                    }
                    if ($type === 'select' && !empty($field['options'])) {
                        $allowedOptions = (array) $field['options'];
                        if (!in_array($value, $allowedOptions, true)) {
                            $v->errors()->add("form_data.{$fieldName}", "Pilihan pada kolom '{$fieldLabel}' tidak valid.");
                        }
                    }
                }
            }
        });
    }
}
