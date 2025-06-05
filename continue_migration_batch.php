<?php

/**
 * Enhanced Aggressive TailwindCSS Migration Script
 * Using Context7 patterns for comprehensive Bootstrap replacement
 */

echo "🚀 Starting Enhanced Aggressive TailwindCSS Migration...\n\n";

// Enhanced Bootstrap to TailwindCSS mappings
$bootstrap_to_tailwind = [
    // Layout & Grid
    'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
    'container-fluid' => 'w-full px-4',
    'row' => 'flex flex-wrap -mx-4',
    'col' => 'flex-1 px-4',
    'col-1' => 'w-1/12 px-4',
    'col-2' => 'w-2/12 px-4',
    'col-3' => 'w-3/12 px-4',
    'col-4' => 'w-4/12 px-4',
    'col-5' => 'w-5/12 px-4',
    'col-6' => 'w-6/12 px-4',
    'col-7' => 'w-7/12 px-4',
    'col-8' => 'w-8/12 px-4',
    'col-9' => 'w-9/12 px-4',
    'col-10' => 'w-10/12 px-4',
    'col-11' => 'w-11/12 px-4',
    'col-12' => 'w-full px-4',
    'col-sm-1' => 'sm:w-1/12 px-4',
    'col-sm-2' => 'sm:w-2/12 px-4',
    'col-sm-3' => 'sm:w-3/12 px-4',
    'col-sm-4' => 'sm:w-4/12 px-4',
    'col-sm-5' => 'sm:w-5/12 px-4',
    'col-sm-6' => 'sm:w-6/12 px-4',
    'col-sm-7' => 'sm:w-7/12 px-4',
    'col-sm-8' => 'sm:w-8/12 px-4',
    'col-sm-9' => 'sm:w-9/12 px-4',
    'col-sm-10' => 'sm:w-10/12 px-4',
    'col-sm-11' => 'sm:w-11/12 px-4',
    'col-sm-12' => 'sm:w-full px-4',
    'col-md-1' => 'md:w-1/12 px-4',
    'col-md-2' => 'md:w-2/12 px-4',
    'col-md-3' => 'md:w-3/12 px-4',
    'col-md-4' => 'md:w-4/12 px-4',
    'col-md-5' => 'md:w-5/12 px-4',
    'col-md-6' => 'md:w-6/12 px-4',
    'col-md-7' => 'md:w-7/12 px-4',
    'col-md-8' => 'md:w-8/12 px-4',
    'col-md-9' => 'md:w-9/12 px-4',
    'col-md-10' => 'md:w-10/12 px-4',
    'col-md-11' => 'md:w-11/12 px-4',
    'col-md-12' => 'md:w-full px-4',
    'col-lg-1' => 'lg:w-1/12 px-4',
    'col-lg-2' => 'lg:w-2/12 px-4',
    'col-lg-3' => 'lg:w-3/12 px-4',
    'col-lg-4' => 'lg:w-4/12 px-4',
    'col-lg-5' => 'lg:w-5/12 px-4',
    'col-lg-6' => 'lg:w-6/12 px-4',
    'col-lg-7' => 'lg:w-7/12 px-4',
    'col-lg-8' => 'lg:w-8/12 px-4',
    'col-lg-9' => 'lg:w-9/12 px-4',
    'col-lg-10' => 'lg:w-10/12 px-4',
    'col-lg-11' => 'lg:w-11/12 px-4',
    'col-lg-12' => 'lg:w-full px-4',
    'col-xl-1' => 'xl:w-1/12 px-4',
    'col-xl-2' => 'xl:w-2/12 px-4',
    'col-xl-3' => 'xl:w-3/12 px-4',
    'col-xl-4' => 'xl:w-4/12 px-4',
    'col-xl-5' => 'xl:w-5/12 px-4',
    'col-xl-6' => 'xl:w-6/12 px-4',
    'col-xl-7' => 'xl:w-7/12 px-4',
    'col-xl-8' => 'xl:w-8/12 px-4',
    'col-xl-9' => 'xl:w-9/12 px-4',
    'col-xl-10' => 'xl:w-10/12 px-4',
    'col-xl-11' => 'xl:w-11/12 px-4',
    'col-xl-12' => 'xl:w-full px-4',

    // Buttons
    'btn' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200',
    'btn-primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
    'btn-secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
    'btn-success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'btn-danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'btn-warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
    'btn-info' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    'btn-light' => 'bg-gray-100 text-gray-900 hover:bg-gray-200 focus:ring-gray-500',
    'btn-dark' => 'bg-gray-900 text-white hover:bg-gray-800 focus:ring-gray-500',
    'btn-outline-primary' => 'border-indigo-600 text-indigo-600 hover:bg-indigo-50',
    'btn-outline-secondary' => 'border-gray-600 text-gray-600 hover:bg-gray-50',
    'btn-outline-success' => 'border-green-600 text-green-600 hover:bg-green-50',
    'btn-outline-danger' => 'border-red-600 text-red-600 hover:bg-red-50',
    'btn-outline-warning' => 'border-yellow-600 text-yellow-600 hover:bg-yellow-50',
    'btn-outline-info' => 'border-blue-600 text-blue-600 hover:bg-blue-50',
    'btn-sm' => 'px-3 py-1.5 text-xs',
    'btn-lg' => 'px-6 py-3 text-lg',
    'btn-block' => 'w-full justify-center',

    // Forms
    'form-control' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
    'form-control-sm' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1 px-2',
    'form-control-lg' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg py-3 px-4',
    'form-select' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
    'form-check-input' => 'h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded',
    'form-label' => 'block text-sm font-medium text-gray-700 mb-1',
    'form-text' => 'text-sm text-gray-500',
    'form-group' => 'mb-4',
    'input-group' => 'flex',
    'input-group-text' => 'inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm',

    // Cards
    'card' => 'bg-white overflow-hidden shadow rounded-lg',
    'card-header' => 'px-4 py-5 border-b border-gray-200 sm:px-6',
    'card-body' => 'px-4 py-5 sm:p-6',
    'card-footer' => 'px-4 py-4 border-t border-gray-200 sm:px-6',
    'card-title' => 'text-lg font-medium text-gray-900',
    'card-text' => 'text-gray-600',

    // Tables
    'table' => 'min-w-full divide-y divide-gray-200',
    'table-striped' => 'bg-white divide-y divide-gray-200',
    'table-bordered' => 'border border-gray-200',
    'table-hover' => 'hover:bg-gray-50',
    'table-sm' => 'text-sm',
    'thead-dark' => 'bg-gray-50',
    'thead-light' => 'bg-gray-100',

    // Alerts
    'alert' => 'rounded-md p-4',
    'alert-primary' => 'bg-blue-50 border border-blue-200 text-blue-700',
    'alert-secondary' => 'bg-gray-50 border border-gray-200 text-gray-700',
    'alert-success' => 'bg-green-50 border border-green-200 text-green-700',
    'alert-danger' => 'bg-red-50 border border-red-200 text-red-700',
    'alert-warning' => 'bg-yellow-50 border border-yellow-200 text-yellow-700',
    'alert-info' => 'bg-blue-50 border border-blue-200 text-blue-700',
    'alert-light' => 'bg-gray-50 border border-gray-200 text-gray-700',
    'alert-dark' => 'bg-gray-800 border border-gray-700 text-gray-100',

    // Badges
    'badge' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
    'badge-primary' => 'bg-indigo-100 text-indigo-800',
    'badge-secondary' => 'bg-gray-100 text-gray-800',
    'badge-success' => 'bg-green-100 text-green-800',
    'badge-danger' => 'bg-red-100 text-red-800',
    'badge-warning' => 'bg-yellow-100 text-yellow-800',
    'badge-info' => 'bg-blue-100 text-blue-800',
    'badge-light' => 'bg-gray-100 text-gray-800',
    'badge-dark' => 'bg-gray-800 text-gray-100',

    // Navigation
    'navbar' => 'bg-white shadow',
    'navbar-brand' => 'flex items-center px-4 text-lg font-semibold',
    'navbar-nav' => 'flex space-x-4',
    'nav-link' => 'text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium',
    'nav-item' => '',
    'breadcrumb' => 'flex space-x-2 text-sm',
    'breadcrumb-item' => 'text-gray-500',

    // Utilities
    'd-none' => 'hidden',
    'd-block' => 'block',
    'd-inline' => 'inline',
    'd-inline-block' => 'inline-block',
    'd-flex' => 'flex',
    'd-sm-none' => 'sm:hidden',
    'd-sm-block' => 'sm:block',
    'd-sm-flex' => 'sm:flex',
    'd-md-none' => 'md:hidden',
    'd-md-block' => 'md:block',
    'd-md-flex' => 'md:flex',
    'd-lg-none' => 'lg:hidden',
    'd-lg-block' => 'lg:block',
    'd-lg-flex' => 'lg:flex',
    'd-xl-none' => 'xl:hidden',
    'd-xl-block' => 'xl:block',
    'd-xl-flex' => 'xl:flex',
    'text-center' => 'text-center',
    'text-left' => 'text-left',
    'text-right' => 'text-right',
    'text-justify' => 'text-justify',
    'text-nowrap' => 'whitespace-nowrap',
    'text-wrap' => 'whitespace-normal',
    'text-truncate' => 'truncate',
    'text-lowercase' => 'lowercase',
    'text-uppercase' => 'uppercase',
    'text-capitalize' => 'capitalize',
    'font-weight-normal' => 'font-normal',
    'font-weight-bold' => 'font-bold',
    'font-italic' => 'italic',
    'text-muted' => 'text-gray-500',
    'text-primary' => 'text-indigo-600',
    'text-secondary' => 'text-gray-600',
    'text-success' => 'text-green-600',
    'text-danger' => 'text-red-600',
    'text-warning' => 'text-yellow-600',
    'text-info' => 'text-blue-600',
    'text-light' => 'text-gray-100',
    'text-dark' => 'text-gray-900',
    'bg-primary' => 'bg-indigo-600',
    'bg-secondary' => 'bg-gray-600',
    'bg-success' => 'bg-green-600',
    'bg-danger' => 'bg-red-600',
    'bg-warning' => 'bg-yellow-600',
    'bg-info' => 'bg-blue-600',
    'bg-light' => 'bg-gray-100',
    'bg-dark' => 'bg-gray-900',
    'bg-white' => 'bg-white',
    'bg-transparent' => 'bg-transparent',

    // Spacing
    'm-0' => 'm-0',
    'm-1' => 'm-1',
    'm-2' => 'm-2',
    'm-3' => 'm-3',
    'm-4' => 'm-4',
    'm-5' => 'm-5',
    'mt-0' => 'mt-0',
    'mt-1' => 'mt-1',
    'mt-2' => 'mt-2',
    'mt-3' => 'mt-3',
    'mt-4' => 'mt-4',
    'mt-5' => 'mt-5',
    'mb-0' => 'mb-0',
    'mb-1' => 'mb-1',
    'mb-2' => 'mb-2',
    'mb-3' => 'mb-3',
    'mb-4' => 'mb-4',
    'mb-5' => 'mb-5',
    'ml-0' => 'ml-0',
    'ml-1' => 'ml-1',
    'ml-2' => 'ml-2',
    'ml-3' => 'ml-3',
    'ml-4' => 'ml-4',
    'ml-5' => 'ml-5',
    'mr-0' => 'mr-0',
    'mr-1' => 'mr-1',
    'mr-2' => 'mr-2',
    'mr-3' => 'mr-3',
    'mr-4' => 'mr-4',
    'mr-5' => 'mr-5',
    'p-0' => 'p-0',
    'p-1' => 'p-1',
    'p-2' => 'p-2',
    'p-3' => 'p-3',
    'p-4' => 'p-4',
    'p-5' => 'p-5',
    'pt-0' => 'pt-0',
    'pt-1' => 'pt-1',
    'pt-2' => 'pt-2',
    'pt-3' => 'pt-3',
    'pt-4' => 'pt-4',
    'pt-5' => 'pt-5',
    'pb-0' => 'pb-0',
    'pb-1' => 'pb-1',
    'pb-2' => 'pb-2',
    'pb-3' => 'pb-3',
    'pb-4' => 'pb-4',
    'pb-5' => 'pb-5',
    'pl-0' => 'pl-0',
    'pl-1' => 'pl-1',
    'pl-2' => 'pl-2',
    'pl-3' => 'pl-3',
    'pl-4' => 'pl-4',
    'pl-5' => 'pl-5',
    'pr-0' => 'pr-0',
    'pr-1' => 'pr-1',
    'pr-2' => 'pr-2',
    'pr-3' => 'pr-3',
    'pr-4' => 'pr-4',
    'pr-5' => 'pr-5',

    // Positioning
    'position-static' => 'static',
    'position-relative' => 'relative',
    'position-absolute' => 'absolute',
    'position-fixed' => 'fixed',
    'position-sticky' => 'sticky',

    // Flexbox
    'justify-content-start' => 'justify-start',
    'justify-content-end' => 'justify-end',
    'justify-content-center' => 'justify-center',
    'justify-content-between' => 'justify-between',
    'justify-content-around' => 'justify-around',
    'align-items-start' => 'items-start',
    'align-items-end' => 'items-end',
    'align-items-center' => 'items-center',
    'align-items-baseline' => 'items-baseline',
    'align-items-stretch' => 'items-stretch',
    'flex-row' => 'flex-row',
    'flex-column' => 'flex-col',
    'flex-wrap' => 'flex-wrap',
    'flex-nowrap' => 'flex-nowrap',

    // Borders
    'border' => 'border',
    'border-0' => 'border-0',
    'border-top' => 'border-t',
    'border-right' => 'border-r',
    'border-bottom' => 'border-b',
    'border-left' => 'border-l',
    'border-primary' => 'border-indigo-600',
    'border-secondary' => 'border-gray-600',
    'border-success' => 'border-green-600',
    'border-danger' => 'border-red-600',
    'border-warning' => 'border-yellow-600',
    'border-info' => 'border-blue-600',
    'border-light' => 'border-gray-200',
    'border-dark' => 'border-gray-900',
    'rounded' => 'rounded',
    'rounded-sm' => 'rounded-sm',
    'rounded-lg' => 'rounded-lg',
    'rounded-pill' => 'rounded-full',
    'rounded-0' => 'rounded-none',

    // Shadows
    'shadow' => 'shadow',
    'shadow-sm' => 'shadow-sm',
    'shadow-lg' => 'shadow-lg',
    'shadow-none' => 'shadow-none',

    // Width & Height
    'w-25' => 'w-1/4',
    'w-50' => 'w-1/2',
    'w-75' => 'w-3/4',
    'w-100' => 'w-full',
    'h-25' => 'h-1/4',
    'h-50' => 'h-1/2',
    'h-75' => 'h-3/4',
    'h-100' => 'h-full',

    // Overflow
    'overflow-auto' => 'overflow-auto',
    'overflow-hidden' => 'overflow-hidden',
    'overflow-visible' => 'overflow-visible',
    'overflow-scroll' => 'overflow-scroll',

    // Float
    'float-left' => 'float-left',
    'float-right' => 'float-right',
    'float-none' => 'float-none',

    // Clear
    'clearfix' => 'clear-both',

    // List styles
    'list-unstyled' => 'list-none',
    'list-inline' => 'inline-flex space-x-4',
    'list-inline-item' => '',

    // Modal
    'modal' => 'fixed inset-0 z-50 overflow-y-auto',
    'modal-dialog' => 'flex items-center justify-center min-h-screen px-4',
    'modal-content' => 'bg-white rounded-lg shadow-xl max-w-md w-full',
    'modal-header' => 'px-6 py-4 border-b border-gray-200',
    'modal-body' => 'px-6 py-4',
    'modal-footer' => 'px-6 py-4 border-t border-gray-200 flex justify-end space-x-2',
    'modal-title' => 'text-lg font-medium text-gray-900',

    // Dropdown
    'dropdown' => 'relative inline-block text-left',
    'dropdown-toggle' => 'inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50',
    'dropdown-menu' => 'origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5',
    'dropdown-item' => 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100',

    // Spinner
    'spinner-border' => 'animate-spin rounded-full border-2 border-gray-300 border-t-blue-600',
    'spinner-border-sm' => 'animate-spin rounded-full border-2 border-gray-300 border-t-blue-600 h-4 w-4',

    // Progress
    'progress' => 'w-full bg-gray-200 rounded-full',
    'progress-bar' => 'bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full',

    // Close button
    'btn-close' => 'text-gray-400 hover:text-gray-600 focus:outline-none',

    // Accordion
    'accordion' => 'divide-y divide-gray-200',
    'accordion-item' => 'border border-gray-200',
    'accordion-header' => 'px-4 py-5 bg-gray-50',
    'accordion-body' => 'px-4 py-5',

    // Collapse
    'collapse' => 'hidden',
    'collapsing' => 'transition-all duration-300 ease-in-out',

    // Pagination
    'pagination' => 'flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6',
    'page-item' => '',
    'page-link' => 'relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50',

    // Breadcrumb
    'breadcrumb-item' => 'flex items-center text-sm text-gray-500',

    // List group
    'list-group' => 'divide-y divide-gray-200',
    'list-group-item' => 'px-6 py-4 hover:bg-gray-50',

    // Toast
    'toast' => 'max-w-sm bg-white shadow-lg rounded-lg pointer-events-auto',
    'toast-header' => 'flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200 rounded-t-lg',
    'toast-body' => 'px-4 py-3',

    // Offcanvas
    'offcanvas' => 'fixed inset-y-0 left-0 z-50 w-80 bg-white shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out',
    'offcanvas-header' => 'flex items-center justify-between px-6 py-4 border-b border-gray-200',
    'offcanvas-body' => 'px-6 py-4',

    // Input groups
    'input-group-prepend' => 'flex',
    'input-group-append' => 'flex',
];

// Get all blade files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$blade_files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $blade_files[] = $file->getPathname();
    }
}

$total_files = count($blade_files);
echo "📊 Found {$total_files} total files\n";

// Find files that still contain Bootstrap classes
$files_to_migrate = [];
foreach ($blade_files as $file) {
    $content = file_get_contents($file);
    $has_bootstrap = false;
    
    foreach (array_keys($bootstrap_to_tailwind) as $bootstrap_class) {
        if (preg_match('/class=["\'][^"\']*\b' . preg_quote($bootstrap_class, '/') . '\b[^"\']*["\']/', $content)) {
            $has_bootstrap = true;
            break;
        }
    }
    
    if ($has_bootstrap) {
        $files_to_migrate[] = $file;
    }
}

$remaining_files = count($files_to_migrate);
echo "🎯 Processing {$remaining_files} remaining files\n\n";

// Process files in larger batches
$batch_size = 50; // Increased batch size
$batches = array_chunk($files_to_migrate, $batch_size);
$total_batches = count($batches);

$total_migrated = 0;
$total_classes_replaced = 0;

for ($i = 0; $i < min(3, $total_batches); $i++) { // Process up to 3 batches
    $batch = $batches[$i];
    echo "📦 Processing batch " . ($i + 1) . "/{$total_batches} (" . count($batch) . " files)\n";
    
    foreach ($batch as $file) {
        $content = file_get_contents($file);
        $original_content = $content;
        $classes_replaced = 0;
        
        // Replace Bootstrap classes with TailwindCSS
        foreach ($bootstrap_to_tailwind as $bootstrap_class => $tailwind_class) {
            $pattern = '/class=(["\'])([^"\']*)\b' . preg_quote($bootstrap_class, '/') . '\b([^"\']*)\1/';
            
            $content = preg_replace_callback($pattern, function($matches) use ($bootstrap_class, $tailwind_class, &$classes_replaced) {
                $quote = $matches[1];
                $before_class = $matches[2];
                $after_class = $matches[3];
                
                // Remove the Bootstrap class and add TailwindCSS class
                $new_classes = trim($before_class . ' ' . $after_class);
                $new_classes = preg_replace('/\s+/', ' ', $new_classes); // Clean up multiple spaces
                
                // Add TailwindCSS class
                if (!empty($new_classes)) {
                    $new_classes = $tailwind_class . ' ' . $new_classes;
                } else {
                    $new_classes = $tailwind_class;
                }
                
                $classes_replaced++;
                return 'class=' . $quote . trim($new_classes) . $quote;
            }, $content);
        }
        
        // Only write if content changed
        if ($content !== $original_content) {
            file_put_contents($file, $content);
            $filename = basename($file);
            echo "  ✅ {$filename} ({$classes_replaced} classes)\n";
            $total_migrated++;
            $total_classes_replaced += $classes_replaced;
        }
    }
    
    echo "✅ Batch " . ($i + 1) . " complete\n\n";
}

echo "======================================================================\n";
echo "🚀 ENHANCED AGGRESSIVE TAILWINDCSS MIGRATION COMPLETED\n";
echo "======================================================================\n\n";

echo "📊 MIGRATION SUMMARY:\n";
echo "- Files Migrated: {$total_migrated}\n";
echo "- Bootstrap Classes Replaced: {$total_classes_replaced}\n\n";

echo "🎯 NEXT STEPS:\n";
echo "1. Run npm run build to compile TailwindCSS\n";
echo "2. Run script again to process more batches\n";
echo "3. Test migrated components\n";
echo "4. Remove Bootstrap dependencies when complete\n\n";

echo "✅ Enhanced migration batch complete!\n";

?> 