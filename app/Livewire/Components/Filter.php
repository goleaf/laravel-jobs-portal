<?php

namespace App\Livewire\Components;

use Closure;

class Filter
{
    protected string $key;
    protected string $label;
    protected string $type = 'select';
    protected array $options = [];
    protected ?Closure $callback = null;

    /**
     * Create a new Filter instance.
     *
     * @param  string  $key  The filter key
     */
    public static function make(string $key): self
    {
        $instance = new static;
        $instance->setKey($key);
        $instance->setLabel(ucfirst(str_replace('_', ' ', $key)));

        return $instance;
    }

    /**
     * Set the label for the filter.
     */
    public function label(string $label): self
    {
        $this->setLabel($label);

        return $this;
    }

    /**
     * Set the filter as a select dropdown.
     *
     * @param  array  $options  The options for the select dropdown
     */
    public function select(array $options): self
    {
        $this->type = 'select';
        $this->options = $options;

        return $this;
    }

    /**
     * Set the filter as a multi-select dropdown.
     *
     * @param  array  $options  The options for the multi-select dropdown
     */
    public function multiSelect(array $options): self
    {
        $this->type = 'multi-select';
        $this->options = $options;

        return $this;
    }

    /**
     * Set the filter as a date range picker.
     */
    public function dateRange(): self
    {
        $this->type = 'date-range';

        return $this;
    }

    /**
     * Set the filter as a text input.
     */
    public function text(): self
    {
        $this->type = 'text';

        return $this;
    }

    /**
     * Set a callback function for the filter.
     */
    public function callback(callable $callback): self
    {
        $this->callback = $callback instanceof Closure ? $callback : Closure::fromCallable($callback);

        return $this;
    }

    /**
     * Get the key for the filter.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the label for the filter.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the type of the filter.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the options for the filter.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get the callback for the filter.
     */
    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    /**
     * Set the key for the filter.
     */
    protected function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * Set the label for the filter.
     */
    protected function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
