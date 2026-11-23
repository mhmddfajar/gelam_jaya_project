<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLetterTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Prepare inputs for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }

        // Clean empty requirements
        if ($this->has('requirements') && is_array($this->input('requirements'))) {
            $cleanReqs = array_values(array_filter(
                array_map(fn ($r) => trim((string) $r), $this->input('requirements')),
                fn ($r) => $r !== ''
            ));
            $this->merge(['requirements' => $cleanReqs]);
        }

        // Clean dynamic form fields
        if ($this->has('form_fields') && is_array($this->input('form_fields'))) {
            $cleanFields = [];
            foreach ($this->input('form_fields') as $field) {
                if (empty($field['name']) && empty($field['label'])) {
                    continue;
                }
                $name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', trim($field['name'] ?? $field['label'] ?? '')));
                $options = $field['options'] ?? [];
                if (is_string($options)) {
                    $options = array_values(array_filter(array_map('trim', explode(',', $options))));
                }

                $cleanFields[] = [
                    'label' => trim($field['label'] ?? ''),
                    'name' => $name,
                    'key' => $name,
                    'type' => $field['type'] ?? 'text',
                    'required' => (bool) ($field['required'] ?? false),
                    'options' => $options,
                ];
            }
            $this->merge(['form_fields' => $cleanFields]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'unique:letter_types,code'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['required', 'string', 'max:150'],
            'form_fields' => ['nullable', 'array', 'max:15'],
            'form_fields.*.label' => ['required', 'string', 'max:100'],
            'form_fields.*.name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/'],
            'form_fields.*.type' => ['required', 'in:text,textarea,number,date,select'],
            'form_fields.*.required' => ['nullable', 'boolean'],
            'form_fields.*.options' => ['nullable', 'array'],
            'template' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode jenis surat wajib diisi.',
            'code.unique' => 'Kode jenis surat sudah digunakan.',
            'code.max' => 'Kode surat maksimal 20 karakter.',
            'name.required' => 'Nama jenis surat wajib diisi.',
            'name.max' => 'Nama surat maksimal 150 karakter.',
            'form_fields.max' => 'Maksimal 15 field input per jenis surat.',
            'form_fields.*.label.required' => 'Label kolom formulir wajib diisi.',
            'form_fields.*.name.required' => 'Nama kolom (identifier) wajib diisi.',
            'form_fields.*.type.required' => 'Tipe input formulir wajib dipilih.',
            'form_fields.*.type.in' => 'Tipe input formulir tidak valid.',
        ];
    }
}
