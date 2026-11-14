<?php

namespace App\Http\Requests\Staf;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isStaf() || $this->user()->isAdmin());
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $residentId = $this->route('resident') instanceof \App\Models\Resident
            ? $this->route('resident')->id
            : $this->route('resident');

        return [
            // NIK cannot be changed via update — ignored and stripped at controller level
            'nik' => ['nullable', 'string'],
            'family_card_no' => ['nullable', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'name' => ['required', 'string', 'max:150'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['required', 'string', 'max:30'],
            'marital_status' => ['required', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:50'],
            'citizenship' => ['nullable', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:255'],
            'hamlet_id' => ['nullable', 'exists:hamlets,id'],
            'rt' => ['required', 'string', 'max:3'],
            'rw' => ['required', 'string', 'max:3'],
            'family_relation' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'religion.required' => 'Agama wajib diisi.',
            'marital_status.required' => 'Status perkawinan wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'rt.required' => 'RT wajib diisi.',
            'rw.required' => 'RW wajib diisi.',
            'family_card_no.size' => 'Nomor KK harus tepat 16 digit.',
            'family_card_no.regex' => 'Nomor KK hanya boleh berisi angka.',
        ];
    }
}

