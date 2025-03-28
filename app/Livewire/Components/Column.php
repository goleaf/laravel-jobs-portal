<?php

namespace App\Livewire\Components;

class Column
{
    public string $title;
    public string $field;
    public bool $sortable = false;
    public bool $searchable = false;
    public ?string $viewComponent = null;
    public ?string $format = null;
    public bool $hidden = false;
    
    /**
     * Create a new column.
     */
    public function __construct(string $title, string $field)
    {
        $this->title = $title;
        $this->field = $field;
    }
    
    /**
     * Create a new column instance.
     */
    public static function make(string $title, string $field): self
    {
        return new static($title, $field);
    }
    
    /**
     * Make the column sortable.
     */
    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        
        return $this;
    }
    
    /**
     * Make the column searchable.
     */
    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;
        
        return $this;
    }
    
    /**
     * Set the view component for the column.
     */
    public function view(string $viewComponent): self
    {
        $this->viewComponent = $viewComponent;
        
        return $this;
    }
    
    /**
     * Format the column using a callback.
     */
    public function format(string $format): self
    {
        $this->format = $format;
        
        return $this;
    }
    
    /**
     * Hide the column.
     */
    public function hideIf(bool $condition): self
    {
        $this->hidden = $condition;
        
        return $this;
    }
    
    /**
     * Check if the column is hidden.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }
    
    /**
     * Check if the column is sortable.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }
    
    /**
     * Check if the column is searchable.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
    
    /**
     * Get the field name.
     */
    public function getField(): string
    {
        return $this->field;
    }
    
    /**
     * Get the title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }
    
    /**
     * Get the view component.
     */
    public function getViewComponent(): ?string
    {
        return $this->viewComponent;
    }
} 