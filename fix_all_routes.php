<?php

// Fix all broken routes in home.blade.php
$file = 'resources/views/front_web/home/home.blade.php';
$content = file_get_contents($file);

// Route fixes based on analysis
$fixes = [
    // Job category routes - should point to search.jobs with category filter
    "route('front.', ['categories' => \$jobCategory->id])" => "route('search.jobs', ['categories' => \$jobCategory->id])",
    
    // Featured jobs routes - should point to search.jobs with featured filter
    "route('front.', ['is_featured' => true])" => "route('search.jobs', ['is_featured' => true])",
    "route('front.',['is_featured' => true])" => "route('search.jobs', ['is_featured' => true])",
    
    // General job search routes - should point to search.jobs
    "route('front.')" => "route('search.jobs')",
    
    // Post detail routes - should point to posts.details
    "route('front.', \$post->id)" => "route('posts.details', \$post->id)",
];

// Apply all fixes
foreach ($fixes as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

// Write back to file
file_put_contents($file, $content);

echo "Fixed all route issues in home.blade.php\n";
echo "Applied " . count($fixes) . " route fixes\n"; 