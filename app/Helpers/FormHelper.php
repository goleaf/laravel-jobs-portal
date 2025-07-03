<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;

class FormHelper
{
    /**
     * Open a new form.
     *
     * @return HtmlString
     */
    public static function open(array $options = [])
    {
        $method = $options['method'] ?? 'post';
        $action = $options['url'] ?? '';
        $files = isset($options['files']) && $options['files'] ? 'enctype="multipart/form-data"' : '';
        $attrs = static::buildHtmlAttributes(static::arrayExcept($options, ['method', 'url', 'files']));

        $html = sprintf(
            '<form method="%s" action="%s" %s %s>',
            in_array(strtolower($method), ['get', 'post']) ? strtolower($method) : 'post',
            $action,
            $files,
            $attrs
        );

        if (strtolower($method) !== 'get' && strtolower($method) !== 'post') {
            $html .= sprintf('<input type="hidden" name="_method" value="%s">', $method);
        }

        if (strtolower($method) !== 'get') {
            $html .= csrf_field();
        }

        return new HtmlString($html);
    }

    /**
     * Close the form.
     *
     * @return HtmlString
     */
    public static function close()
    {
        return new HtmlString('</form>');
    }

    /**
     * Create a text input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @return HtmlString
     */
    public static function text($name, $value = null, array $options = [])
    {
        return static::input('text', $name, $value, $options);
    }

    /**
     * Create a generic input field.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  string  $value
     * @return HtmlString
     */
    public static function input($type, $name, $value = null, array $options = [])
    {
        $options['type'] = $type;
        $options['name'] = $name;
        $options['value'] = $value ?? old($name);
        $options['id'] = $options['id'] ?? $name;

        return new HtmlString(sprintf('<input %s>', static::buildHtmlAttributes($options)));
    }

    /**
     * Create a password input field.
     *
     * @param  string  $name
     * @return HtmlString
     */
    public static function password($name, array $options = [])
    {
        return static::input('password', $name, null, $options);
    }

    /**
     * Create an email input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @return HtmlString
     */
    public static function email($name, $value = null, array $options = [])
    {
        return static::input('email', $name, $value, $options);
    }

    /**
     * Create a file input field.
     *
     * @param  string  $name
     * @return HtmlString
     */
    public static function file($name, array $options = [])
    {
        return static::input('file', $name, null, $options);
    }

    /**
     * Create a textarea input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @return HtmlString
     */
    public static function textarea($name, $value = null, array $options = [])
    {
        $options['name'] = $name;
        $options['id'] = $options['id'] ?? $name;

        $value = $value ?? old($name) ?? '';

        return new HtmlString(sprintf(
            '<textarea %s>%s</textarea>',
            static::buildHtmlAttributes($options),
            e($value)
        ));
    }

    /**
     * Create a select box field.
     *
     * @param  string  $name
     * @param  array  $list
     * @param  array|string  $selected
     * @return HtmlString
     */
    public static function select($name, $list = [], $selected = null, array $options = [])
    {
        $options['name'] = $name;
        $options['id'] = $options['id'] ?? $name;

        $html = [];
        $html[] = sprintf('<select %s>', static::buildHtmlAttributes($options));

        if (isset($options['placeholder'])) {
            $html[] = sprintf('<option value="">%s</option>', e($options['placeholder']));
        }

        foreach ($list as $value => $display) {
            $isSelected = $selected !== null
                          && ((is_array($selected) && in_array($value, $selected))
                           || $selected == $value);

            $html[] = sprintf(
                '<option value="%s"%s>%s</option>',
                e($value),
                $isSelected ? ' selected' : '',
                e($display)
            );
        }

        $html[] = '</select>';

        return new HtmlString(implode('', $html));
    }

    /**
     * Create a checkbox input field.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool  $checked
     * @return HtmlString
     */
    public static function checkbox($name, $value = 1, $checked = null, array $options = [])
    {
        return static::checkable('checkbox', $name, $value, $checked, $options);
    }

    /**
     * Create a radio input field.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool  $checked
     * @return HtmlString
     */
    public static function radio($name, $value = null, $checked = null, array $options = [])
    {
        if ($checked === null) {
            $checked = old($name) === $value;
        }

        return static::checkable('radio', $name, $value, $checked, $options);
    }

    /**
     * Create a checkable input field.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool  $checked
     * @return HtmlString
     */
    public static function checkable($type, $name, $value, $checked, array $options)
    {
        if ($checked) {
            $options['checked'] = 'checked';
        }

        return static::input($type, $name, $value, $options);
    }

    /**
     * Create a submit button element.
     *
     * @param  string  $value
     * @return HtmlString
     */
    public static function submit($value = null, array $options = [])
    {
        return static::input('submit', null, $value, $options);
    }

    /**
     * Create a button element.
     *
     * @param  string  $value
     * @return HtmlString
     */
    public static function button($value = null, array $options = [])
    {
        $options['type'] = $options['type'] ?? 'button';

        return new HtmlString(sprintf(
            '<button %s>%s</button>',
            static::buildHtmlAttributes($options),
            $value
        ));
    }

    /**
     * Create a label element.
     *
     * @param  string  $for
     * @param  string  $text
     * @return HtmlString
     */
    public static function label($for, $text, array $options = [])
    {
        $options['for'] = $for;

        return new HtmlString(sprintf(
            '<label %s>%s</label>',
            static::buildHtmlAttributes($options),
            $text
        ));
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @return string
     */
    protected static function buildHtmlAttributes(array $attributes)
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $html[] = e($key);
            } elseif ($value !== false && $value !== null) {
                $html[] = sprintf('%s="%s"', e($key), e($value));
            }
        }

        return implode(' ', $html);
    }

    /**
     * Helper function to emulate Laravel's array_except function.
     *
     * @return array
     */
    protected static function arrayExcept(array $array, array $keys)
    {
        foreach ($keys as $key) {
            unset($array[$key]);
        }

        return $array;
    }
}
