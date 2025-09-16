<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanPembayaranExport implements FromView
{
     protected $data;

     public function __construct($data)
     {
          $this->data = $data;
     }

     public function view(): View
     {
          // Arahkan ke view baru yang tidak memiliki header
          return view('admin.laporan.excel_export', [
               'pembayaran' => $this->data
          ]);
     }
}
