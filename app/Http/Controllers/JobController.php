<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Http\Requests\JobRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JobController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Job::class);
        // List jobs
        $jobs = Job::all();
        return view('jobs.index', compact('jobs'));
    }

    public function create()
    {
        Gate::authorize('create', Job::class);
        return view('jobs.create');
    }

    public function store(JobRequest $request)
    {
        Gate::authorize('create', Job::class);
        $job = Job::create($request->validated());
        return redirect()->route('jobs.index')->with('success', 'Job created successfully.');
    }

    public function show(Job $job)
    {
        Gate::authorize('view', $job);
        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        Gate::authorize('update', $job);
        return view('jobs.edit', compact('job'));
    }

    public function update(JobRequest $request, Job $job)
    {
        Gate::authorize('update', $job);
        $job->update($request->validated());
        return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        Gate::authorize('delete', $job);
        $job->delete();
        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }
} 