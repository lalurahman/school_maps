<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://api.ahadunstudio.id/api/sekolah');
        if ($response->successful()) {
            $schools = $response->json('data');

            foreach ($schools as $item) {
                // Check if the school already exists
                $existingSchool = School::where('npsn', $item['npsn'] ?? null)->first();
                if ($existingSchool) {
                    // If the school already exists, skip to the next iteration
                    continue;
                } else {
                    School::create([
                        'npsn' => $item['npsn'] ?? null,
                        'name' => $item['nama'] ?? null,
                        'address' => $item['alamat_jalan'] ?? null,
                        'latitude' => $item['lintang'] ?? null,
                        'longitude' => $item['bujur'] ?? null,
                        'phone' => $item['nomor_telepon'] ?? null,
                        'email' => $item['email'] ?? null,
                        'website' => $item['website'] ?? null,
                        'education_type' => $item['bentuk_pendidikan'] ?? null,
                        'school_type' => $item['status_sekolah'] ?? null,
                        'principal_name' => $item['nama_kepala_sekolah'] ?? null,
                        'practice_facility' => $item['fasilitas_praktik'] ?? null,
                        'sports_facility' => $item['fasilitas_olahraga'] ?? null,
                        'curriculum' => $item['kurikulum'] ?? null,
                        'extracurricular' => $item['ekstrakurikuler'] ?? null,
                        'achievements' => $item['prestasi'] ?? null,
                        'student_count' => $item['jumlah_siswa'] ?? 0,
                        'teacher_count' => $item['jumlah_guru'] ?? 0,
                    ]);
                    $this->command->info('Sekolah ' . $item['nama'] . ' berhasil di-generate.');
                }
            }
            $this->command->info(count($schools) . ' Sekolah berhasil di-generate.');
        } else {
            $this->command->error('Gagal mengambil data sekolah dari API.');
        }
    }
}
