<?php

namespace App\Http\Controllers;

use App\Models\Datastream;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataOverviewController extends Controller
{
    public function index()
    {
        return view('dataoverview');
    }

    public function fetch(Request $request)
    {
        $limit = $request->input('limit', 25);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

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

        // Filter berdasarkan tanggal jika disediakan
        if ($startDate && $endDate) {
            $query->whereBetween('timestamp', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $data = $query->latest()->take($limit)->get();

        return response()->json($data);
    }

    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shalmonic_datalog_'.date('Y-m-d').'.csv"',
        ];

        $columns = [
            'ID', 'Timestamp', 'Temp', 'Humid', 'Light Intensity', 'PPM', 'VPD',
            'Voltage', 'Current', 'Power', 'PF', 'Freq', 'Energy'
        ];
        
        $selectColumns = [
            'id', 'timestamp', 'temp', 'humid', 'light_intensity', 'ppm', 'vpd',
            'voltage', 'current', 'power', 'pf', 'freq', 'energy'
        ];

        $callback = function() use ($columns, $selectColumns) {
            $file = fopen('php://output', 'w');
            
            // [FIXED] Menambahkan argumen ';' untuk menggunakan titik koma sebagai pemisah
            fputcsv($file, $columns, ';');

            Datastream::select($selectColumns)->latest()->chunk(500, function ($datastreams) use ($file) {
                foreach ($datastreams as $data) {
                    // [FIXED] Menambahkan argumen ';' untuk setiap baris data
                    fputcsv($file, $data->toArray(), ';');
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}