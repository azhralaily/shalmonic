<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function controls()
    {
        return view('controls');
    }

    public function dataoverview()
    {
        return view('table');
    }

    public function apiDatastream()
    {
        if (Storage::disk('local')->exists('api_datastream.json')) {
            $data = Storage::disk('local')->get('api_datastream.json');
            return response($data, 200, ['Content-Type' => 'application/json']);
        }
        return response()->json(['error' => 'Data not found.'], 404);
    }

    public function updateManualValues(Request $request)
    {
        $request->validate([
            'ppm' => 'required|numeric|min:0',
            'vpd' => 'required|numeric|min:0',
        ]);

        if (Storage::disk('local')->exists('api_datastream.json')) {
            $data = json_decode(Storage::disk('local')->get('api_datastream.json'), true);
            
            // Update nilai manual
            $data['ppm'] = $request->ppm;
            $data['vpd'] = $request->vpd;
            
            // Simpan kembali
            Storage::disk('local')->put('api_datastream.json', json_encode($data, JSON_PRETTY_PRINT));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Manual values updated successfully',
                'data' => $data
            ]);
        }

        return response()->json(['error' => 'No data found to update'], 404);
    }

    public function apiControls(Request $request)
    {
        // Logika dari api_controls.php
        $action = $request->input('action');
        
        // Proses kontrol
        return response()->json(['status' => 'success']);
    }
}