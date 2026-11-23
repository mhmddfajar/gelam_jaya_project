<?php

namespace App\Http\Requests\Warga;

use App\Models\LetterRequest;
use App\Models\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->isWarga() && !empty($user->resident_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'letter_type_id' => ['required', 'exists:letter_types,id'],
            'purpose' => ['required', 'string', 'max:255'],
            'form_data' => ['nullable', 'array'],
            'attachments' => ['required', 'array', 'max:5'],
            'attachments.*' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'letter_type_id.required' => 'Pilih jenis surat yang ingin diajukan.',
            'letter_type_id.exists' => 'Jenis surat tidak valid atau tidak ditemukan.',
            'purpose.required' => 'Keperluan permohonan surat wajib diisi.',
            'purpose.max' => 'Keperluan surat maksimal 255 karakter.',
            'attachments.required' => 'Dokumen persyaratan berkas wajib diunggah.',
            'attachments.max' => 'Maksimal 5 berkas lampiran per pengajuan.',
            'attachments.*.mimes' => 'Format berkas harus JPG, JPEG, PNG, atau PDF.',
            'attachments.*.max' => 'Ukuran berkas maksimal 2 MB per dokumen.',
        ];
    }

    /**
     * Validate business rules: active duplicate request, dynamic form schema, and requirements.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $user = $this->user();
            if (!$user || !$user->resident_id) {
                $v->errors()->add('auth', 'Akun Anda belum tertaut dengan data kependudukan.');
                return;
            }

            $letterTypeId = (int) $this->input('letter_type_id');
            $letterType = LetterType::with('typeRequirements')->find($letterTypeId);

            if (!$letterType) {
                return;
            }

            if (!$letterType->is_active) {
                $v->errors()->add('letter_type_id', 'Jenis surat ini sedang nonaktif dan belum dapat diajukan.');
                return;
            }

            // Check duplicate active request
            $hasActiveRequest = LetterRequest::where('resident_id', $user->resident_id)
                ->where('letter_type_id', $letterTypeId)
                ->active()
                ->exists();

            if ($hasActiveRequest) {
                $v->errors()->add(
                    'letter_type_id',
                    "Anda masih memiliki pengajuan aktif untuk {$letterType->name} yang sedang diproses. Mohon tunggu hingga selesai atau ditolak sebelum mengajukan kembali."
                );
            }

            // Validate dynamic form fields
            $formData = $this->input('form_data', []);
            $formFields = $letterType->form_fields ?? [];

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

            // Validate requirements file presence
            // Check attachments key matching requirement IDs or count
            $uploadedFiles = $this->file('attachments', []);
            $typeRequirements = $letterType->typeRequirements;

            if ($typeRequirements->isNotEmpty()) {
                foreach ($typeRequirements as $req) {
                    if ($req->is_required) {
                        // Check if file is provided for requirement id or index
                        $reqId = $req->id;
                        $hasFile = isset($uploadedFiles[$reqId]) || isset($uploadedFiles[(string) $reqId]);

                        // Fallback: If uploaded as numeric array without specific keys, ensure count matches or exceeds required
                        if (!$hasFile && empty($uploadedFiles[$reqId])) {
                            // Check if indexed attachment covers it
                            // If user uploaded key is req name or general indexed
                            $found = false;
                            foreach ($uploadedFiles as $key => $file) {
                                if ((string) $key === (string) $reqId || (string) $key === $req->name) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found && count($uploadedFiles) < $typeRequirements->where('is_required', true)->count()) {
                                $v->errors()->add("attachments.{$reqId}", "Berkas '{$req->name}' wajib diunggah.");
                            }
                        }
                    }
                }
            }
        });
    }
}
