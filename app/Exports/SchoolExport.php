<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SchoolExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = School::query();

        // Apply filters
        if (isset($this->filters['education_type'])) {
            $query->where('education_type', $this->filters['education_type']);
        }

        if (isset($this->filters['district'])) {
            $query->where('address', 'like', '%' . $this->filters['district'] . '%');
        }

        if (isset($this->filters['school_type'])) {
            $query->where('school_type', $this->filters['school_type']);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Sekolah',
            'NPSN',
            'Alamat',
            'Latitude',
            'Longitude',
            'Telepon',
            'Email',
            'Website',
            'Jenis Pendidikan',
            'Status Sekolah',
            'Nama Kepala Sekolah',
            'NIP Kepala Sekolah',
            'Jumlah Siswa',
            'Jumlah Guru',
            'Fasilitas Praktik',
            'Fasilitas Olahraga',
            'Kurikulum',
            'Ekstrakurikuler',
            'Prestasi',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    /**
     * @param mixed $school
     * @return array
     */
    public function map($school): array
    {
        return [
            $school->id,
            $school->name,
            $school->npsn,
            $school->address,
            $school->latitude,
            $school->longitude,
            $school->phone,
            $school->email,
            $school->website,
            $school->education_type,
            $school->school_type,
            $school->principal_name,
            $school->principal_nip,
            $school->student_count,
            $school->teacher_count,
            $school->practice_facility,
            $school->sports_facility,
            $school->curriculum,
            $school->extracurricular,
            $school->achievements,
            $school->created_at,
            $school->updated_at
        ];
    }
}
