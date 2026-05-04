<?php

use Illuminate\Support\Facades\Broadcast;

// 1. Private User Channel (Keep this for personal alerts)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 2. Pharmacy Department Channel
Broadcast::channel('pharmacy.orders', function ($user) {
    // Only allow users with the 'pharmacist' role
    return $user->role === 'pharmacist';
});

// 3. Lab Department Channel
Broadcast::channel('lab.requests', function ($user) {
    // Only allow users with the 'lab-technician' role
    return $user->role === 'lab-technician';
});

// 4. Nurse Admissions Channel
Broadcast::channel('nurse.admissions', function ($user) {
    // Only allow users with the 'nurse' role
    return $user->role === 'nurse';
});
