<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification envoyée au vendeur lorsqu'une commande confirmée contient ses livres.
 * Il doit préparer ses exemplaires pour la livraison.
 */
class SellerNewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemCount = $this->order->items
            ->filter(fn ($item) => $item->sellerBook->user_id === $notifiable->id)
            ->sum('quantity');

        return (new MailMessage)
            ->subject('Nouvelle vente confirmée — Commande #' . $this->order->id)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line("Une commande contenant **{$itemCount} exemplaire(s)** de vos livres a été confirmée.")
            ->line('**Commande #' . $this->order->id . '** — passée le ' . $this->order->created_at->format('d/m/Y à H:i'))
            ->line('Merci de préparer vos exemplaires et de les marquer comme « prêts » depuis votre espace vendeur.')
            ->action('Voir la commande', route('seller.orders.show', $this->order))
            ->line('Merci pour votre confiance !');
    }
}
