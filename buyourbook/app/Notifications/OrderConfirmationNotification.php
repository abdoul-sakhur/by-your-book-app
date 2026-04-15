<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmation de commande #' . $this->order->id)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Votre commande #' . $this->order->id . ' a bien été enregistrée.')
            ->line('Montant total : ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->action('Voir ma commande', route('orders.show', $this->order))
            ->line('Merci pour votre achat sur BuyYourBook !');
    }
}
