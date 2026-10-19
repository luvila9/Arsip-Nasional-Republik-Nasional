<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aspek;
use App\Models\SubAspek;
use App\Models\SubSubAspek;
use Illuminate\Support\Facades\Schema;

class AspekSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("Membersihkan data aspek lama...");
        Schema::disableForeignKeyConstraints();
        SubSubAspek::truncate();
        SubAspek::truncate();
        Aspek::truncate();
        Schema::enableForeignKeyConstraints();

        $files = glob(database_path('seeders/data_aspek/*.csv'));

        if (empty($files)) {
            $this->command->error("Tidak ada file CSV yang ditemukan! Pastikan Anda menaruhnya di database/seeders/data_aspek/");
            return;
        }

        $currentAspekId = null;
        $currentSubAspekId = null;

        $this->command->info("Ditemukan " . count($files) . " file CSV. Mulai memproses...");

        foreach ($files as $file) {
            $this->command->info("Membaca file: " . basename($file) . "...");
            $handle = fopen($file, "r");

            while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                $no = isset($data[0]) ? trim($data[0]) : '';
                $pernyataan = isset($data[1]) ? trim($data[1]) : '';

                if (empty($pernyataan)) continue;

                if (preg_match('/^[IVX]+\.?$/i', $no)) {
                    $aspek = Aspek::create(['nama_aspek' => $pernyataan]);
                    $currentAspekId = $aspek->id;
                }
                elseif (preg_match('/^[0-9]+\.?$/', $no)) {
                    if ($currentAspekId) {
                        $subAspek = SubAspek::create(['aspek_id' => $currentAspekId, 'nama_sub_aspek' => $pernyataan]);
                        $currentSubAspekId = $subAspek->id;
                    }
                }
                elseif (preg_match('/^[a-z]\.?$/i', $no)) {
                    if ($currentSubAspekId) {
                        SubSubAspek::create(['sub_aspek_id' => $currentSubAspekId, 'nama_sub_sub_aspek' => $pernyataan]);
                    }
                }
            }
            fclose($handle);
        }

        $this->command->info("Hore! SEMUA Data Aspek dari berbagai file berhasil digabungkan ke Database!");
    }
}