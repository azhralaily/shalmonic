<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ControlsController extends Controller
{
    private function getControlsData()
    {
        $defaults = ['light_intensity' => 70, 'schedule' => 0, 'light_status' => 0];
        if (!Storage::disk('local')->exists('api_controls.json')) {
            return $defaults;
        }
        $data = json_decode(Storage::disk('local')->get('api_controls.json'), true);
        return $data ?: $defaults;
    }
    
    // [MODIFIED] Fungsi baru untuk membaca data PPM/VPD dari file datastream
    private function getDatastreamData()
    {
        $defaults = ['ppm' => 0, 'vpd' => 0];
        if (!Storage::disk('local')->exists('api_datastream.json')) {
            return $defaults;
        }
        $data = json_decode(Storage::disk('local')->get('api_datastream.json'), true);
        return [
            'ppm' => $data['ppm'] ?? $defaults['ppm'],
            'vpd' => $data['vpd'] ?? $defaults['vpd'],
        ];
    }

    public function index()
    {
        $controls_state = $this->getControlsData();
        $manual_values = $this->getDatastreamData();

        return view('controls', [
            'initial_intensity' => $controls_state['light_intensity'],
            'initial_schedule' => $controls_state['schedule'],
            'initial_light_status' => $controls_state['light_status'],
            'initial_ppm' => $manual_values['ppm'], // Kirim PPM ke view
            'initial_vpd' => $manual_values['vpd'], // Kirim VPD ke view
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'light_intensity' => 'required|integer|min:0|max:100',
            'schedule' => 'required|integer|min:0|max:3',
            'light_status' => 'sometimes|integer|min:0|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $current_data = $this->getControlsData();
        $validated = $validator->validated();
        $clean_data = [
            'light_intensity' => $validated['light_intensity'],
            'schedule' => $validated['schedule'],
            'light_status' => $validated['light_status'] ?? $current_data['light_status'] ?? 0,
        ];

        Storage::disk('local')->put('api_controls.json', json_encode($clean_data, JSON_PRETTY_PRINT));
        return response()->json(['status' => 'success', 'message' => 'Controls saved.']);
    }

    // [MODIFIED] Fungsi baru untuk menyimpan PPM/VPD ke file datastream
    public function updateManualValues(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ppm' => 'sometimes|numeric|min:0',
            'vpd' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $datastreamJson = Storage::disk('local')->exists('api_datastream.json')
            ? Storage::disk('local')->get('api_datastream.json')
            : '{}';
        $datastreamData = json_decode($datastreamJson, true) ?: [];

        $updatedData = array_merge($datastreamData, $request->only(['ppm', 'vpd']));

        Storage::disk('local')->put('api_datastream.json', json_encode($updatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return response()->json(['status' => 'success', 'message' => 'Manual values updated.']);
    }
    
    // --- API untuk Hardware (Tidak perlu diubah) ---
    public function apiGetControls() { /* ... */ }
    public function apiStoreControls(Request $request) { /* ... */ }
    public function apiGetControlsHardware() { /* ... */ }
}