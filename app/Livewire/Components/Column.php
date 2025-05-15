<?php

namespace App\Livewire\Components;

use Closure;

class Column
{
    protected string $field;
    protected string $title;
    protected bool $sortable = false;
    protected bool $searchable = false;
    protected bool $hidden = false;
    protected ?string $viewComponent = null;
    protected ?Closure $formatCallback = null;
    protected ?string $class = null;

    /**
     * Create a new Column instance.
     *
     * @param  string  $field  The database field or key in the model
     */
    public static function make(string $field): self
    {
        $instance = new static;
        $instance->field = $field;
        $instance->title = ucfirst(str_replace('_', ' ', $field));

        return $instance;
    }

    /**
     * Set the title of the column.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
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
     * Hide the column from view.
     */
    public function hidden(bool $hidden = true): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Set a view component to render for this column.
     */
    public function view(string $viewComponent): self
    {
        $this->viewComponent = $viewComponent;

        return $this;
    }

    /**
     * Set a format callback for this column.
     */
    public function format(callable $callback): self
    {
        $this->formatCallback = $callback instanceof Closure ? $callback : Closure::fromCallable($callback);

        return $this;
    }

    /**
     * Set the CSS class for this column.
     */
    public function class(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get the field of this column.
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get the title of this column.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Check if this column is sortable.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Check if this column is searchable.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Check if this column is hidden.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Get the view component of this column.
     */
    public function getViewComponent(): ?string
    {
        return $this->viewComponent;
    }

    /**
     * Get the format callback of this column.
     */
    public function getFormatCallback(): ?callable
    {
        return $this->formatCallback;
    }

    /**
     * Get the CSS class of this column.
     */
    public function getClass(): ?string
    {
        return $this->class;
    }
}
