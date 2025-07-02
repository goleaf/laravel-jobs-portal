<?php

namespace App\Repositories;

use App\Models\Term;
use App\Repositories\Contracts\TermRepositoryInterface;

class TermRepository extends BaseRepository implements TermRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Term::class;

    /**
     * Get terms by type.
     *
     * @param string $type
     * @return mixed
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }
} 