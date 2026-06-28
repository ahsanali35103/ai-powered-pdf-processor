<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SignupVerificationNotification extends Notification
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
        $verifyUrl = url('/api/auth/verify-signup') . '?token=' . $this->token . '&email=' . urlencode($this->user->email);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->view('emails.signup_verification', [
                'name' => $this->user->name,
                'verification_code' => $this->token,
                'verification_url' => $verifyUrl,
            ]);
    }
}
