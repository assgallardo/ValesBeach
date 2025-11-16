<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Payment;

$user = User::where('email', 'rico.mendoza@gmail.com')->first();

if ($user) {
    echo "Found user: {$user->name}\n";
    
    // Get all payments for this user
    $payments = Payment::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "Total payments: {$payments->count()}\n\n";
    
    foreach ($payments as $payment) {
        echo "ID: {$payment->id}, Amount: ₱{$payment->amount}, Status: {$payment->status}, Ref: {$payment->payment_reference}\n";
    }
    
    if ($payments->count() > 0) {
        echo "\nEnter the payment ID you want to delete (or 'all' to delete all): ";
    }
} else {
    echo "User not found\n";
}

