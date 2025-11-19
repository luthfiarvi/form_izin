<?php

namespace App\Notifications;

use App\Models\FormIzin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class IzinSubmitted extends Notification
{
    use Queueable;

    public function __construct(public readonly FormIzin $form)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        if (config('services.twilio.account_sid') && method_exists($notifiable, 'routeNotificationForTwilio') && $notifiable->routeNotificationForTwilio($this)) {
            $channels[] = TwilioChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.izin.show', $this->form);

        return (new MailMessage)
            ->subject('Form Izin Submitted')
            ->greeting('Hi Admin')
            ->line('A new Form Izin has been submitted by '.$this->form->user?->name)
            ->action('View Form', $url)
            ->line('Form ID: '.$this->form->id);
    }

    public function toTwilio(object $notifiable): TwilioSmsMessage
    {
        $url = route('admin.izin.show', $this->form);
        return (new TwilioSmsMessage())
            ->content('Form Izin submitted by '.$this->form->user?->name.' (ID '.$this->form->id.'). '.$url);
    }
}

