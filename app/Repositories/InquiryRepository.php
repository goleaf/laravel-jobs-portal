<?php

namespace App\Repositories;

use App\Models\Inquiry;
use App\Repositories\Contracts\InquiryRepositoryInterface;

class InquiryRepository extends BaseRepository implements InquiryRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Inquiry::class;

    /**
     * Get inquiries by status.
     *
     * @return mixed
     */
    public function getByStatus(string $status)
    {
        return $this->model->where('status', $status)->get();
    }
}
