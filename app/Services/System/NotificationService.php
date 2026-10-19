<?php

namespace App\Services\System;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     */
    public function notifyUser(User $user, string $message, string $category = 'general', ?string $link = null, string $typeLabel = 'info'): void
    {
        $user->notify(new SystemNotification($message, $category, $link, $typeLabel));
    }

    /**
     * Send notifications to all users who have a specific permission.
     */
    public function notifyByPermission(string $permission, string $message, string $category = 'general', ?string $link = null, string $typeLabel = 'info'): void
    {
        $users = User::permission($permission)->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new SystemNotification($message, $category, $link, $typeLabel));
        }
    }

    /**
     * Notify all doctors about a new event.
     */
    public function notifyDoctors(string $message, string $category = 'clinical', ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-consultations', $message, $category, $link, $typeLabel);
    }

    /**
     * Notify all lab technicians about a new lab event.
     */
    public function notifyLabTechs(string $message, ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-lab-results', $message, 'lab', $link, $typeLabel);
    }

    /**
     * Notify pharmacy staff.
     */
    public function notifyPharmacy(string $message, ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-drugs', $message, 'pharmacy', $link, $typeLabel);
    }

    /**
     * Notify reception staff.
     */
    public function notifyReception(string $message, string $category = 'clinical', ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-appointments', $message, 'general', $link, $typeLabel);
    }

    /**
     * Notify finance/admin staff.
     */
    public function notifyFinance(string $message, ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-finance', $message, 'general', $link, $typeLabel);
    }
}
