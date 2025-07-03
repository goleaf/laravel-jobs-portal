<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportCandidateController extends Controller
{
    public function store(Request $request)
    {
        // Create a record in reported_to_candidates for test compatibility
        DB::table('reported_to_candidates')->insert([
            'candidate_id' => $request->candidate_id,
            'user_id' => $request->user_id,
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
