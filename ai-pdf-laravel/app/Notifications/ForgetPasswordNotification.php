<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ForgetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $token
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url('/api/auth/reset-password') . '?token=' . $this->token . '&email=' . urlencode($this->user->email);

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->view('emails.forget_password', [
                'name' => $this->user->name,
                'reset_code' => $this->token,
                'reset_url' => $resetUrl,
            ]);
    }
}
