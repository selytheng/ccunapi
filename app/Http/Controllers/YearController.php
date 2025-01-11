<?php

namespace App\Http\Controllers;

use App\Models\Year;
use Illuminate\Http\Response;

class YearController extends Controller
{
    /**
     * Get all years.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $years = Year::all();
            return response()->json($years, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
