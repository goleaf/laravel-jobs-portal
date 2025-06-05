<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\AppBaseController;
use App\Models\Candidate;
use App\Models\User;
use App\Repositories\Candidates\CandidateRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Http\Requests\CreateCandidateRequest;

use App\Http\Requests\UpdateCandidateRequest;

use App\Http\Requests\GetCandidatesListsCandidateRequest;

class CandidateController extends AppBaseController
{
    /** @var CandidateRepository */
    private $candidateRepository;

    public function __construct(CandidateRepository $candidateRepo)
    {
        $this->candidateRepository = $candidateRepo;
    }

    /**
     * Display a listing of candidates for admin
     */
    public function index(): View
    {
        $candidates = Candidate::with('user')->paginate(15);
        return view('admin.candidates.index', compact('candidates'));
    }

    /**
     * Show the form for creating a new candidate
     */
    public function create(): View
    {
        return view('admin.candidates.create');
    }

    /**
     * Store a newly created candidate
     */
    public function store(CreateCandidateRequest $request)
    {
        // Implementation for storing candidate
        return redirect()->route('admin.candidates.index')->with('success', 'Candidate created successfully');
    }

    /**
     * Display the specified candidate
     */
    public function show($id): View
    {
        $candidate = Candidate::with('user')->findOrFail($id);
        return view('admin.candidates.show', compact('candidate'));
    }

    /**
     * Show the form for editing the specified candidate
     */
    public function edit($id): View
    {
        $candidate = Candidate::with('user')->findOrFail($id);
        return view('admin.candidates.edit', compact('candidate'));
    }

    /**
     * Update the specified candidate
     */
    public function update(UpdateCandidateRequest $request, $id)
    {
        // Implementation for updating candidate
        return redirect()->route('admin.candidates.index')->with('success', 'Candidate updated successfully');
    }

    /**
     * Remove the specified candidate
     */
    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->delete();
        return redirect()->route('admin.candidates.index')->with('success', 'Candidate deleted successfully');
    }

    /**
     * @return Application|Factory|View
     */
    public function getCandidateDetails($uniqueId): View
    {
        $candidate = Candidate::whereUniqueId($uniqueId)->first();
        $data = $this->candidateRepository->getCandidateDetail($candidate->id);

        return view('front_web_template.candidate.candidate_details')->with($data);
    }

    /**
     * @return Application|Factory|View
     */
    public function getCandidatesLists(GetCandidatesListsCandidateRequest $request): View
    {
        return view('front_web_template.candidate.index');
    }
}
