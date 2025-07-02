<?php

namespace App\Repositories\Contracts;

interface CandidateExperienceRepositoryInterface
{
    /**
     * Get experiences by candidate ID.
     *
     * @param int $candidateId
     * @return mixed
     */
    public function getByCandidateId(int $candidateId);
} 