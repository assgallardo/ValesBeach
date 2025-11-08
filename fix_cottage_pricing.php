<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cottage;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   FIXING COTTAGE PRICING                               ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check current pricing
echo "📊 Current Cottage Pricing:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$cottages = Cottage::all();
foreach ($cottages as $cottage) {
    echo "{$cottage->name}: Day=₱{$cottage->price_per_day}, Hour=₱{$cottage->price_per_hour}\n";
}

echo "\n";
echo "🔧 Fixing Pricing Based on Cottage Type...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

DB::beginTransaction();

try {
    // Update Umbrella Cottages
    $umbrellaUpdated = DB::table('cottages')
        ->where('name', 'like', 'Umbrella Cottage%')
        ->update([
            'price_per_day' => 350.00,
            'price_per_hour' => 50.00,
            'weekend_rate' => 400.00,
            'holiday_rate' => 450.00,
        ]);
    
    echo "✅ Updated {$umbrellaUpdated} Umbrella Cottages\n";
    echo "   - Day Rate: ₱350.00\n";
    echo "   - Hourly Rate: ₱50.00\n";
    echo "   - Night Rate: ₱400.00 (weekend_rate)\n\n";
    
    // Update Bahay Kubo
    $bahayKuboUpdated = DB::table('cottages')
        ->where('name', 'like', 'Bahay Kubo%')
        ->update([
            'price_per_day' => 200.00,
            'price_per_hour' => 30.00,
            'weekend_rate' => 250.00,
            'holiday_rate' => 300.00,
        ]);
    
    echo "✅ Updated {$bahayKuboUpdated} Bahay Kubo Cottages\n";
    echo "   - Day Rate: ₱200.00\n";
    echo "   - Hourly Rate: ₱30.00\n";
    echo "   - Night Rate: ₱250.00 (weekend_rate)\n\n";
    
    DB::commit();
    
    // Display updated pricing
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 Updated Cottage Pricing:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $cottages = Cottage::all();
    foreach ($cottages as $cottage) {
        echo "{$cottage->name}: Day=₱{$cottage->price_per_day}, Hour=₱{$cottage->price_per_hour}\n";
    }
    
    echo "\n✅ PRICING UPDATED SUCCESSFULLY!\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
