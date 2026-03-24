<?php

namespace App\Http\Controllers;

use App\Models\Sitrep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class SitrepController extends Controller
{


    public function index()
    {
        $sitreps = Sitrep::orderBy('incident_date', 'desc')->paginate(10);

        return Inertia::render('Incident/List', [
            'sitreps' => $sitreps
        ]);
    }

    public function print($id)
    {
        $sitrep = Sitrep::findOrFail($id);

        $pdf = Pdf::loadView('pdf.sitrep', [
            'sitrep' => $sitrep
        ]);

        return $pdf->stream('minor_incident.pdf');
    }

      public function store(Request $request)
    {

          Log::info('Authenticated User ID:', ['user_id' => auth()->id()]);
          Log::info('Incoming QR Scan:', $request->all());

        $sitrep = Sitrep::create([
            'incident_type' => $request->incident_type,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
            'province' => $request->province,
            'municipality' => $request->municipality,
            'barangay' => $request->barangay,
            'overview' => $request->overview,

            'affected_families' => $request->affected_families,
            'affected_individuals' => $request->affected_individuals,
            'partially_damaged' => $request->partially_damaged,
            'totally_damaged' => $request->totally_damaged,

            'open_ec' => $request->open_ec,
            'displaced_family' => $request->displaced_family,
            'displaced_individual' => $request->displaced_individual,

            'dswd_cost' => $request->dswd_cost,
            'lgu_cost' => $request->lgu_cost,
            'ngo_cost' => $request->ngo_cost,
            'partners_cost' => $request->partners_cost,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Sitrep created',
            'data' => $sitrep
        ]);
    }

}
