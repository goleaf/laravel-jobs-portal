<?php

namespace App\Livewire;

class Column
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $field;

    /**
     * @var bool
     */
    protected $sortable = false;

    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * @var string|null
     */
    protected $view = null;

    /**
     * Create a new column instance.
     *
     * @param string $title
     * @param string|null $field
     * @return void
     */
    public function __construct(string $title, ?string $field = null)
    {
        $this->title = $title;
        $this->field = $field;
    }

    /**
     * Create a new column instance.
     *
     * @param string $title
     * @param string|null $field
     * @return static
     */
    public static function make(string $title, ?string $field = null): self
    {
        return new static($title, $field);
    }

    /**
     * Make column sortable.
     *
     * @return $this
     */
    public function sortable(): self
    {
        $this->sortable = true;

        return $this;
    }

    /**
     * Make column searchable.
     *
     * @return $this
     */
    public function searchable(): self
    {
        $this->searchable = true;

        return $this;
    }

    /**
     * Set view to render the column.
     *
     * @param string $view
     * @return $this
     */
    public function view(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get column title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get column field.
     *
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * Check if column is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Check if column is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Get column view.
     *
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }
} 