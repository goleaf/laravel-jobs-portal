<?php

namespace App\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class BrandingSliders
 */
class BrandingSliders extends Component
{
    use WithPagination;

    /**
     * @var string
     */
    public $searchByBrandingSlider = '';

    public $status = '';

    /**
     * @var string[]
     */
    protected $listeners = ['refresh' => '$refresh', 'deleteBrandingSlider', 'changeFilter'];

    /**
     * @var string
     */
    protected $paginationTheme = 'tailwind';

    /**
     * @var int
     */
    private $perPage = 8;

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

    public function deleteBrandingSlider($brandingSliderId)
    {
        $brandingSlider = \App\Models\BrandingSliders::findOrFail($brandingSliderId);
        $brandingSlider->delete();
        $this->dispatch('delete');
    }

    public function changeFilter($param, $value)
    {
        $this->resetPage();
        $this->$param = $value;
    }

    public function updatingSearchByEmployee()
    {
        $this->resetPage();
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $brandingSliders = $this->brandingSlider();

        return view('livewire.branding-sliders', compact('brandingSliders'));
    }

    public function brandingSlider(): LengthAwarePaginator
    {
        $query = \App\Models\BrandingSliders::with('media')->select('branding_sliders.*');

        $query->when(isset($this->searchByBrandingSlider) && $this->searchByBrandingSlider != '', function (Builder $q) {
            $q->where('title', 'like',
                '%'.strtolower($this->searchByBrandingSlider).'%');
        });

        $query->when(($this->status != '') && $this->status == 1,
            function (Builder $q) {
                $q->where('is_active', '=', 1);
            });

        $query->when(($this->status != '') && $this->status == 0,
            function (Builder $q) {
                $q->where('is_active', '=', 0);
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
