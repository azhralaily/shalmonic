<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Datastream;
use Exception;

class DataOverviewController extends Controller
{
    /**
     * Display the data overview page.
     */
    public function index()
    {
        return view('dataoverview');
    }

    /**
     * Fetch data for the table via AJAX.
     */
    public function fetchData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'sometimes|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid limit parameter.'], 400);
            }

            $limit = $request->input('limit', 25);

            $data = Datastream::orderBy('id', 'desc')
                              ->limit($limit)
                              ->get();

            return response()->json($data);
        } catch (Exception $e) {
            // Generic error for security
            return response()->json(['error' => 'Could not retrieve data from the database.'], 500);
        }
    }

    /**
     * Export all data to a CSV file.
     */
    public function exportCsv()
    {
        try {
            $headers = [
                'Content-Type'        => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="TSS_Controls_Data_Log_All_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() {
                $output = fopen('php://output', 'w');
                
                // Use a representative row to get column headers to handle empty table case
                $firstRow = Datastream::first();
                if ($firstRow) {
                    $columns = array_keys($firstRow->toArray());
                    fputcsv($output, $columns);
                }

                // Stream the rest of the data
                Datastream::orderBy('id', 'asc')->chunk(200, function ($rows) use ($output) {
                    foreach ($rows as $row) {
                        fputcsv($output, $row->toArray());
                    }
                });
                
                fclose($output);
            };

            return Response::stream($callback, 200, $headers);

        } catch (Exception $e) {
            // Redirect back with an error message if export fails
            return redirect()->route('dataoverview')->with('error', 'Could not export CSV: ' . $e->getMessage());
        }
    }
} 