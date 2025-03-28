<?php

return [
    /**
     * Options: tailwind | bootstrap-5
     */
    'theme' => 'tailwind',

    /**
     * Pagination Options
     */
    'pagination' => [
        'default_per_page' => 10,
        'per_page_options' => [10, 25, 50, 100],
    ],

    /**
     * Table Layout
     */
    'table' => [
        'default_classes' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700',
        'thead_classes' => 'bg-gray-50 dark:bg-gray-700',
        'th_classes' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider',
        'tbody_classes' => 'bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700',
        'tr_classes' => 'hover:bg-gray-50 dark:hover:bg-gray-600',
        'td_classes' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300',
    ],

    /**
     * Search Options
     */
    'search' => [
        'debounce' => 300, // ms
    ],

    /**
     * Filter Default Configuration Options
     */
    'filters' => [
        'date_format' => 'Y-m-d',
        'datetime_format' => 'Y-m-d H:i:s',
        'date_display_format' => 'd M Y',
    ],

    /**
     * UI Options
     */
    'ui' => [
        'show_search' => true,
        'show_per_page_selector' => true,
    ],
];
