<?php

require __DIR__.'/vendor/autoload.php';

use Carbon\Carbon;

$checkIn = Carbon::parse('2025-11-13 00:00:00')->startOfDay();
$checkOut = Carbon::parse('2025-11-13 00:00:00')->startOfDay();

$nights = $checkIn->diffInDays($checkOut);

echo "Check-in: " . $checkIn->toDateTimeString() . "\n";
echo "Check-out: " . $checkOut->toDateTimeString() . "\n";
echo "diffInDays result: "; var_dump($nights);
echo "Type: " . gettype($nights) . "\n";
echo "Value: {$nights}\n";
echo "Is === 0? " . ($nights === 0 ? 'YES' : 'NO') . "\n";
echo "Is == 0? " . ($nights == 0 ? 'YES' : 'NO') . "\n";
echo "Ternary with ===: " . (($nights === 0) ? 1 : $nights) . "\n";
echo "Ternary with ==: " . (($nights == 0) ? 1 : $nights) . "\n";
