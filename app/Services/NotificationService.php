<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\LabResultNotification;
use App\Notifications\NewLabOrderNotification;
use App\Notifications\NewPrescriptionOrder;
use App\Notifications\NewPatientAdmissionNotification;
use App\Notifications\AppointmentReminderNotification;
use App\Models\LabResult;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Admission;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class NotificationService
{
    /**
     * Send a notification to a single user.
     *
     * @param  User  $user
     * @param  string  $type
     * @param  array  $data
     * @return void
     */
    public function sendNotification(User $user, string $type, array $data)
    {
        // Create notification in database
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'data' => $data,
            'is_read' => false,
        ]);

        // Broadcast to Reverb if enabled
        $this->broadcastNotification($user, $notification);
    }

    /**
     * Send notifications to multiple users.
     *
     * @param  array  $users
     * @param  string  $type
     * @param  array  $data
     * @return void
     */
    public function sendNotifications(array $users, string $type, array $data)
    {
        foreach ($users as $user) {
            $this->sendNotification($user, $type, $data);
        }
    }

    /**
     * Send a lab result notification to a doctor.
     *
     * @param  LabResult  $labResult
     * @return void
     */
    public function sendLabResultNotification(LabResult $labResult)
    {
        $doctor = User::find($labResult->doctor_id);

        if ($doctor) {
            $doctor->notify(new LabResultNotification($labResult));
        }
    }

    /**
     * Send a new lab order notification to lab technicians.
     *
     * @param  MedicalRecord  $medicalRecord
     * @return void
     */
   /**
     * Send notification to Lab Technicians
     */
    public function sendNewLabOrderNotification(MedicalRecord $record)
    {
        // 1. Find users with role 'lab-technician'
        // Make sure your Spatie roles are seeded correctly as 'lab-technician'
        $labTechs = User::where('role', 'lab-technician')->get();

        // 2. Debugging Log (Check your storage/logs/laravel.log)
        if ($labTechs->isEmpty()) {
            Log::warning("New Lab Order created for Record #{$record->id}, but NO users with role 'lab-technician' were found.");
            return;
        }

        Log::info("Dispatching Lab Order Notification to " . $labTechs->count() . " technicians.");

        // 3. Send the notification
        LaravelNotification::send($labTechs, new NewLabOrderNotification($record));
    }

    /**
     * Send a new prescription order notification to pharmacists.
     *
     * @param  Prescription  $prescription
     * @return void
     */
    public function sendNewPrescriptionNotification(Prescription $prescription)
    {
        $pharmacists = User::where('role', 'pharmacist')->get();

        foreach ($pharmacists as $pharmacist) {
            $pharmacist->notify(new NewPrescriptionOrder($prescription));
        }
    }

    /**
     * Send a new patient admission notification to nurses.
     *
     * @param  Admission  $admission
     * @return void
     */
    public function sendNewPatientAdmissionNotification(Admission $admission)
    {
        $nurses = User::where('role', 'nurse')->get();

        foreach ($nurses as $nurse) {
            $nurse->notify(new NewPatientAdmissionNotification($admission));
        }
    }

    /**
     * Send an appointment reminder notification to a doctor.
     *
     * @param  Appointment  $appointment
     * @return void
     */
    public function sendAppointmentReminderNotification(Appointment $appointment)
    {
        $doctor = User::find($appointment->doctor_id);

        if ($doctor) {
            $doctor->notify(new AppointmentReminderNotification($appointment));
        }
    }

    /**
     * Broadcast a notification using Reverb.
     *
     * @param  User  $user
     * @param  Notification  $notification
     * @return void
     */
    protected function broadcastNotification(User $user, Notification $notification)
    {
        // Reverb broadcasting is handled by Laravel's notification system
        // through the broadcast channel
    }

    /**
     * Get all notifications for a user.
     *
     * @param  User  $user
     * @param  bool  $includeRead
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserNotifications(User $user, bool $includeRead = true)
    {
        $query = Notification::where('user_id', $user->id);

        if (!$includeRead) {
            $query->where('is_read', false);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param  User  $user
     * @return void
     */
    public function markAllNotificationsAsRead(User $user)
    {
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  Notification  $notification
     * @return void
     */
    public function markNotificationAsRead(Notification $notification)
    {
        $notification->markAsRead();
    }

    /**
     * Delete a notification.
     *
     * @param  Notification  $notification
     * @return void
     */
    public function deleteNotification(Notification $notification)
    {
        $notification->delete();
    }

    /**
     * Get unread notifications count for a user.
     *
     * @param  User  $user
     * @return int
     */
    public function getUnreadNotificationsCount(User $user)
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Send a general notification to a user.
     *
     * @param  User  $user
     * @param  string  $message
     * @param  array  $data
     * @return void
     */
    public function sendGeneralNotification(User $user, string $message, array $data = [])
    {
        $this->sendNotification($user, 'general', array_merge(['message' => $message], $data));
    }

    /**
     * Send a notification to users with specific roles.
     *
     * @param  array  $roles
     * @param  string  $type
     * @param  array  $data
     * @return void
     */
    public function sendNotificationToRoles(array $roles, string $type, array $data)
    {
        $users = User::whereIn('role', $roles)->get();
        $this->sendNotifications($users, $type, $data);
    }
}
