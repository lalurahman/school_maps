<?php

namespace App\Http\Controllers;

use App\Exports\SchoolExport;
use App\Exports\SDExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export($bentuk_pendidikan, $kecamatan)
    {
        $filters = [
            'bentuk_pendidikan' => $bentuk_pendidikan,
            'kecamatan' => $kecamatan
        ];

        return Excel::download(new SDExport($filters), 'data_sekolah_' . strtolower($bentuk_pendidikan) . '_' . str_replace(' ', '_', strtolower($kecamatan)) . '.xlsx');
    }

    /**
     * Export schools sorted by distance from a specific point
     */
    public function exportByLocation(Request $request)
    {
        $filters = [];

        // Get filters from request
        if ($request->has('bentuk_pendidikan') && $request->bentuk_pendidikan) {
            $filters['bentuk_pendidikan'] = $request->bentuk_pendidikan;
        }

        if ($request->has('kecamatan') && $request->kecamatan) {
            $filters['kecamatan'] = $request->kecamatan;
        }

        if ($request->has('status_sekolah') && $request->status_sekolah) {
            $filters['status_sekolah'] = $request->status_sekolah;
        }

        // Get center coordinates (default to Makassar center if not provided)
        $centerLat = $request->get('lat', -5.135399);
        $centerLng = $request->get('lng', 119.423790);

        // Generate filename
        $filename = 'data_sekolah_terdekat';
        if (isset($filters['bentuk_pendidikan'])) {
            $filename .= '_' . strtolower($filters['bentuk_pendidikan']);
        }
        if (isset($filters['kecamatan'])) {
            $filename .= '_' . str_replace([' ', '.'], '_', strtolower($filters['kecamatan']));
        }
        $filename .= '.xlsx';

        return Excel::download(new SDExport($filters, $centerLat, $centerLng), $filename);
    }

    /**
     * Export SD schools in Kec. Tamalate sorted by distance
     */
    public function exportSDTamalateByLocation(Request $request)
    {
        $filters = [
            'bentuk_pendidikan' => 'SD',
            'kecamatan' => 'Kec. Tamalate'
        ];

        // Get center coordinates (default to Tamalate center)
        $centerLat = $request->get('lat', -5.1876); // Koordinat pusat Tamalate
        $centerLng = $request->get('lng', 119.4273);

        return Excel::download(
            new SDExport($filters, $centerLat, $centerLng),
            'data_sekolah_sd_tamalate_terdekat.xlsx'
        );
    }
}
