<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SDExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    protected $rowNumber = 0;
    protected $centerLat;
    protected $centerLng;

    public function __construct($filters = [], $centerLat = null, $centerLng = null)
    {
        $this->filters = $filters;
        $this->centerLat = $centerLat ?? -5.2052173; // Default: Makassar center
        $this->centerLng = $centerLng ?? 119.4963776; // Default: Makassar center
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = DB::table('sekolah')->select(
            'nama',
            'npsn',
            'bentuk_pendidikan',
            'status_sekolah',
            'alamat_jalan',
            'kecamatan',
            'kabupaten',
            'provinsi',
            'kode_pos',
            'nomor_telepon',
            'akreditasi',
            'lintang',
            'bujur'
        );

        // Apply filters
        if (isset($this->filters['bentuk_pendidikan'])) {
            $query->where('bentuk_pendidikan', $this->filters['bentuk_pendidikan']);
        }

        if (isset($this->filters['kecamatan'])) {
            $query->where('kecamatan', $this->filters['kecamatan']);
        }

        if (isset($this->filters['status_sekolah'])) {
            $query->where('status_sekolah', $this->filters['status_sekolah']);
        }

        // Get the data
        $schools = $query->get();

        // Calculate distance and sort by proximity
        $schoolsWithDistance = $schools->map(function ($school) {
            $distance = $this->calculateDistance(
                $this->centerLat,
                $this->centerLng,
                floatval($school->lintang ?? 0),
                floatval($school->bujur ?? 0)
            );
            $school->distance = $distance;
            return $school;
        });

        // Sort by distance (closest first)
        return $schoolsWithDistance->sortBy('distance');
    }

    /**
     * Calculate distance between two points using Haversine formula
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        if ($lat2 == 0 || $lng2 == 0) {
            return 999999; // Put schools without coordinates at the end
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'Nama Sekolah',
            'NPSN',
            'Bentuk Pendidikan',
            'Status Sekolah',
            'Alamat',
            'Kecamatan',
            'Kabupaten/Kota',
            'Provinsi',
            'Kode Pos',
            'Telepon',
            'Akreditasi',
            'Jarak (KM)',
        ];
    }

    /**
     * @param mixed $school
     * @return array
     */
    public function map($school): array
    {
        $this->rowNumber++; // Increment nomor urut
        return [
            $this->rowNumber, // Nomor urut
            $school->nama ?? '',
            $school->npsn ?? '',
            $school->bentuk_pendidikan ?? '',
            $school->status_sekolah ?? '',
            $school->alamat_jalan ?? '',
            $school->kecamatan ?? '',
            $school->kabupaten ?? '',
            $school->provinsi ?? '',
            $school->kode_pos ?? '',
            $school->nomor_telepon ?? '',
            $school->akreditasi ?? '',
            $school->distance ?? 'N/A',
        ];
    }
}
