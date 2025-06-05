<?php

echo "🔍 Finding all remaining Bootstrap files...\n\n";

// Bootstrap classes to search for
$bootstrap_classes = [
    'container', 'container-fluid', 'row', 'col', 'col-', 
    'btn', 'btn-', 'form-control', 'form-group', 'form-label',
    'card', 'card-', 'table', 'table-', 'alert', 'alert-',
    'badge', 'badge-', 'd-none', 'd-block', 'd-flex',
    'text-center', 'text-left', 'text-right', 'text-muted',
    'm-', 'mt-', 'mb-', 'ml-', 'mr-', 'p-', 'pt-', 'pb-', 'pl-', 'pr-',
    'bg-primary', 'bg-success', 'bg-danger', 'bg-warning',
    'justify-content-', 'align-items-', 'flex-wrap'
];

// Find all files with Bootstrap classes
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$bootstrap_files = [];
$total_bootstrap_instances = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $file_instances = 0;
        
        foreach ($bootstrap_classes as $class) {
            $count = substr_count($content, $class);
            $file_instances += $count;
        }
        
        if ($file_instances > 0) {
            $relative_path = str_replace(getcwd() . '/resources/views/', '', $file->getPathname());
            $bootstrap_files[] = [
                'path' => $file->getPathname(),
                'relative' => $relative_path,
                'instances' => $file_instances
            ];
            $total_bootstrap_instances += $file_instances;
        }
    }
}

// Sort by number of instances (highest first)
usort($bootstrap_files, function($a, $b) {
    return $b['instances'] - $a['instances'];
});

echo "📊 BOOTSTRAP ANALYSIS RESULTS:\n";
echo "- Total files with Bootstrap: " . count($bootstrap_files) . "\n";
echo "- Total Bootstrap instances: {$total_bootstrap_instances}\n\n";

echo "🎯 TOP 20 FILES (Most Bootstrap Usage):\n";
for ($i = 0; $i < min(20, count($bootstrap_files)); $i++) {
    echo "  " . ($i + 1) . ". " . $bootstrap_files[$i]['relative'] . " ({$bootstrap_files[$i]['instances']} instances)\n";
}

echo "\n📝 ALL REMAINING FILES:\n";
foreach ($bootstrap_files as $index => $file) {
    echo ($index + 1) . ". " . $file['relative'] . " ({$file['instances']} instances)\n";
}

echo "\n✅ Analysis complete!\n";

?> 