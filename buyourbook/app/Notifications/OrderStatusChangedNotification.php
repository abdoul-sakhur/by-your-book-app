<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $newStatus) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->order->status->label();

        return (new MailMessage)
            ->subject('Commande #' . $this->order->id . ' — ' . $statusLabel)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Le statut de votre commande #' . $this->order->id . ' a été mis à jour.')
            ->line('Nouveau statut : **' . $statusLabel . '**')
            ->action('Voir ma commande', route('orders.show', $this->order))
            ->line('Merci pour votre confiance !');
    }
}
