<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithProperties;

class RekapJamKerja implements FromView, WithProperties
{
    use Exportable;
    protected $data,$periode;

    public function __construct($data,$periode)
    {
        $this->data = $data;
        $this->periode = $periode;
    }

    public function view(): View
    {
        return view('whr.exportRecap',
        [
            'data' => $this->data,
            'periode' => $this->periode,
        ]);
    }

    public function properties(): array
    {
        return [
            'creator'        => 'S.JGU',
            'lastModifiedBy' => 'S.JGU',
            'title'          => 'Rekap Jam Kerja Karyawan',
            'description'    => 'S.JGU by Ali Fikri',
            'manager'        => 'ITIC',
            'company'        => 'JGU',
        ];
    }

}
