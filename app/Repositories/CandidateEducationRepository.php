<?php

namespace App\Repositories;

use App\Models\CandidateEducation;
use App\Repositories\Contracts\CandidateEducationRepositoryInterface;

class CandidateEducationRepository extends BaseRepository implements CandidateEducationRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = CandidateEducation::class;

    /**
     * Get educations by candidate ID.
     *
     * @return mixed
     */
    public function getByCandidateId(int $candidateId)
    {
        return $this->model->where('candidate_id', $candidateId)->get();
    }
}
