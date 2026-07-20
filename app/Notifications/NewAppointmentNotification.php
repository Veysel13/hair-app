<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    use Queueable;

    protected Appointment $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $serviceNames = $this->appointment->services->map(function ($appointmentService) {
            return $appointmentService->service ? $appointmentService->service->name : '';
        })->filter()->implode(', ');

        if (empty($serviceNames) && $this->appointment->service) {
            $serviceNames = $this->appointment->service->name;
        }

        return [
            'appointment_id' => $this->appointment->id,
            'customer_name'  => $this->appointment->customer->name,
            'service_names'  => $serviceNames,
            'staff_name'     => $this->appointment->staff->name,
            'date'           => $this->appointment->appointment_date->format('Y-m-d'),
            'time'           => substr($this->appointment->start_time, 0, 5),
            'total_price'    => $this->appointment->total_price,
            'message'        => 'Yeni randevu talebi: ' . $this->appointment->customer->name . ' - ' . $serviceNames . ' (' . substr($this->appointment->start_time, 0, 5) . ')',
        ];
    }
}
