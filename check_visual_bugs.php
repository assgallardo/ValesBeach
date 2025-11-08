<?php

/**
 * Visual Bug Detection Script
 * Scans all blade files for common visual issues
 */

echo "═══════════════════════════════════════════════════\n";
echo "  VISUAL BUG DETECTION SCAN\n";
echo "═══════════════════════════════════════════════════\n\n";

$viewsPath = __DIR__ . '/resources/views';
$issues = [];
$warnings = [];

// Common visual bug patterns
$patterns = [
    'unclosed_div' => [
        'pattern' => '/<div[^>]*>(?!.*<\/div>)/s',
        'severity' => 'high',
        'description' => 'Unclosed div tags'
    ],
    'inline_styles' => [
        'pattern' => '/style=["\'][^"\']+["\']/i',
        'severity' => 'low',
        'description' => 'Inline styles (should use Tailwind classes)'
    ],
    'missing_responsive' => [
        'pattern' => '/class=["\'][^"\']*\btext-\d+xl\b[^"\']*["\']/i',
        'severity' => 'medium',
        'description' => 'Large text without responsive classes (md:, lg:)'
    ],
    'hardcoded_colors' => [
        'pattern' => '/#[0-9a-f]{3,6}/i',
        'severity' => 'low',
        'description' => 'Hardcoded color values'
    ],
    'broken_grid' => [
        'pattern' => '/grid-cols-\d+(?!.*gap-)/i',
        'severity' => 'medium',
        'description' => 'Grid without gap spacing'
    ],
    'missing_alt' => [
        'pattern' => '/<img[^>]+(?!alt=)/i',
        'severity' => 'high',
        'description' => 'Image tags without alt attribute'
    ],
    'fixed_width' => [
        'pattern' => '/\bw-\[\d+px\]/i',
        'severity' => 'medium',
        'description' => 'Fixed pixel widths (not responsive)'
    ],
    'overflow_hidden_text' => [
        'pattern' => '/overflow-hidden[^>]*>.*text-/i',
        'severity' => 'medium',
        'description' => 'Text with overflow-hidden (may cut off content)'
    ],
];

function scanDirectory($dir, &$issues, &$warnings, $patterns) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );

    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file->getPathname());
            $content = file_get_contents($file->getPathname());
            
            // Check for common issues
            checkDivBalance($file->getPathname(), $relativePath, $content, $issues);
            checkResponsiveness($file->getPathname(), $relativePath, $content, $warnings);
            checkTailwindClasses($file->getPathname(), $relativePath, $content, $warnings);
            checkAlpineJs($file->getPathname(), $relativePath, $content, $issues);
            checkFormElements($file->getPathname(), $relativePath, $content, $warnings);
        }
    }
}

function checkDivBalance($file, $relativePath, $content, &$issues) {
    // Remove blade directives and comments
    $cleanContent = preg_replace('/@\w+.*?@end\w+/s', '', $content);
    $cleanContent = preg_replace('/{{--.*?--}}/s', '', $cleanContent);
    
    $openDivs = preg_match_all('/<div[^>]*>/i', $cleanContent);
    $closeDivs = preg_match_all('/<\/div>/i', $cleanContent);
    
    if ($openDivs !== $closeDivs) {
        $issues[] = [
            'file' => $relativePath,
            'type' => 'Unbalanced DIV tags',
            'severity' => 'HIGH',
            'details' => "Opening: $openDivs, Closing: $closeDivs"
        ];
    }
}

function checkResponsiveness($file, $relativePath, $content, &$warnings) {
    // Check for large containers without responsive classes
    if (preg_match('/class=["\'][^"\']*\b(w-full|max-w-7xl|container)\b/i', $content)) {
        if (!preg_match('/(sm:|md:|lg:|xl:)/i', $content)) {
            $warnings[] = [
                'file' => $relativePath,
                'type' => 'Missing responsive classes',
                'severity' => 'LOW',
                'details' => 'Page may not be mobile-friendly'
            ];
        }
    }
}

function checkTailwindClasses($file, $relativePath, $content, &$warnings) {
    // Check for duplicate classes
    if (preg_match_all('/class=["\']([^"\']+)["\']/i', $content, $matches)) {
        foreach ($matches[1] as $classString) {
            $classes = explode(' ', $classString);
            $classCount = array_count_values($classes);
            foreach ($classCount as $class => $count) {
                if ($count > 1 && !empty(trim($class))) {
                    $warnings[] = [
                        'file' => $relativePath,
                        'type' => 'Duplicate CSS class',
                        'severity' => 'LOW',
                        'details' => "Class '$class' appears $count times in same element"
                    ];
                    break; // Only report once per file
                }
            }
        }
    }
    
    // Check for conflicting classes
    $conflicts = [
        'flex' => 'block|inline|grid',
        'grid' => 'flex|block|inline',
        'hidden' => 'block|flex|grid',
    ];
    
    foreach ($conflicts as $class => $conflictPattern) {
        if (preg_match("/\b$class\b.*\b($conflictPattern)\b/i", $content)) {
            $warnings[] = [
                'file' => $relativePath,
                'type' => 'Conflicting CSS classes',
                'severity' => 'MEDIUM',
                'details' => "'$class' conflicts with display properties"
            ];
        }
    }
}

function checkAlpineJs($file, $relativePath, $content, &$issues) {
    // Check for Alpine.js scope issues (like we fixed in cottages)
    preg_match_all('/x-data=["\']({[^"\']+})["\']/i', $content, $xDataMatches, PREG_OFFSET_CAPTURE);
    
    if (count($xDataMatches[0]) > 1) {
        // Multiple x-data declarations - check if they define same variables
        $variables = [];
        foreach ($xDataMatches[1] as $match) {
            preg_match_all('/(\w+):/i', $match[0], $varMatches);
            foreach ($varMatches[1] as $var) {
                if (isset($variables[$var])) {
                    $issues[] = [
                        'file' => $relativePath,
                        'type' => 'Alpine.js scope conflict',
                        'severity' => 'HIGH',
                        'details' => "Variable '$var' defined in multiple x-data scopes"
                    ];
                    break 2;
                }
                $variables[$var] = true;
            }
        }
    }
}

function checkFormElements($file, $relativePath, $content, &$warnings) {
    // Check for input fields without labels
    if (preg_match_all('/<input[^>]+>/i', $content, $matches)) {
        foreach ($matches[0] as $input) {
            if (stripos($input, 'type="hidden"') === false) {
                // Get the id or name
                if (preg_match('/(?:id|name)=["\']([^"\']+)["\']/i', $input, $idMatch)) {
                    $id = $idMatch[1];
                    // Check if there's a label for this input
                    $pattern = '/for=["\']' . preg_quote($id, '/') . '["\']/i';
                    if (!preg_match($pattern, $content)) {
                        $warnings[] = [
                            'file' => $relativePath,
                            'type' => 'Input without label',
                            'severity' => 'LOW',
                            'details' => "Input '$id' missing associated label"
                        ];
                        break; // Only report once per file
                    }
                }
            }
        }
    }
    
    // Check for buttons without proper styling
    if (preg_match('/<button[^>]*>(?!.*\b(bg-|border-|px-|py-)\w+)/i', $content)) {
        $warnings[] = [
            'file' => $relativePath,
            'type' => 'Unstyled button',
            'severity' => 'MEDIUM',
            'details' => 'Button may be missing visual styling'
        ];
    }
}

echo "📋 Scanning blade templates...\n\n";
scanDirectory($viewsPath, $issues, $warnings, $patterns);

// Module-specific checks
echo "🔍 Checking specific modules...\n\n";

// Check dashboard pages
$dashboards = [
    'guest/dashboard.blade.php',
    'admin/dashboard.blade.php',
    'manager/dashboard.blade.php',
    'staff/dashboard.blade.php',
];

foreach ($dashboards as $dashboard) {
    $file = $viewsPath . '/' . $dashboard;
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check for stat cards consistency
        if (strpos($dashboard, 'guest') === false) {
            if (!preg_match_all('/bg-(blue|green|red|yellow|purple|indigo)-\d+/i', $content, $colorMatches)) {
                $warnings[] = [
                    'file' => $dashboard,
                    'type' => 'Dashboard cards missing colors',
                    'severity' => 'LOW',
                    'details' => 'Stat cards should have distinct colors'
                ];
            }
        }
    }
}

// Check booking forms
$bookingForms = [
    'guest/rooms/book.blade.php',
    'guest/cottages/book.blade.php',
    'guest/services/request.blade.php',
];

foreach ($bookingForms as $form) {
    $file = $viewsPath . '/' . $form;
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check for submit button
        if (!preg_match('/<button[^>]+type=["\']submit["\']/i', $content)) {
            $issues[] = [
                'file' => $form,
                'type' => 'Form missing submit button',
                'severity' => 'HIGH',
                'details' => 'Booking form needs a submit button'
            ];
        }
        
        // Check for CSRF token
        if (!preg_match('/@csrf/i', $content)) {
            $issues[] = [
                'file' => $form,
                'type' => 'Form missing CSRF token',
                'severity' => 'HIGH',
                'details' => 'Security vulnerability'
            ];
        }
    }
}

// Check navigation consistency
$layouts = [
    'layouts/guest.blade.php',
    'layouts/admin.blade.php',
    'layouts/manager.blade.php',
    'layouts/staff.blade.php',
];

foreach ($layouts as $layout) {
    $file = $viewsPath . '/' . $layout;
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check for mobile menu
        if (strpos($content, 'menu') !== false || strpos($content, 'nav') !== false) {
            if (!preg_match('/sm:|md:|lg:/i', $content)) {
                $warnings[] = [
                    'file' => $layout,
                    'type' => 'Navigation may not be responsive',
                    'severity' => 'MEDIUM',
                    'details' => 'Missing mobile menu breakpoints'
                ];
            }
        }
    }
}

// Generate Report
echo "═══════════════════════════════════════════════════\n";
echo "  VISUAL BUG REPORT\n";
echo "═══════════════════════════════════════════════════\n\n";

if (count($issues) === 0 && count($warnings) === 0) {
    echo "✅ NO VISUAL BUGS DETECTED!\n\n";
    echo "All pages appear to be visually correct.\n";
} else {
    if (count($issues) > 0) {
        echo "❌ CRITICAL ISSUES FOUND: " . count($issues) . "\n";
        echo "───────────────────────────────────────────────\n";
        foreach ($issues as $i => $issue) {
            echo "\n" . ($i + 1) . ". [{$issue['severity']}] {$issue['type']}\n";
            echo "   File: {$issue['file']}\n";
            echo "   Details: {$issue['details']}\n";
        }
        echo "\n";
    }
    
    if (count($warnings) > 0) {
        echo "⚠️  WARNINGS: " . count($warnings) . "\n";
        echo "───────────────────────────────────────────────\n";
        
        // Group warnings by type
        $groupedWarnings = [];
        foreach ($warnings as $warning) {
            $groupedWarnings[$warning['type']][] = $warning;
        }
        
        foreach ($groupedWarnings as $type => $typeWarnings) {
            echo "\n📌 {$type} (" . count($typeWarnings) . " files)\n";
            foreach (array_slice($typeWarnings, 0, 5) as $warning) {
                echo "   • {$warning['file']}\n";
                if (isset($warning['details'])) {
                    echo "     {$warning['details']}\n";
                }
            }
            if (count($typeWarnings) > 5) {
                echo "   ... and " . (count($typeWarnings) - 5) . " more files\n";
            }
        }
        echo "\n";
    }
}

echo "═══════════════════════════════════════════════════\n";
echo "  Scan completed: " . date('Y-m-d H:i:s') . "\n";
echo "═══════════════════════════════════════════════════\n";
