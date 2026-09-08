<?php

namespace App\Notifications\Foyers;

use App\Models\FoyerInvitation as FoyerInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FoyerInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public FoyerInvitationModel $invitation)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $foyer = $this->invitation->foyer;
        $inviter = $this->invitation->inviter;

        return (new MailMessage)
            ->subject(__("You've been invited to join :foyerName", ['foyerName' => $foyer->name]))
            ->line(__(':inviterName has invited you to join the :foyerName foyer.', [
                'inviterName' => $inviter->name,
                'foyerName' => $foyer->name,
            ]))
            ->line(__('Log in and visit your dashboard to accept or decline this invitation.'))
            ->action(
                __('Log in'),
                route('login', ['invitation' => $this->invitation->code]),
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'foyer_id' => $this->invitation->foyer_id,
            'foyer_name' => $this->invitation->foyer->name,
            'role' => $this->invitation->role->value,
        ];
    }
}
