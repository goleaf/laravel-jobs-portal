<?php

namespace App\Livewire\Components;

class Filter
{
    protected string $key;
    protected string $label;
    protected string $type = 'select';
    protected array $options = [];
    protected $callback;
    
    public static function make(string $key, string $label): self
    {
        return (new static())
            ->setKey($key)
            ->setLabel($label);
    }
    
    public function select(array $options): self
    {
        $this->type = 'select';
        $this->options = $options;
        return $this;
    }
    
    public function multiSelect(array $options): self
    {
        $this->type = 'multi-select';
        $this->options = $options;
        return $this;
    }
    
    public function dateRange(): self
    {
        $this->type = 'date-range';
        return $this;
    }
    
    public function text(): self
    {
        $this->type = 'text';
        return $this;
    }
    
    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }
    
    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getOptions(): array
    {
        return $this->options;
    }
    
    public function getCallback()
    {
        return $this->callback;
    }
    
    protected function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }
    
    protected function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }
} 