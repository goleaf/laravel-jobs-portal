<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Closure;

class Filter
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $type = 'select';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string|null
     */
    protected $view = null;

    /**
     * @var Closure|null
     */
    protected $callback = null;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new filter instance.
     *
     * @param string $key
     * @param string|null $label
     * @return void
     */
    public function __construct(string $key, string $label = null)
    {
        $this->key = $key;
        $this->label = $label ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Create a new filter instance.
     *
     * @param string $key
     * @param string|null $label
     * @return static
     */
    public static function make(string $key, string $label = null): self
    {
        return new static($key, $label);
    }

    /**
     * Configure filter as a select.
     *
     * @return $this
     */
    public function select(): self
    {
        $this->type = 'select';
        return $this;
    }

    /**
     * Configure filter as a multi-select.
     *
     * @return $this
     */
    public function multiSelect(): self
    {
        $this->type = 'multiselect';
        return $this;
    }

    /**
     * Configure filter as a date.
     *
     * @return $this
     */
    public function date(): self
    {
        $this->type = 'date';
        return $this;
    }

    /**
     * Configure filter as a date range.
     *
     * @return $this
     */
    public function dateRange(): self
    {
        $this->type = 'daterange';
        return $this;
    }

    /**
     * Set custom view for filter.
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
     * Set options for the filter.
     *
     * @param array $options
     * @return $this
     */
    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Set options from an enum class.
     *
     * @param string $enumClass
     * @return $this
     */
    public function optionsFromEnum($enumClass): self
    {
        if (enum_exists($enumClass)) {
            $options = [];
            foreach ($enumClass::cases() as $case) {
                $options[$case->value] = $case->name;
            }
            $this->options = $options;
        }
        
        return $this;
    }

    /**
     * Set options from a query.
     *
     * @param Builder $query
     * @param string $valueField
     * @param string $labelField
     * @return $this
     */
    public function optionsFromQuery(Builder $query, string $valueField = 'id', string $labelField = 'name'): self
    {
        $this->options = $query->pluck($labelField, $valueField)->toArray();
        return $this;
    }

    /**
     * Set a callback for the filter.
     *
     * @param Closure $callback
     * @return $this
     */
    public function callback(Closure $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Set an attribute for the filter.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function attribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Set multiple attributes for the filter.
     *
     * @param array $attributes
     * @return $this
     */
    public function attributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Get filter label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get filter key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get filter type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get filter options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get filter view.
     *
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * Get filter callback.
     *
     * @return Closure|null
     */
    public function getCallback(): ?Closure
    {
        return $this->callback;
    }

    /**
     * Get filter attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Apply filter to a query.
     *
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function apply(Builder $query, $value)
    {
        if ($this->callback !== null) {
            return call_user_func($this->callback, $query, $value);
        }

        switch ($this->type) {
            case 'select':
                return $query->where($this->key, $value);
            
            case 'multiselect':
                return $query->whereIn($this->key, (array)$value);
            
            case 'date':
                return $query->whereDate($this->key, $value);
            
            case 'daterange':
                if (isset($value['from']) && !empty($value['from'])) {
<?php

namespace App\Livewire;

class Filter
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $type = 'select';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string|null
     */
    protected $view = null;

    /**
     * Create a new filter instance.
     *
     * @param string $label
     * @param string $key
     * @return void
     */
    public function __construct(string $label, string $key)
    {
        $this->label = $label;
        $this->key = $key;
    }

    /**
     * Create a new filter instance.
     *
     * @param string $label
     * @param string $key
     * @return static
     */
    public static function make(string $label, string $key): self
    {
        return new static($label, $key);
    }

    /**
     * Configure filter as a select.
     *
     * @param array $options
     * @return $this
     */
    public function select(array $options): self
    {
        $this->type = 'select';
        $this->options = $options;

        return $this;
    }

    /**
     * Configure filter as a multi-select.
     *
     * @param array $options
     * @return $this
     */
    public function multiSelect(array $options): self
    {
        $this->type = 'multiselect';
        $this->options = $options;

        return $this;
    }

    /**
     * Configure filter as a date.
     *
     * @return $this
     */
    public function date(): self
    {
        $this->type = 'date';

        return $this;
    }

    /**
     * Configure filter as a date range.
     *
     * @return $this
     */
    public function dateRange(): self
    {
        $this->type = 'daterange';

        return $this;
    }

    /**
     * Set custom view for filter.
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
     * Get filter label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get filter key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get filter type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get filter options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get filter view.
     *
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * Convert filter to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'key' => $this->key,
            'type' => $this->type,
            'options' => $this->options,
            'view' => $this->view,
        ];
    }
} 