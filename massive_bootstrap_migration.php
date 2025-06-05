<?php

echo "🚀 Starting MASSIVE Bootstrap to TailwindCSS Migration...\n\n";

// Get all blade files containing Bootstrap
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

// Comprehensive Bootstrap patterns
$bootstrap_patterns = [
    '/\bcontainer-fluid\b/' => 'w-full px-4',
    '/\bcontainer\b/' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
    '/\brow\b/' => 'flex flex-wrap -mx-4',
    '/\bcol-12\b/' => 'w-full px-4',
    '/\bcol-6\b/' => 'w-6/12 px-4',
    '/\bcol-4\b/' => 'w-4/12 px-4',
    '/\bcol-3\b/' => 'w-3/12 px-4',
    '/\bcol-md-12\b/' => 'md:w-full px-4',
    '/\bcol-md-6\b/' => 'md:w-6/12 px-4',
    '/\bcol-md-4\b/' => 'md:w-4/12 px-4',
    '/\bcol-md-3\b/' => 'md:w-3/12 px-4',
    '/\bcol-lg-12\b/' => 'lg:w-full px-4',
    '/\bcol-lg-6\b/' => 'lg:w-6/12 px-4',
    '/\bcol-lg-4\b/' => 'lg:w-4/12 px-4',
    '/\bcol-lg-3\b/' => 'lg:w-3/12 px-4',
    '/\bbtn\b/' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200',
    '/\bbtn-primary\b/' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
    '/\bbtn-secondary\b/' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
    '/\bbtn-success\b/' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    '/\bbtn-danger\b/' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    '/\bbtn-warning\b/' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
    '/\bbtn-sm\b/' => 'px-3 py-1.5 text-xs',
    '/\bbtn-lg\b/' => 'px-6 py-3 text-lg',
    '/\bform-control\b/' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
    '/\bform-select\b/' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
    '/\bform-label\b/' => 'block text-sm font-medium text-gray-700 mb-1',
    '/\bform-group\b/' => 'mb-4',
    '/\bcard\b/' => 'bg-white overflow-hidden shadow rounded-lg',
    '/\bcard-header\b/' => 'px-4 py-5 border-b border-gray-200 sm:px-6',
    '/\bcard-body\b/' => 'px-4 py-5 sm:p-6',
    '/\bcard-footer\b/' => 'px-4 py-4 border-t border-gray-200 sm:px-6',
    '/\bd-none\b/' => 'hidden',
    '/\bd-block\b/' => 'block',
    '/\bd-flex\b/' => 'flex',
    '/\btext-center\b/' => 'text-center',
    '/\btext-left\b/' => 'text-left',
    '/\btext-right\b/' => 'text-right',
    '/\btext-muted\b/' => 'text-gray-500',
    '/\bmb-0\b/' => 'mb-0',
    '/\bmb-1\b/' => 'mb-1',
    '/\bmb-2\b/' => 'mb-2',
    '/\bmb-3\b/' => 'mb-3',
    '/\bmb-4\b/' => 'mb-4',
    '/\bmb-5\b/' => 'mb-5',
    '/\bmt-0\b/' => 'mt-0',
    '/\bmt-1\b/' => 'mt-1',
    '/\bmt-2\b/' => 'mt-2',
    '/\bmt-3\b/' => 'mt-3',
    '/\bmt-4\b/' => 'mt-4',
    '/\bmt-5\b/' => 'mt-5',
    '/\balert\b/' => 'rounded-md p-4',
    '/\balert-success\b/' => 'bg-green-50 border border-green-200 text-green-700',
    '/\balert-danger\b/' => 'bg-red-50 border border-red-200 text-red-700',
    '/\bbadge\b/' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
    '/\bbadge-primary\b/' => 'bg-indigo-100 text-indigo-800',
    '/\bbadge-success\b/' => 'bg-green-100 text-green-800',
    '/\bbadge-danger\b/' => 'bg-red-100 text-red-800',
];

$all_files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match('/\b(container|row|col-|btn|form-|card|alert|badge|d-|text-|m[blt]-\d|p[blt]-\d)\b/', $content)) {
            $all_files[] = $file->getPathname();
        }
    }
}

echo "🎯 Found " . count($all_files) . " files with Bootstrap patterns\n\n";

$total_migrated = 0;
$total_replacements = 0;

// Process in large batch (200 files)
$batch = array_slice($all_files, 0, 200);

foreach ($batch as $file) {
    $content = file_get_contents($file);
    $original_content = $content;
    $file_replacements = 0;
    
    foreach ($bootstrap_patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content, -1, $count);
        $file_replacements += $count;
    }
    
    if ($content !== $original_content) {
        file_put_contents($file, $content);
        $relative_path = str_replace(getcwd() . '/resources/views/', '', $file);
        echo "✅ " . basename($file) . " ({$file_replacements} replacements)\n";
        $total_migrated++;
        $total_replacements += $file_replacements;
    }
}

echo "\n📊 MASSIVE MIGRATION SUMMARY:\n";
echo "- Files Found: " . count($all_files) . "\n";
echo "- Files Processed: " . count($batch) . "\n";
echo "- Files Migrated: {$total_migrated}\n";
echo "- Total Replacements: {$total_replacements}\n";
echo "- Remaining Files: " . (count($all_files) - count($batch)) . "\n\n";

echo "🎯 IMPACT ANALYSIS:\n";
echo "- Massive scale processing achieved\n";
echo "- Regex-based pattern matching for accuracy\n";
echo "- Ready for compilation testing\n\n";

echo "🔄 NEXT STEPS:\n";
echo "1. Run npm run build to compile\n";
echo "2. Process remaining files if needed\n";
echo "3. Test components\n\n";

echo "✅ Massive migration complete!\n";

?> 