<?php

namespace App\Http\Requests\Staf;

use App\Models\LetterType;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfflineLetterRequest extends FormRequest
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
            'resident_id' => ['required', 'exists:residents,id'],
            'letter_type_id' => ['required', 'exists:letter_types,id'],
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
            'resident_id.required' => 'Pilih data penduduk pemohon.',
            'resident_id.exists' => 'Data penduduk tidak ditemukan dalam sistem.',
            'letter_type_id.required' => 'Pilih jenis surat yang diajukan.',
            'letter_type_id.exists' => 'Jenis surat tidak valid atau tidak ditemukan.',
            'purpose.required' => 'Keperluan permohonan surat wajib diisi.',
            'purpose.max' => 'Keperluan surat maksimal 255 karakter.',
            'attachments.max' => 'Maksimal 5 berkas lampiran.',
            'attachments.*.mimes' => 'Format berkas harus JPG, JPEG, PNG, atau PDF.',
            'attachments.*.max' => 'Ukuran berkas maksimal 2 MB per dokumen.',
        ];
    }

    /**
     * Validate business rules: active letter type and dynamic form schema.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $letterTypeId = (int) $this->input('letter_type_id');
            $letterType = LetterType::find($letterTypeId);

            if (!$letterType) {
                return;
            }

            if (!$letterType->is_active) {
                $v->errors()->add('letter_type_id', 'Jenis surat ini sedang nonaktif dan belum dapat diajukan.');
                return;
            }

            // Validate dynamic form fields if schema exists
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
