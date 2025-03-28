<?php

namespace App\Livewire;

use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;

class SimpleCountryTable extends Component
{
    use WithPagination;

    // Table properties
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    public $searchTerm = '';
    public $perPage = 10;
    
    // UI customization
    public $tableClass = 'table table-striped';
    
    // Listeners
    protected $listeners = ['refresh' => '$refresh', 'deleteCountry'];

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteCountry($id)
    {
        $country = Country::findOrFail($id);
        
        // Check if country can be deleted
        if ($country->states()->count() > 0 || $country->users()->count() > 0) {
            session()->flash('error', __('messages.flash.country_cant_deleted'));
            return;
        }
        
        $country->delete();
        $this->dispatch('deleted');
        session()->flash('success', __('messages.flash.country_deleted'));
    }
    
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $countries = Country::query()
            ->when($this->searchTerm, function($query) {
                return $query->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('short_code', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('phone_code', 'like', '%'.$this->searchTerm.'%');
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.simple-country-table', [
            'countries' => $countries,
        ]);
    }
} 