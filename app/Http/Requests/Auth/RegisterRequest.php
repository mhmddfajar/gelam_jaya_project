<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Step 1: User Account
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Step 2: Citizen Data (KTP)
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'name' => ['required', 'string', 'max:150'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['required', 'string', 'max:30'],
            'marital_status' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'rt' => ['required', 'string', 'max:3'],
            'rw' => ['required', 'string', 'max:3'],

            // Optional fields
            'family_card_no' => ['nullable', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:50'],
            'citizenship' => ['nullable', 'string', 'max:10'],
            'hamlet_id' => ['nullable', 'exists:hamlets,id'],
            'family_relation' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit angka.',
            'nik.regex' => 'NIK harus berupa 16 digit angka.',
            'name.required' => 'Nama lengkap wajib diisi sesuai KTP.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'religion.required' => 'Agama wajib diisi.',
            'marital_status.required' => 'Status perkawinan wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'rt.required' => 'Nomor RT wajib diisi.',
            'rw.required' => 'Nomor RW wajib diisi.',
            'family_card_no.size' => 'Nomor Kartu Keluarga harus 16 digit.',
            'family_card_no.regex' => 'Nomor Kartu Keluarga harus berupa digit angka.',
        ];
    }
}

