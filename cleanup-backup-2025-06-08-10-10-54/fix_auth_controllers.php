<?php
/**
 * Fix Auth Controllers Script
 * Fixes malformed use statements in Auth controllers
 */

$authControllers = [
    'app/Http/Controllers/Auth/LoginController.php',
    'app/Http/Controllers/Auth/ConfirmPasswordController.php', 
    'app/Http/Controllers/Auth/ResetPasswordController.php',
    'app/Http/Controllers/Auth/RegisterController.php',
    'app/Http/Controllers/Auth/VerificationController.php',
    'app/Http/Controllers/Auth/Front/LoginController.php'
];

foreach ($authControllers as $controller) {
    if (!file_exists($controller)) {
        echo "❌ File not found: {$controller}\n";
        continue;
    }
    
    $content = file_get_contents($controller);
    $originalContent = $content;
    
    // Remove malformed use statements inside class
    $content = preg_replace('/use App\\\\Http\\\\Requests\\\\Auth\\\\[^;]+;/', '', $content);
    
    // Fix specific controller patterns
    $filename = basename($controller);
    
    switch ($filename) {
        case 'LoginController.php':
            $content = str_replace(
                'use Illuminate\Foundation\Auth\AuthenticatesUsers;',
                "use App\Http\Requests\Auth\LoginRequest;\nuse Illuminate\Foundation\Auth\AuthenticatesUsers;",
                $content
            );
            break;
            
        case 'RegisterController.php':
            $content = str_replace(
                'use Illuminate\Foundation\Auth\RegistersUsers;',
                "use App\Http\Requests\Auth\RegisterRequest;\nuse Illuminate\Foundation\Auth\RegistersUsers;",
                $content
            );
            break;
            
        case 'ResetPasswordController.php':
            $content = str_replace(
                'use Illuminate\Foundation\Auth\ResetsPasswords;',
                "use App\Http\Requests\Auth\ResetPasswordRequest;\nuse Illuminate\Foundation\Auth\ResetsPasswords;",
                $content
            );
            break;
            
        case 'ConfirmPasswordController.php':
            $content = str_replace(
                'use Illuminate\Foundation\Auth\ConfirmsPasswords;',
                "use Illuminate\Foundation\Auth\ConfirmsPasswords;",
                $content
            );
            break;
            
        case 'VerificationController.php':
            $content = str_replace(
                'use Illuminate\Foundation\Auth\VerifiesEmails;',
                "use Illuminate\Foundation\Auth\VerifiesEmails;",
                $content
            );
            break;
    }
    
    // Clean up any duplicate use statements
    $lines = explode("\n", $content);
    $useStatements = [];
    $cleanedLines = [];
    $inUseSection = false;
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        if (str_starts_with($trimmed, 'use ') && str_ends_with($trimmed, ';')) {
            if (!in_array($trimmed, $useStatements)) {
                $useStatements[] = $trimmed;
                $cleanedLines[] = $line;
            }
        } else {
            $cleanedLines[] = $line;
        }
    }
    
    $content = implode("\n", $cleanedLines);
    
    if ($content !== $originalContent) {
        file_put_contents($controller, $content);
        echo "✅ Fixed: {$controller}\n";
    } else {
        echo "ℹ️  No changes needed: {$controller}\n";
    }
}

echo "\n🎯 Auth controllers fix completed!\n"; 