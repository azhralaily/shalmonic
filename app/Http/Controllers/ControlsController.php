<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ControlsController extends Controller
{  
    private function getControlsData()
    {
        $defaults = [
            'light_intensity' => 70,
            'schedule' => 0,
            'light_status' => 0,
        ];

        if (!Storage::disk('local')->exists('api_controls.json')) {
            return $defaults;
        }

        $jsonString = Storage::disk('local')->get('api_controls.json');
        $data = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE || !$data) {
            return $defaults;
        }

        return [
            'light_intensity' => $data['light_intensity'] ?? $defaults['light_intensity'],
            'schedule' => $data['schedule'] ?? $defaults['schedule'],
            'light_status' => $data['light_status'] ?? $defaults['light_status'],
        ];
    }

    public function index()
    {
        $controls_state = $this->getControlsData();
        return view('controls', [
            'initial_intensity' => $controls_state['light_intensity'],
            'initial_schedule' => $controls_state['schedule'],
            'initial_light_status' => $controls_state['light_status'],
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
            return response()->json(['status' => 'error', 'message' => 'Invalid data.', 'errors' => $validator->errors()], 400);
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

    /**
     * API endpoint untuk hardware Arduino - GET controls
     */
    public function apiGetControls()
    {
        $controls = $this->getControlsData();
        return response()->json($controls);
    }

    /**
     * API endpoint untuk hardware Arduino - POST controls
     */
    public function apiStoreControls(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'light_intensity' => 'sometimes|integer|min:0|max:100',
            'schedule' => 'sometimes|integer|min:0|max:3',
            'light_status' => 'sometimes|integer|min:0|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        $current_data = $this->getControlsData();
        $incoming_data = $request->all();

        // Merge data tanpa replace key yang tidak ada di request
        $clean_data = array_replace($current_data, $incoming_data);

        Storage::disk('local')->put('api_controls.json', json_encode($clean_data, JSON_PRETTY_PRINT));

        return response()->json([
            'message' => 'Data updated successfully',
            'updated_data' => $incoming_data
        ]);
    }

    /**
     * API endpoint untuk hardware Arduino - GET controls (tanpa authentication)
     * Endpoint ini bisa diakses langsung oleh hardware tanpa CSRF token
     */
    public function apiGetControlsHardware()
    {
        $controls = $this->getControlsData();
        return response()->json($controls);
    }
} 