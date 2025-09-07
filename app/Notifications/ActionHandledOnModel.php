<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ActionHandledOnModel extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Authenticatable|User $causer,
        public array $subjectData,
        public string $eventType,
        public array $routingData,
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
            'causer' => $this->causer->name,
            'message' => Str::ucfirst(__($this->eventType)) . " {$this->subjectData['type']} <{$this->subjectData['name']}>.",
            'photoUrl' => $this->causer->profile_photo_url,
            'timestamp' => $this->subjectData['timestamp'],
            'url' => $this->urlResolver(),
        ];
    }

    private function urlResolver(): string
    {
        $url = route("{$this->routingData['routeName']}.index");

        if (!($this->eventType === 'deleted' || $this->eventType === 'f_deleted'))
        {
            $url = route("{$this->routingData['routeName']}.show", ["{$this->routingData['routeParam']}" => $this->subjectData['id']]);
        }

        return $url;
    }
}
