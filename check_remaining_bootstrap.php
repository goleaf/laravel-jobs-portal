<?php

/**
 * Check Remaining Bootstrap Classes
 * Quick assessment of remaining work
 */

echo "🔍 Checking remaining Bootstrap classes...\n\n";

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$bootstrap_patterns = [
    'btn ', 'btn-', 'form-control', 'container', 'row', 'col-', 
    'card ', 'table ', 'alert-', 'badge-', 'd-flex', 'd-none',
    'text-center', 'text-right', 'ml-', 'mr-', 'mt-', 'mb-',
    'p-1', 'p-2', 'p-3', 'm-1', 'm-2', 'm-3'
];

$remaining_files = [];
$total_files = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $total_files++;
        $content = file_get_contents($file->getPathname());
        $has_bootstrap = false;
        
        foreach ($bootstrap_patterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                $remaining_files[] = $file->getPathname();
                $has_bootstrap = true;
                break;
            }
        }
    }
}

echo "📊 REMAINING BOOTSTRAP ANALYSIS:\n";
echo "- Total blade files: {$total_files}\n";
echo "- Files with Bootstrap: " . count($remaining_files) . "\n";
echo "- Completion rate: " . round((($total_files - count($remaining_files)) / $total_files) * 100, 1) . "%\n\n";

echo "📋 SAMPLE REMAINING FILES:\n";
for ($i = 0; $i < min(15, count($remaining_files)); $i++) {
    echo "  " . ($i + 1) . ". " . str_replace('resources/views/', '', $remaining_files[$i]) . "\n";
}

if (count($remaining_files) > 15) {
    echo "  ... and " . (count($remaining_files) - 15) . " more files\n";
}

echo "\n🎯 NEXT ACTIONS:\n";
if (count($remaining_files) > 0) {
    echo "- Continue migration script to process remaining " . count($remaining_files) . " files\n";
    echo "- Estimated batches needed: " . ceil(count($remaining_files) / 50) . " batches\n";
} else {
    echo "- ✅ Bootstrap to TailwindCSS migration COMPLETE!\n";
    echo "- Ready for final testing and validation\n";
}

echo "\n✅ Analysis complete!\n";

?> 