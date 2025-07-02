<?php

namespace App\Repositories;

use App\Models\CandidateExperience;
use App\Repositories\Contracts\CandidateExperienceRepositoryInterface;

class CandidateExperienceRepository extends BaseRepository implements CandidateExperienceRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = CandidateExperience::class;

    /**
     * Get experiences by candidate ID.
     *
     * @param int $candidateId
     * @return mixed
     */
    public function getByCandidateId(int $candidateId)
    {
        return $this->model->where('candidate_id', $candidateId)->get();
    }
} 