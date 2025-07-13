<?php

namespace App\Http\Controllers;

use App\Models\Datastream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    /**
     * [MODIFIED] Method untuk menyimpan data dari hardware.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temp' => 'required|numeric',
            'humid' => 'required|numeric',
            'light_intensity' => 'sometimes|numeric',
            'ppm' => 'sometimes|numeric',
            'voltage' => 'sometimes|numeric',
            'current' => 'sometimes|numeric',
            'power' => 'sometimes|numeric',
            'pf' => 'sometimes|numeric',
            'freq' => 'sometimes|numeric',
            'energy' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        // Hitung VPD
        if (is_numeric($validated['temp']) && is_numeric($validated['humid'])) {
            $vpd = 0.611 * exp(17.502 * $validated['temp'] / ($validated['temp'] + 240.97)) * (1 - ($validated['humid'] / 100));
            $validated['vpd'] = round($vpd, 4);
        }

        // --- [FIXED] Tambahkan timestamp saat ini ke dalam data ---
        $validated['timestamp'] = now();
        // ---------------------------------------------------------

        $datastream = Datastream::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data stored successfully.',
            'data' => $datastream
        ], 201);
    }
    
    // ... method lainnya (datastream(), history()) tidak perlu diubah ...
    public function datastream()
    {
        $latestData = Datastream::latest()->first();
        if (!$latestData) {
            return response()->json(['error' => 'No data available in database.'], 404);
        }
        return response()->json($latestData);
    }

    public function history(Request $request)
    {
        $minutes = $request->input('minutes', 10);
        $start = now()->subMinutes($minutes);
        $end = now();
        $query = Datastream::whereBetween('created_at', [$start, $end]);
        $query->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as minute"),
            DB::raw("AVG(temp) as temp"),
            DB::raw("AVG(humid) as humid"),
            DB::raw("AVG(light_intensity) as light_intensity")
        )
        ->groupBy('minute')
        ->orderBy('minute', 'asc');
        $data = $query->get();
        return response()->json($data);
    }
}