<?php

namespace App\Livewire;

use App\Models\CareerLevel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class CareerLevels
 */
class CareerLevels extends Component
{
    use WithPagination;

    /**
     * @var string
     */
    public $searchByCareerLevel = '';

    /**
     * @var string
     */
    protected $paginationTheme = 'tailwind';

    /**
     * @var string[]
     */
    protected $listeners = ['refresh' => '$refresh', 'deleteCareerLevel'];

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

    public function deleteCareerLevel($careerLevelId)
    {
        $careerLevel = CareerLevel::findOrFail($careerLevelId);
        $careerLevel->delete();
        $this->dispatch('delete');
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $careerLevels = $this->careerLevel();

        return view('livewire.career-levels', compact('careerLevels'));
    }

    public function careerLevel(): LengthAwarePaginator
    {
        $query = CareerLevel::query()->select('career_levels.*');

        $query->when(isset($this->searchByCareerLevel) && $this->searchByCareerLevel != '', function (Builder $q) {
            $q->where('level_name', 'like',
                '%'.strtolower($this->searchByCareerLevel).'%');
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
