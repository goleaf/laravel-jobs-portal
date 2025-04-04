<?php

namespace App\Livewire;

use App\Models\PostCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class BlogCategories
 */
class BlogCategories extends Component
{
    use WithPagination;

    /**
     * @var string
     */
    public $searchByPostCategory = '';

    /**
     * @var string
     */
    protected $paginationTheme = 'tailwind';

    /**
     * @var string[]
     */
    protected $listeners = ['refresh' => '$refresh', 'deletePostCategory'];

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

    public function deletePostCategory($postCategoryId)
    {
        $postCategory = PostCategory::findOrFail($postCategoryId);
        $postCategory->delete();
        $this->dispatch('delete');
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $postCategories = $this->postCategory();

        return view('livewire.blog-categories', compact('postCategories'));
    }

    public function postCategory(): LengthAwarePaginator
    {
        $query = PostCategory::query()->select('post_categories.*');

        $query->when(isset($this->searchByPostCategory) && $this->searchByPostCategory != '', function (Builder $q) {
            $q->where('name', 'like',
                '%'.strtolower($this->searchByPostCategory).'%');
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
