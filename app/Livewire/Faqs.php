<?php

namespace App\Livewire;

use App\Models\FAQ;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class Faqs
 */
class Faqs extends Component
{
    use WithPagination;

    /**
     * @var string
     */
    public $searchByFaq = '';

    /**
     * @var string[]
     */
    protected $listeners = ['refresh' => '$refresh', 'deleteFaq'];

    /**
     * @var string
     */
    protected $paginationTheme = 'tailwind';

    /**
     * @var int
     */
    private $perPage = 16;

    public function paginationView(): string
    {
        return 'vendor.livewire.tailwind';
    }

    public function nextPage($lastPage)
    {
        if ($this->page < $lastPage) {
            $this->page = $this->page + 1;
        }
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page = $this->page - 1;
        }
    }

    public function deleteFaq($faqId)
    {
        $faq = FAQ::findOrFail($faqId);
        $faq->delete();
        $this->dispatch('delete');
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $faqs = $this->faq();

        return view('livewire.faqs', compact('faqs'));
    }

    public function faq(): LengthAwarePaginator
    {
        $query = FAQ::query()->select('faqs.*');

        $query->when(isset($this->searchByFaq) && $this->searchByFaq != '', function (Builder $q) {
            $q->where('title', 'like',
                '%'.strtolower($this->searchByFaq).'%');
        });

        $all = $query->paginate($this->perPage);
        $currentPage = $all->currentPage();
        $lastPage = $all->lastPage();
        if ($currentPage > $lastPage) {
            $this->page = $lastPage;
            $all = $query->paginate($this->perPage);
        }

        return $all;
    }
}
