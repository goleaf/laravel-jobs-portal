<?php

echo "🚀 Starting Aggressive Bootstrap to TailwindCSS Migration...\n\n";

// Comprehensive Bootstrap to TailwindCSS mappings
$bootstrap_to_tailwind = [
    // Layout & Grid
    'container-fluid' => 'w-full px-4',
    'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
    'row' => 'flex flex-wrap -mx-4',
    'col' => 'flex-1 px-4',
    
    // Grid columns
    'col-1' => 'w-1/12 px-4', 'col-2' => 'w-2/12 px-4', 'col-3' => 'w-3/12 px-4',
    'col-4' => 'w-4/12 px-4', 'col-5' => 'w-5/12 px-4', 'col-6' => 'w-6/12 px-4',
    'col-7' => 'w-7/12 px-4', 'col-8' => 'w-8/12 px-4', 'col-9' => 'w-9/12 px-4',
    'col-10' => 'w-10/12 px-4', 'col-11' => 'w-11/12 px-4', 'col-12' => 'w-full px-4',
    
    'col-sm-1' => 'sm:w-1/12 px-4', 'col-sm-2' => 'sm:w-2/12 px-4', 'col-sm-3' => 'sm:w-3/12 px-4',
    'col-sm-4' => 'sm:w-4/12 px-4', 'col-sm-5' => 'sm:w-5/12 px-4', 'col-sm-6' => 'sm:w-6/12 px-4',
    'col-sm-7' => 'sm:w-7/12 px-4', 'col-sm-8' => 'sm:w-8/12 px-4', 'col-sm-9' => 'sm:w-9/12 px-4',
    'col-sm-10' => 'sm:w-10/12 px-4', 'col-sm-11' => 'sm:w-11/12 px-4', 'col-sm-12' => 'sm:w-full px-4',
    
    'col-md-1' => 'md:w-1/12 px-4', 'col-md-2' => 'md:w-2/12 px-4', 'col-md-3' => 'md:w-3/12 px-4',
    'col-md-4' => 'md:w-4/12 px-4', 'col-md-5' => 'md:w-5/12 px-4', 'col-md-6' => 'md:w-6/12 px-4',
    'col-md-7' => 'md:w-7/12 px-4', 'col-md-8' => 'md:w-8/12 px-4', 'col-md-9' => 'md:w-9/12 px-4',
    'col-md-10' => 'md:w-10/12 px-4', 'col-md-11' => 'md:w-11/12 px-4', 'col-md-12' => 'md:w-full px-4',
    
    'col-lg-1' => 'lg:w-1/12 px-4', 'col-lg-2' => 'lg:w-2/12 px-4', 'col-lg-3' => 'lg:w-3/12 px-4',
    'col-lg-4' => 'lg:w-4/12 px-4', 'col-lg-5' => 'lg:w-5/12 px-4', 'col-lg-6' => 'lg:w-6/12 px-4',
    'col-lg-7' => 'lg:w-7/12 px-4', 'col-lg-8' => 'lg:w-8/12 px-4', 'col-lg-9' => 'lg:w-9/12 px-4',
    'col-lg-10' => 'lg:w-10/12 px-4', 'col-lg-11' => 'lg:w-11/12 px-4', 'col-lg-12' => 'lg:w-full px-4',

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
    'btn-sm' => 'px-3 py-1.5 text-xs',
    'btn-lg' => 'px-6 py-3 text-lg',
    'btn-block' => 'w-full justify-center',

    // Forms
    'form-control' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
    'form-select' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
    'form-label' => 'block text-sm font-medium text-gray-700 mb-1',
    'form-text' => 'text-sm text-gray-500',
    'form-group' => 'mb-4',

    // Cards
    'card' => 'bg-white overflow-hidden shadow rounded-lg',
    'card-header' => 'px-4 py-5 border-b border-gray-200 sm:px-6',
    'card-body' => 'px-4 py-5 sm:p-6',
    'card-footer' => 'px-4 py-4 border-t border-gray-200 sm:px-6',
    'card-title' => 'text-lg font-medium text-gray-900',

    // Tables
    'table' => 'min-w-full divide-y divide-gray-200',
    'table-striped' => 'bg-white divide-y divide-gray-200',
    'table-hover' => 'hover:bg-gray-50',

    // Alerts
    'alert' => 'rounded-md p-4',
    'alert-primary' => 'bg-blue-50 border border-blue-200 text-blue-700',
    'alert-success' => 'bg-green-50 border border-green-200 text-green-700',
    'alert-danger' => 'bg-red-50 border border-red-200 text-red-700',
    'alert-warning' => 'bg-yellow-50 border border-yellow-200 text-yellow-700',

    // Badges
    'badge' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
    'badge-primary' => 'bg-indigo-100 text-indigo-800',
    'badge-success' => 'bg-green-100 text-green-800',
    'badge-danger' => 'bg-red-100 text-red-800',
    'badge-warning' => 'bg-yellow-100 text-yellow-800',

    // Display utilities
    'd-none' => 'hidden',
    'd-block' => 'block',
    'd-inline' => 'inline',
    'd-inline-block' => 'inline-block',
    'd-flex' => 'flex',
    'd-sm-none' => 'sm:hidden',
    'd-sm-block' => 'sm:block',
    'd-md-none' => 'md:hidden',
    'd-md-block' => 'md:block',
    'd-lg-none' => 'lg:hidden',
    'd-lg-block' => 'lg:block',

    // Text utilities
    'text-center' => 'text-center',
    'text-left' => 'text-left',
    'text-right' => 'text-right',
    'text-muted' => 'text-gray-500',
    'text-primary' => 'text-indigo-600',
    'text-success' => 'text-green-600',
    'text-danger' => 'text-red-600',
    'text-warning' => 'text-yellow-600',

    // Spacing utilities
    'm-0' => 'm-0', 'm-1' => 'm-1', 'm-2' => 'm-2', 'm-3' => 'm-3', 'm-4' => 'm-4', 'm-5' => 'm-5',
    'mt-0' => 'mt-0', 'mt-1' => 'mt-1', 'mt-2' => 'mt-2', 'mt-3' => 'mt-3', 'mt-4' => 'mt-4', 'mt-5' => 'mt-5',
    'mb-0' => 'mb-0', 'mb-1' => 'mb-1', 'mb-2' => 'mb-2', 'mb-3' => 'mb-3', 'mb-4' => 'mb-4', 'mb-5' => 'mb-5',
    'ml-0' => 'ml-0', 'ml-1' => 'ml-1', 'ml-2' => 'ml-2', 'ml-3' => 'ml-3', 'ml-4' => 'ml-4', 'ml-5' => 'ml-5',
    'mr-0' => 'mr-0', 'mr-1' => 'mr-1', 'mr-2' => 'mr-2', 'mr-3' => 'mr-3', 'mr-4' => 'mr-4', 'mr-5' => 'mr-5',
    'p-0' => 'p-0', 'p-1' => 'p-1', 'p-2' => 'p-2', 'p-3' => 'p-3', 'p-4' => 'p-4', 'p-5' => 'p-5',
    'pt-0' => 'pt-0', 'pt-1' => 'pt-1', 'pt-2' => 'pt-2', 'pt-3' => 'pt-3', 'pt-4' => 'pt-4', 'pt-5' => 'pt-5',
    'pb-0' => 'pb-0', 'pb-1' => 'pb-1', 'pb-2' => 'pb-2', 'pb-3' => 'pb-3', 'pb-4' => 'pb-4', 'pb-5' => 'pb-5',
    'pl-0' => 'pl-0', 'pl-1' => 'pl-1', 'pl-2' => 'pl-2', 'pl-3' => 'pl-3', 'pl-4' => 'pl-4', 'pl-5' => 'pl-5',
    'pr-0' => 'pr-0', 'pr-1' => 'pr-1', 'pr-2' => 'pr-2', 'pr-3' => 'pr-3', 'pr-4' => 'pr-4', 'pr-5' => 'pr-5',

    // Flexbox
    'justify-content-start' => 'justify-start',
    'justify-content-center' => 'justify-center',
    'justify-content-between' => 'justify-between',
    'align-items-center' => 'items-center',
    'flex-wrap' => 'flex-wrap',

    // Background colors
    'bg-primary' => 'bg-indigo-600',
    'bg-success' => 'bg-green-600',
    'bg-danger' => 'bg-red-600',
    'bg-warning' => 'bg-yellow-600',
    'bg-light' => 'bg-gray-100',
    'bg-dark' => 'bg-gray-900',

    // Border utilities
    'border' => 'border',
    'border-0' => 'border-0',
    'rounded' => 'rounded',
    'rounded-sm' => 'rounded-sm',
    'rounded-lg' => 'rounded-lg',

    // Width utilities
    'w-25' => 'w-1/4',
    'w-50' => 'w-1/2',
    'w-75' => 'w-3/4',
    'w-100' => 'w-full',

    // Float utilities
    'float-left' => 'float-left',
    'float-right' => 'float-right',
];

// Get all blade files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$files_to_migrate = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        foreach (array_keys($bootstrap_to_tailwind) as $bootstrap_class) {
            if (strpos($content, $bootstrap_class) !== false) {
                $files_to_migrate[] = $file->getPathname();
                break;
            }
        }
    }
}

echo "🎯 Processing " . count($files_to_migrate) . " files with Bootstrap classes\n\n";

$total_migrated = 0;
$total_classes_replaced = 0;

// Process files in batches of 100 for aggressive processing
$batch_size = 100;
$batch = array_slice($files_to_migrate, 0, $batch_size);

foreach ($batch as $file) {
    $content = file_get_contents($file);
    $original_content = $content;
    $classes_replaced = 0;
    
    foreach ($bootstrap_to_tailwind as $bootstrap_class => $tailwind_class) {
        $count = 0;
        $content = str_replace($bootstrap_class, $tailwind_class, $content, $count);
        $classes_replaced += $count;
    }
    
    if ($content !== $original_content) {
        file_put_contents($file, $content);
        echo "✅ " . basename($file) . " ({$classes_replaced} classes)\n";
        $total_migrated++;
        $total_classes_replaced += $classes_replaced;
    }
}

echo "\n📊 MIGRATION SUMMARY:\n";
echo "- Files Migrated: {$total_migrated}\n";
echo "- Bootstrap Classes Replaced: {$total_classes_replaced}\n";
echo "- Remaining Files: " . (count($files_to_migrate) - $total_migrated) . "\n\n";

echo "🎯 NEXT STEPS:\n";
echo "1. Run npm run build to compile TailwindCSS\n";
echo "2. Run script again to process more batches\n";
echo "3. Test migrated components\n\n";

echo "✅ Aggressive migration batch complete!\n";

?>
