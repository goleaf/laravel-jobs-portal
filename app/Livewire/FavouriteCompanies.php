<?php

namespace App\Livewire;

use App\Models\FavouriteCompany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class FavouriteCompanies extends Component
{
    use WithPagination;

    public $searchByFavouriteCompanies = '';

    protected $paginationTheme = 'tailwind';

    protected $listeners = 'removeFavouriteCompany';

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

//    /**
//     * @param $id
//     */
//    public function removeFavouriteCompany($id)
//    {
//        $favouriteCompany = FavouriteCompany::findOrFail($id);
//        $favouriteCompany->delete($id);
//        $this->dispatchBrowserEvent('deleted');
//    }

    public function updatingsearchByFavouriteCompanies()
    {
        $this->resetPage();
    }

    /**
     * @return Factory|View
     */
    public function render()
    {
        $favouriteCompanies = $this->searchFavouriteCompanies();

        return view('livewire.favourite-companies', compact('favouriteCompanies'));
    }

    public function searchFavouriteCompanies(): LengthAwarePaginator
    {
        $query = FavouriteCompany::with(['company.user', 'company.industry'])->where('user_id',
            getLoggedInUserId())->orderByDesc('created_at');

        $query->when($this->searchByFavouriteCompanies != '', function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereHas('company.user', function (Builder $query) {
                    $query->where('first_name', 'like', '%'.strtolower($this->searchByFavouriteCompanies).'%');
                    $query->orWhere('email', 'like', '%'.strtolower($this->searchByFavouriteCompanies).'%');
                    $query->orWhere('phone', 'like', '%'.strtolower($this->searchByFavouriteCompanies).'%');
                });

                $query->orwhereHas('company.industry', function (Builder $query) {
                    $query->where('name', 'like', '%'.strtolower($this->searchByFavouriteCompanies).'%');
                });
            });
        });

        return $query->paginate(6);
    }
}
