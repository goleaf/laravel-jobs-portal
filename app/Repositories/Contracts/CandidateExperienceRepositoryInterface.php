<?php

namespace App\Repositories\Contracts;

interface CandidateExperienceRepositoryInterface
{
    /**
     * Get experiences by candidate ID.
     *
     * @return mixed
     */
    public function getByCandidateId(int $candidateId);
}
