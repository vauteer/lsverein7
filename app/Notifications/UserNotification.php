<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $headline;
    private string $text;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $headline, string $text)
    {
        $this->headline = $headline;
        $this->text = $text;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $fromUser = currentUser();

        return (new MailMessage)
            ->markdown('emails.user', ['senderName' => $fromUser->name, 'clubName' => currentClub()->name])
            ->subject('Benachrichtigung von ' . config('app.name'))
            ->line($this->headline)
            ->line($this->text)
            ->action('Login', url('/'))
            ->line(__('thank_you_for_using_our_application'))
//            ->replyTo($fromUser->email, $fromUser->name)
            ->from($fromUser->email, $fromUser->name);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
