<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Datastream;
use Exception;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Receive data from an IoT device, save it to a JSON file for the dashboard,
     * and store it in the database for historical logging.
     */
    public function storeDatastream(Request $request)
    {
        $data = $request->json()->all();

        // 1. Validasi Input: Sesuaikan dengan data yang dikirim Arduino
        $validator = Validator::make($data, [
            'light_intensity' => 'required|numeric',
            'temp'            => 'required|numeric',
            'humid'           => 'required|numeric',
            'current'         => 'required|numeric',
            'voltage'         => 'required|numeric',
            'power'           => 'required|numeric',
            'pf'              => 'required|numeric',
            'freq'            => 'required|numeric',
            'energy'          => 'required|numeric',
            'duration'        => 'required|numeric',
            'timestamp'       => 'required|string',
            // Field yang tidak ada di Arduino - buat optional
            'batt'            => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil data yang sudah divalidasi
        $validatedData = $validator->validated();

        // Set nilai default untuk field yang tidak ada di Arduino
        $dataToSave = array_merge([
            'batt' => 0,
        ], $validatedData);

        try {
            // 2. Simpan data ke file JSON (untuk dashboard atau keperluan lain)
            Storage::disk('local')->put('api_datastream.json', json_encode($dataToSave, JSON_PRETTY_PRINT));

            // 3. Simpan data ke database menggunakan Eloquent Model
            Datastream::create($dataToSave);

            // 4. Berikan respons sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Data saved successfully to both file and database.',
                'data_received' => $dataToSave,
            ], 200);

        } catch (Exception $e) {
            // 5. Penanganan Error Internal Server
            Log::error('API Datastream Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An internal server error occurred.',
                // 'error_details' => $e->getMessage() // Hapus di production
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $minutes = intval($request->query('minutes', 10));
        $end = now()->startOfMinute();
        $start = (clone $end)->subMinutes($minutes - 1);

        // Ambil data group per menit
        $raw = Datastream::select([
                DB::raw("strftime('%Y-%m-%d %H:%M:00', created_at) as minute"),
                DB::raw('AVG(temp) as temp'),
                DB::raw('AVG(humid) as humid'),
                DB::raw('AVG(light_intensity) as light_intensity')
            ])
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('minute')
            ->orderBy('minute', 'asc')
            ->get()
            ->keyBy('minute');

        // Generate semua menit
        $minutesArr = [];
        $period = new \DatePeriod($start, new \DateInterval('PT1M'), (clone $end)->addMinute());
        foreach ($period as $dt) {
            $minute = $dt->format('Y-m-d H:i:00');
            $row = $raw[$minute] ?? null;
            $minutesArr[] = [
                'timestamp' => $minute,
                'temp' => $row->temp ?? null,
                'humid' => $row->humid ?? null,
                'light_intensity' => $row->light_intensity ?? null,
            ];
        }

        return response()->json($minutesArr);
    }
}