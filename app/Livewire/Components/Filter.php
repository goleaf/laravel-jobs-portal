<?php

namespace App\Livewire\Components;

class Filter
{
    /**
     * Filter key
     *
     * @var string
     */
    protected $key;

    /**
     * Filter title
     *
     * @var string
     */
    protected $title;

    /**
     * Filter type (select, multi-select, date-range)
     *
     * @var string
     */
    protected $type = 'select';

    /**
     * Filter options (for select and multi-select types)
     *
     * @var array
     */
    protected $options = [];

    /**
     * Custom view to render this filter (optional)
     * @var string|null
     */
    protected $customView = null;

    /**
     * Create a new filter instance
     *
     * @param string $key
     * @param string $title
     * @param string $type
     * @return self
     */
    public static function make(string $key, string $title, string $type = 'select'): self
    {
        $filter = new static();
        $filter->key = $key;
        $filter->title = $title;
        $filter->type = $type;

        return $filter;
    }

    /**
     * Set filter type
     *
     * @param string $type
     * @return self
     */
    public function type(string $type): self
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Set filter options
     *
     * @param array $options
     * @return self
     */
    public function options(array $options): self
    {
        $this->options = $options;
        
        return $this;
    }

    /**
     * Set the custom view for the filter
     *
     * @param string $view
     * @return $this
     */
    public function view(string $view): self
    {
        $this->customView = $view;
        return $this;
    }

    /**
     * Get the key for the filter
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the title for the filter
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the type for the filter
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the options for the filter
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Check if the filter has a custom view
     *
     * @return bool
     */
    public function hasCustomView(): bool
    {
        return $this->customView !== null;
    }

    /**
     * Get the custom view for the filter
     *
     * @return string|null
     */
    public function getCustomView(): ?string
    {
        return $this->customView;
    }

    /**
     * Convert filter to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'title' => $this->title,
            'type' => $this->type,
            'options' => $this->options,
            'view' => $this->hasCustomView() 
                ? $this->getCustomView() 
                : 'livewire.components.filters.select',
        ];
    }
} 