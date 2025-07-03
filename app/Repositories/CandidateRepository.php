<?php

namespace App\Repositories;

use App\Models\Candidate;
use App\Repositories\Contracts\CandidateRepositoryInterface;

class CandidateRepository extends BaseRepository implements CandidateRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Candidate::class;

    /**
     * Get candidates by status.
     *
     * @return mixed
     */
    public function getByStatus(string $status)
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get candidates by availability.
     *
     * @return mixed
     */
    public function getByAvailability(bool $isAvailable)
    {
        return $this->model->where('is_available', $isAvailable)->get();
    }

    /**
     * Search candidates by name or email.
     *
     * @return mixed
     */
    public function search(string $query)
    {
        return $this->model->where('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->get();
    }
}
