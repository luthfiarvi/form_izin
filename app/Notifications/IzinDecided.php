<?php

namespace App\Notifications;

use App\Models\FormIzin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class IzinDecided extends Notification
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
        $status = $this->form->approved_at
            ? 'Disetujui'
            : ($this->form->rejected_at ? 'Ditolak' : 'Menunggu Keputusan');

        $tanggal = optional($this->form->date)->format('d-m-Y') ?: (string) $this->form->date;

        $url = route('izin.view', $this->form);

        return (new MailMessage)
            ->subject('Status Pengajuan Izin Anda - '.$status)
            ->greeting('Yth. '.$notifiable->name)
            ->line('Pengajuan izin Anda di Ibnu Hajar Boarding School telah berstatus: '.$status.'.')
            ->line('Detail pengajuan:')
            ->line('• Tanggal      : '.$tanggal)
            ->line('• Jenis Izin   : '.$this->form->izin_type)
            ->line('• Jam Masuk    : '.$this->form->in_time)
            ->line('• Jam Keluar   : '.$this->form->out_time)
            ->line('• Keperluan    : '.$this->form->purpose)
            ->when($this->form->decidedBy, function (MailMessage $message) {
                return $message->line('Diproses oleh: '.$this->form->decidedBy?->name);
            })
            ->action('Lihat Detail Izin', $url)
            ->line('Email ini dikirim otomatis oleh Sistem Informasi Perizinan Ibnu Hajar Boarding School.')
            ->line('Mohon tidak membalas email ini.')
            ->line('Terima kasih telah menggunakan sistem perizinan kami.');
    }

    public function toTwilio(object $notifiable): TwilioSmsMessage
    {
        $status = $this->form->approved_at ? 'APPROVED' : ($this->form->rejected_at ? 'REJECTED' : 'PENDING');
        return (new TwilioSmsMessage())
            ->content('Form Izin #'.$this->form->id.' status: '.$status.'.');
    }
}
