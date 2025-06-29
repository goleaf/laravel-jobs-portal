<?php

namespace App\Repositories\Contracts;

interface CandidateRepositoryInterface
{
    /**
     * Get candidates by status.
     *
     * @param string $status
     * @return mixed
     */
    public function getByStatus(string $status);

    /**
     * Get candidates by availability.
     *
     * @param bool $isAvailable
     * @return mixed
     */
    public function getByAvailability(bool $isAvailable);

    /**
     * Search candidates by name or email.
     *
     * @param string $query
     * @return mixed
     */
    public function search(string $query);
}