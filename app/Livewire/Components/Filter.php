<?php

namespace App\Livewire\Components;

class Filter
{
    public string $name;
    public string $key;
    public string $type;
    public array $options = [];
    public $default = null;
    
    /**
     * Create a new filter instance.
     */
    public function __construct(string $name, string $key, string $type = 'select')
    {
        $this->name = $name;
        $this->key = $key;
        $this->type = $type;
    }
    
    /**
     * Create a new select filter.
     */
    public static function select(string $name, string $key): self
    {
        return new static($name, $key, 'select');
    }
    
    /**
     * Create a new multiselect filter.
     */
    public static function multiSelect(string $name, string $key): self
    {
        return new static($name, $key, 'multiselect');
    }
    
    /**
     * Create a new date range filter.
     */
    public static function dateRange(string $name, string $key): self
    {
        return new static($name, $key, 'daterange');
    }
    
    /**
     * Create a new number range filter.
     */
    public static function numberRange(string $name, string $key): self
    {
        return new static($name, $key, 'numberrange');
    }
    
    /**
     * Set the options for the filter.
     */
    public function options(array $options): self
    {
        $this->options = $options;
        
        return $this;
    }
    
    /**
     * Set the default value for the filter.
     */
    public function default($default): self
    {
        $this->default = $default;
        
        return $this;
    }
    
    /**
     * Get all available filter types.
     */
    public static function getTypes(): array
    {
        return [
            'select',
            'multiselect',
            'daterange',
            'numberrange',
        ];
    }
    
    /**
     * Get the name of the filter.
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get the key of the filter.
     */
    public function getKey(): string
    {
        return $this->key;
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
     * Get the default value for the filter.
     */
    public function getDefault()
    {
        return $this->default;
    }
} 