<?php

namespace App\Actions;

use App\Models\Candidate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class RegisterCandidate
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(array $data): Candidate
    {
        return DB::transaction(function () use ($data) {
            $candidate = Candidate::create([
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'email' => $data['email'],
                'password' => isset($data['password']) ? Hash::make($data['password']) : null,
            ]);

            try {
                UpdateCandidateRecommendations::dispatch($candidate);
            } catch (\Throwable $e) {
                Log::warning('Post registration tasks failed', [
                    'candidate_id' => $candidate->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $candidate;
        });
    }
}
