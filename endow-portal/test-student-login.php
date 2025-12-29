<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

$user = User::where('email', 'student@endowglobal.com')->first();

if ($user) {
    echo "User found: " . $user->name . " (" . $user->email . ")\n";
    echo "Has Student Role: " . ($user->hasRole('Student') ? 'Yes' : 'No') . "\n";
    echo "All Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Password Check: " . (Hash::check('password', $user->password) ? 'Correct' : 'Wrong') . "\n";
    echo "User Status: " . $user->status . "\n";
    echo "\nTesting Auth::attempt...\n";
    
    $credentials = [
        'email' => 'student@endowglobal.com',
        'password' => 'password'
    ];
    
    if (Auth::attempt($credentials)) {
        echo "Auth::attempt SUCCESSFUL\n";
        echo "Authenticated user: " . Auth::user()->name . "\n";
    } else {
        echo "Auth::attempt FAILED\n";
    }
} else {
    echo "User not found!\n";
}
