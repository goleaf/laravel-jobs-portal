<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\HtmlString open(array $options = [])
 * @method static \Illuminate\Support\HtmlString close()
 * @method static \Illuminate\Support\HtmlString text(string $name, string $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString password(string $name, array $options = [])
 * @method static \Illuminate\Support\HtmlString email(string $name, string $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString file(string $name, array $options = [])
 * @method static \Illuminate\Support\HtmlString textarea(string $name, string $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString select(string $name, array $list = [], string|array $selected = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString checkbox(string $name, mixed $value = 1, bool $checked = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString radio(string $name, mixed $value = null, bool $checked = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString submit(string $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString button(string $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString label(string $for, string $text, array $options = [])
 */
class Form extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'form';
    }
}
