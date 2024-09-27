<?php

namespace App\Console\Commands;

use App\Models\CLUB\TrainerTime;
use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Services\Api\NotificationService;
use Carbon\Carbon;

class SendAppointmentReminder extends Command
{
    protected $signature = 'send:appointment-reminder';
    protected $description = 'Send appointment reminder notifications to users';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
        $appointments = TrainerTime::whereDate('date', $tomorrow)->get();

        foreach ($appointments as $appointment) {
            $user = $appointment->user;
            $title = 'Appointment Reminder';
            $message = 'You have an appointment scheduled for tomorrow at ' . $appointment->start_time;

            $this->notificationService->send($user, $title, $message, 'reminder');
        }

        $this->info('Appointment reminders sent successfully.');
    }
}
