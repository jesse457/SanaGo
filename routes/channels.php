<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Reverb authenticates private channels through these callbacks. Every user
| may only listen on their own notification channel; landlord users are
| additionally allowed on the platform channel.
|
*/

Broadcast::channel('App.Models.User.{userId}', function ($user, string $userId) {
    return (string) $user->getKey() === (string) $userId;
});

Broadcast::channel('platform.landlord', function ($user) {
    return $user->can('view-super-admin');
});

Broadcast::channel('tenant.{tenantId}', function ($user, string $tenantId) {
    return tenancy()->tenant !== null
        && (string) tenancy()->tenant->getTenantKey() === (string) $tenantId;
});
