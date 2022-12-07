<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class RekapJamKerja implements FromView
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

}
