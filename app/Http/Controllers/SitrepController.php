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

          $user = $request->user();
          Log::info('user id' . $user->id);
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

<<<<<<< HEAD
=======
    public function submitAndEmail(Request $request, Sitrep $sitrep)
    {
        $user = $request->user();
        Log::info('user id' . $user->id);

        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string',
        ]);

        $messageContent = $request->input('message', 'No message provided');

        try {

            $sitrep->submittedBy()->associate($request->user());
            $sitrep->save();
            Log::info('Sitrep marked as submitted', ['sitrep_id' => $sitrep->id]);


            $pdf = Pdf::loadView('pdf.sitrep', ['sitrep' => $sitrep]);
            Log::info('PDF generated', ['sitrep_id' => $sitrep->id]);


            $fileName = 'sitreps/for_approval/Sitrep_No_1_on_the_' . $sitrep->incident_type . '_in_' . $sitrep->barangay . '_' . $sitrep->municipality . '_' . $sitrep->province . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());
            $filePath = storage_path('app/public/' . $fileName);
            Log::info('PDF saved to storage', ['filePath' => $filePath]);


            Mail::to($request->email)->send(new SitrepNotificationMail($sitrep, $messageContent, $filePath));
            Log::info('Email sent successfully', ['to' => $request->email, 'sitrep_id' => $sitrep->id]);

            return response()->json([
                'success' => true,
                'message' => 'Sitrep submitted and email sent with PDF attachment',
            ]);
        } catch (\Exception $e) {
            Log::error('submitAndEmail FAILED', [
                'sitrep_id' => $sitrep->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit sitrep: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function UploadApprovedSitrep(Request $request, Sitrep $sitrep)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120', // max 5 MB
            'comments' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('sitreps/approved', 'public');

            // Save the path in the database
            $sitrep->sitrep_file = $path;
            $sitrep->save();

            return response()->json([
                'message' => 'Approved Sitrep uploaded successfully!',
                'path' => $path,
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 422);
    }

>>>>>>> 1e2c656c6a583bd698a746a432ef8ce29f6c791c
}
