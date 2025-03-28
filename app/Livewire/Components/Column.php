<?php

namespace App\Livewire\Components;

class Column
{
    protected $field;
    protected $title;
    protected $sortable = false;
    protected $searchable = false;
    protected $hidden = false;
    protected $viewComponent = null;
    protected $formatCallback = null;
    protected $class = null;

    /**
     * Create a new Column instance.
     *
     * @param string $field The database field or key in the model
     * @return self
     */
    public static function make(string $field): self
    {
        $instance = new static();
        $instance->field = $field;
        $instance->title = ucfirst(str_replace('_', ' ', $field));
        
        return $instance;
    }

    /**
     * Set the title of the column.
     *
     * @param string $title
     * @return self
     */
    public function title(string $title): self
    {
        $this->title = $title;
        
        return $this;
    }

    /**
     * Make the column sortable.
     *
     * @param bool $sortable
     * @return self
     */
    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        
        return $this;
    }

    /**
     * Make the column searchable.
     *
     * @param bool $searchable
     * @return self
     */
    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;
        
        return $this;
    }

    /**
     * Hide the column from view.
     *
     * @param bool $hidden
     * @return self
     */
    public function hidden(bool $hidden = true): self
    {
        $this->hidden = $hidden;
        
        return $this;
    }

    /**
     * Set a view component to render for this column.
     *
     * @param string $viewComponent
     * @return self
     */
    public function view(string $viewComponent): self
    {
        $this->viewComponent = $viewComponent;
        
        return $this;
    }

    /**
     * Set a format callback for this column.
     *
     * @param callable $callback
     * @return self
     */
    public function format(callable $callback): self
    {
        $this->formatCallback = $callback;
        
        return $this;
    }

    /**
     * Set the CSS class for this column.
     *
     * @param string $class
     * @return self
     */
    public function class(string $class): self
    {
        $this->class = $class;
        
        return $this;
    }

    /**
     * Get the field of this column.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get the title of this column.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Check if this column is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Check if this column is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Check if this column is hidden.
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Get the view component of this column.
     *
     * @return string|null
     */
    public function getViewComponent(): ?string
    {
        return $this->viewComponent;
    }

    /**
     * Get the format callback of this column.
     *
     * @return callable|null
     */
    public function getFormatCallback(): ?callable
    {
        return $this->formatCallback;
    }

    /**
     * Get the CSS class of this column.
     *
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }
} 