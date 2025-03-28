<?php

namespace App\Livewire\Components;

class Column
{
    protected string $field;
    protected string $title;
    protected bool $sortable = false;
    protected bool $searchable = false;
    protected ?string $viewComponent = null;
    protected ?string $class = null;
    protected bool $hidden = false;
    protected $formatCallback = null;
    
    public static function make(string $title, ?string $field = null): self
    {
        return (new static())
            ->setTitle($title)
            ->setField($field ?? \Illuminate\Support\Str::snake($title));
    }
    
    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        return $this;
    }
    
    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;
        return $this;
    }
    
    public function view(string $viewComponent): self
    {
        $this->viewComponent = $viewComponent;
        return $this;
    }
    
    public function class(string $class): self
    {
        $this->class = $class;
        return $this;
    }
    
    public function hidden(bool $hidden = true): self
    {
        $this->hidden = $hidden;
        return $this;
    }
    
    public function format(callable $callback): self
    {
        $this->formatCallback = $callback;
        return $this;
    }
    
    public function getField(): string
    {
        return $this->field;
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function isSortable(): bool
    {
        return $this->sortable;
    }
    
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
    
    public function isHidden(): bool
    {
        return $this->hidden;
    }
    
    public function getClass(): ?string
    {
        return $this->class;
    }
    
    public function getViewComponent(): ?string
    {
        return $this->viewComponent;
    }
    
    public function getFormatCallback()
    {
        return $this->formatCallback;
    }
    
    protected function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    
    protected function setField(string $field): self
    {
        $this->field = $field;
        return $this;
    }
} 