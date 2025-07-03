<?php

namespace App\Repositories;

use App\Models\ReportedToCandidate;
use App\Repositories\Contracts\ReportedToCandidateRepositoryInterface;

class ReportedToCandidateRepository extends BaseRepository implements ReportedToCandidateRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = ReportedToCandidate::class;

    /**
     * Get reports by candidate ID.
     *
     * @return mixed
     */
    public function getByCandidateId(int $candidateId)
    {
        return $this->model->where('candidate_id', $candidateId)->get();
    }
}
