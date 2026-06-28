<?php

/**
 * Database Debug Script for Service List API
 * Checks actual database content to identify why API returns empty results
 */

require_once __DIR__ . '/ai-pdf-laravel/vendor/autoload.php';

$app = require_once __DIR__ . '/ai-pdf-laravel/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DATABASE DEBUG FOR SERVICE LIST API ===\n\n";

try {
    // Test MongoDB connection
    $db = app('db');
    echo "✅ Database connection established\n";
    echo "   - Default connection: " . config('database.default') . "\n\n";

    // Check Service model
    $service = new App\Models\Service();
    echo "✅ Service model loaded\n";
    echo "   - Connection: " . $service->getConnectionName() . "\n";
    echo "   - Collection: " . $service->getTable() . "\n\n";

    // Get all services from database
    $allServices = App\Models\Service::limit(10)->get();
    echo "📊 SAMPLE SERVICES FROM DATABASE:\n";
    echo "   - Total sample services: " . $allServices->count() . "\n\n";

    if ($allServices->count() > 0) {
        foreach ($allServices as $service) {
            echo "   Service ID: " . $service->_id . "\n";
            echo "   - User ID: " . ($service->user_id ?? 'NULL') . "\n";
            echo "   - Organization ID: " . ($service->organization_id ?? 'NULL') . "\n";
            echo "   - Type: " . ($service->type ?? 'NULL') . "\n";
            echo "   - Status: " . ($service->status ?? 'NULL') . "\n";
            echo "   - Created At: " . ($service->created_at ?? 'NULL') . "\n";
            echo "   ---\n";
        }
    } else {
        echo "   ❌ NO SERVICES FOUND IN DATABASE\n\n";
    }

    // Check users
    $allUsers = App\Models\User::limit(5)->get();
    echo "👥 SAMPLE USERS FROM DATABASE:\n";
    echo "   - Total sample users: " . $allUsers->count() . "\n\n";

    if ($allUsers->count() > 0) {
        foreach ($allUsers as $user) {
            echo "   User ID: " . $user->id . "\n";
            echo "   - Email: " . ($user->email ?? 'NULL') . "\n";
            echo "   - Organization ID: " . ($user->organization_id ?? 'NULL') . "\n";
            echo "   - FCM Token: " . ($user->fcm_token ? 'YES' : 'NO') . "\n";
            echo "   - Is Active: " . ($user->is_active ? 'YES' : 'NO') . "\n";
            echo "   ---\n";
        }
    }

    // Test specific user query (the one from your teammate's response)
    $testUserId = '019dd39d-a92c-728b-a7ea-1d79f4e10fb9';
    $testOrgId = '019dd39d-a84e-72cf-9879-c9c8dc4f1988';
    
    echo "\n🔍 TESTING SPECIFIC QUERY FROM TEAMMATE:\n";
    echo "   - User ID: " . $testUserId . "\n";
    echo "   - Organization ID: " . $testOrgId . "\n\n";

    // Query 1: User + Organization
    $servicesWithOrg = App\Models\Service::where('user_id', $testUserId)
        ->where('organization_id', $testOrgId)
        ->get();
    
    echo "   Query 1 (User + Org): " . $servicesWithOrg->count() . " results\n";

    // Query 2: User only
    $servicesUserOnly = App\Models\Service::where('user_id', $testUserId)->get();
    echo "   Query 2 (User only): " . $servicesUserOnly->count() . " results\n";

    // Query 3: Organization only
    $servicesOrgOnly = App\Models\Service::where('organization_id', $testOrgId)->get();
    echo "   Query 3 (Org only): " . $servicesOrgOnly->count() . " results\n";

    // If we found services for the user, show their actual organization IDs
    if ($servicesUserOnly->count() > 0) {
        echo "\n🎯 ACTUAL ORGANIZATION IDs FOR THIS USER:\n";
        foreach ($servicesUserOnly as $service) {
            echo "   - Service: " . $service->_id . "\n";
            echo "     Organization ID: " . ($service->organization_id ?? 'NULL') . "\n";
        }
    }

    // Check if the user exists
    $testUser = App\Models\User::find($testUserId);
    echo "\n👤 TEST USER EXISTENCE:\n";
    echo "   - User exists: " . ($testUser ? 'YES' : 'NO') . "\n";
    if ($testUser) {
        echo "   - User email: " . $testUser->email . "\n";
        echo "   - User organization: " . ($testUser->organization_id ?? 'NULL') . "\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
