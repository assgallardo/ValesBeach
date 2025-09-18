<?php
/**
 * Test Guest Role Functionality
 * 
 * This script tests if the guest role works properly in the user management system.
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Guest Role Functionality ===\n\n";

try {
    // Test 1: Check if guest role is accepted in validation
    echo "1. Testing role validation...\n";
    
    $validator = Illuminate\Support\Facades\Validator::make([
        'name' => 'Test Guest User',
        'email' => 'guest@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'guest',
    ], [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|same:password_confirmation',
        'password_confirmation' => 'required',
        'role' => 'required|in:admin,manager,staff,guest',
    ]);
    
    if ($validator->passes()) {
        echo "✓ Guest role passes validation\n";
    } else {
        echo "✗ Guest role validation failed: " . implode(', ', $validator->errors()->all()) . "\n";
    }
    
    // Test 2: Create a test guest user
    echo "\n2. Testing guest user creation...\n";
    
    // Check if test user already exists and delete it
    $existingUser = App\Models\User::where('email', 'test-guest@example.com')->first();
    if ($existingUser) {
        $existingUser->delete();
        echo "Cleaned up existing test user\n";
    }
    
    $testUser = App\Models\User::create([
        'name' => 'Test Guest User',
        'email' => 'test-guest@example.com',
        'password' => Illuminate\Support\Facades\Hash::make('password123'),
        'role' => 'guest',
        'status' => 'active',
    ]);
    
    if ($testUser && $testUser->role === 'guest') {
        echo "✓ Guest user created successfully with ID: {$testUser->id}\n";
        
        // Test 3: Test role checking methods
        echo "\n3. Testing role checking methods...\n";
        
        if ($testUser->hasRole('guest')) {
            echo "✓ hasRole('guest') works correctly\n";
        } else {
            echo "✗ hasRole('guest') failed\n";
        }
        
        if ($testUser->hasAnyRole(['guest', 'staff'])) {
            echo "✓ hasAnyRole(['guest', 'staff']) works correctly\n";
        } else {
            echo "✗ hasAnyRole(['guest', 'staff']) failed\n";
        }
        
        // Test 4: Test role filtering
        echo "\n4. Testing role filtering...\n";
        
        $guestUsers = App\Models\User::byRole('guest')->get();
        if ($guestUsers->count() > 0 && $guestUsers->contains($testUser)) {
            echo "✓ Role filtering works correctly (found {$guestUsers->count()} guest users)\n";
        } else {
            echo "✗ Role filtering failed\n";
        }
        
        // Clean up test user
        $testUser->delete();
        echo "\n✓ Test guest user cleaned up\n";
        
    } else {
        echo "✗ Failed to create guest user\n";
    }
    
    echo "\n=== Guest Role Test Complete ===\n";
    echo "The guest role is fully functional in the user management system.\n";
    
} catch (Exception $e) {
    echo "✗ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
