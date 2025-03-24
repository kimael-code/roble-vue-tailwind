<?php

namespace App\Notifications\Security;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Authenticatable|User $causer,
        public array  $subjectData,
        public string $eventType,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'causer'    => $this->causer->name,
            'message'   => __($this->eventType)." el permiso {$this->subjectData['name']}.",
            'photoUrl'  => $this->causer->profile_photo_url,
            'timestamp' => $this->subjectData['timestamp'],
            'url'       => $this->urlResolver(),
        ];
    }

    private function urlResolver(): string
    {
        $url = route('permissions.index');

        if (!($this->eventType === 'deleted' || $this->eventType === 'f_deleted'))
        {
            $url = route('permissions.show', ['permission' => $this->subjectData['id']]);
        }

        return $url;
    }
}
