<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Classes
    |--------------------------------------------------------------------------
    |
    | Default classes that will be applied to elements dependent on their type.
    | These can be overridden for individual elements or globally for all elements
    | of a specific type.
    |
    */
    'default_classes' => [
        'group' => 'mb-4',
        'group_horizontal' => 'sm:flex sm:items-center',
        'group_inline' => 'inline-block',
        'label' => 'block text-sm font-medium text-gray-700 mb-1',
        'label_horizontal' => 'sm:w-1/3 sm:text-right sm:mr-2',
        'label_inline' => 'inline-block mr-2',
        'input' => 'mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md',
        'textarea' => 'mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md',
        'select' => 'mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
        'checkbox' => 'focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded',
        'checkbox_label' => 'ml-2 block text-sm text-gray-700',
        'radio' => 'focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300',
        'radio_label' => 'ml-2 block text-sm text-gray-700',
        'button' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
        'help_text' => 'mt-2 text-sm text-gray-500',
        'error_text' => 'mt-2 text-sm text-red-600',
        'summary' => 'text-sm text-red-600 mb-4',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Options related to validation of the input.
    |
    */
    'validation' => [
        /*
        |--------------------------------------------------------------------------
        | Client-Side Validation
        |--------------------------------------------------------------------------
        |
        | These options configure whether Aire will attempt to add client-side
        | validation to your forms. The options here adjust the behavior
        | of all inline validation. You may turn inline validation off
        | entirely by specifying false. Or you may choose to enable
        | only a subset of client-side rules.
        |
        */
        'inline' => [
            'enabled' => true,
            'rules' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify which theme to use for your forms.
    |
    */
    'theme' => 'tailwind',

    /*
    |--------------------------------------------------------------------------
    | Error Message Bag
    |--------------------------------------------------------------------------
    |
    | This is the error message bag that Aire will check for errors. By default
    | this is the default message bag.
    |
    */
    'error_bag' => null,
]; 