<?php

namespace Database\Seeders;

use App\Models\Hamlet;
use App\Models\Resident;
use Illuminate\Database\Seeder;

class HamletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hamlets = [
            1 => ['name' => 'Dusun I', 'head_name' => 'Janawi'],
            2 => ['name' => 'Dusun II', 'head_name' => 'Askol'],
            3 => ['name' => 'Dusun III', 'head_name' => 'Roni Lesmana'],
            4 => ['name' => 'Dusun IV', 'head_name' => 'Juhri'],
        ];

        // 1. Pindahkan relasi penduduk dari dusun duplikat/lama ke id 1..4
        $duplicates = Hamlet::whereNotIn('id', [1, 2, 3, 4])->get();
        foreach ($duplicates as $extra) {
            $targetId = match ($extra->name) {
                'Dusun I', 'Dusun I Gelam' => 1,
                'Dusun II', 'Dusun II Sukamaju' => 2,
                'Dusun III', 'Dusun III Mekarjaya' => 3,
                'Dusun IV', 'Dusun IV Banjarsari' => 4,
                default => 1,
            };
            Resident::where('hamlet_id', $extra->id)->update(['hamlet_id' => $targetId]);
            $extra->delete();
        }

        // 2. Pastikan tepat 4 dusun utama diperbarui ke nama dan kepala dusun riil
        foreach ($hamlets as $id => $data) {
            $existing = Hamlet::find($id);
            if ($existing) {
                $existing->update($data);
            } else {
                Hamlet::create(array_merge(['id' => $id], $data));
            }
        }
    }
}
