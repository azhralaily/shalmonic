<?php

namespace App\Http\Controllers;

use App\Models\Datastream;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse; // <-- Tambahkan ini untuk export

class DataOverviewController extends Controller
{
    /**
     * Menampilkan halaman utama Data Overview.
     */
    public function index()
    {
        return view('dataoverview');
    }

    /**
     * Mengambil data dari database berdasarkan rentang tanggal.
     */
    public function fetch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|date_format:Y-m-d',
            'end_date' => 'sometimes|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        // [DIUBAH] Memilih kolom secara spesifik dengan urutan yang benar
        $query = Datastream::select(
            'id',
            'timestamp',
            'temp',
            'humid',
            'light_intensity',
            'ppm',
            'vpd',
            'voltage',
            'current',
            'power',
            'pf',
            'freq',
            'energy'
        );

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $endDate = $request->end_date . ' 23:59:59';
            $query->whereBetween('timestamp', [$startDate, $endDate]);
        }

        $data = $query->orderBy('timestamp', 'asc')->get();

        return response()->json($data);
    }
    
    /**
     * Mengekspor data yang difilter ke CSV.
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|date_format:Y-m-d',
            'end_date' => 'sometimes|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? $request->start_date . ' 00:00:00' : null;
        $endDate = $request->end_date ? $request->end_date . ' 23:59:59' : null;

        $filename = "shalmonic_data_from_{$request->start_date}_to_{$request->end_date}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        // [DIUBAH] Tentukan header dan kolom yang akan diekspor
        $columns = [
            'ID', 'Timestamp', 'Temp', 'Humid', 'Light Intensity', 'PPM', 'VPD',
            'Voltage', 'Current', 'Power', 'PF', 'Freq', 'Energy'
        ];
        
        $selectColumns = [
            'id', 'timestamp', 'temp', 'humid', 'light_intensity', 'ppm', 'vpd',
            'voltage', 'current', 'power', 'pf', 'freq', 'energy'
        ];

        $callback = function() use ($columns, $selectColumns, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query = Datastream::select($selectColumns)->orderBy('timestamp', 'desc');

            if ($startDate && $endDate) {
                $query->whereBetween('timestamp', [$startDate, $endDate]);
            }

            // Ambil data dalam potongan (chunks) agar tidak membebani memori
            $query->chunk(1000, function ($datastreams) use ($file) {
                foreach ($datastreams as $data) {
                    fputcsv($file, $data->toArray());
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}