<?php

namespace Database\Seeders;

use App\Models\Official;
use Illuminate\Database\Seeder;

class OfficialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $officials = [
            [
                'name' => 'Moch. Sanusi, AR',
                'position' => 'Kepala Desa',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ahmad Beben Bahrowi',
                'position' => 'Sekretaris Desa',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => true,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Husin Afroni, ST',
                'position' => 'Kasi Pemerintahan',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'M. Abdul Rohim',
                'position' => 'Kasi Kesejahteraan',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Aping',
                'position' => 'Kasi Pelayanan',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Moch. Wildanul Alim, MS',
                'position' => 'Kaur Keuangan',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Achmad Sururi, MS',
                'position' => 'Kaur Perencanaan',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Danu Anggara',
                'position' => 'Kaur Umum & TU',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Janawi',
                'position' => 'Kepala Dusun I',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Askol',
                'position' => 'Kepala Dusun II',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Roni Lesmana',
                'position' => 'Kepala Dusun III',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Juhri',
                'position' => 'Kepala Dusun IV',
                'period_start' => 2021,
                'period_end' => 2027,
                'can_sign' => false,
                'sort_order' => 12,
                'is_active' => true,
            ],
        ];

        Official::query()->delete();

        foreach ($officials as $data) {
            Official::create($data);
        }
    }
}
