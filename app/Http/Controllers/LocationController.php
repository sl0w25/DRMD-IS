<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function municipalities($province)
    {
        try {
            $province = urldecode($province); // <-- decode spaces
            $municipalities = Location::where('province', $province)
                ->distinct()
                ->orderBy('municipality')
                ->pluck('municipality');

            return response()->json($municipalities);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function barangays($municipality)
    {
        try {
            $municipality = urldecode($municipality); // <-- decode spaces
            $barangays = Location::where('municipality', $municipality)
                ->distinct()
                ->orderBy('barangay')
                ->pluck('barangay');

            return response()->json($barangays);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
