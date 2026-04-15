<?php

namespace App\Notifications;

use App\Models\SellerBook;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerBookStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public SellerBook $sellerBook) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->sellerBook->status->label();
        $bookTitle = $this->sellerBook->officialBook->title ?? 'votre livre';

        $mail = (new MailMessage)
            ->subject('Livre « ' . $bookTitle . ' » — ' . $statusLabel)
            ->greeting('Bonjour ' . $notifiable->name . ' !');

        if ($this->sellerBook->status->value === 'approved') {
            $mail->line('Bonne nouvelle ! Votre livre « ' . $bookTitle . ' » a été approuvé et est maintenant visible dans le catalogue.')
                 ->action('Voir mes livres', route('seller.books.index'));
        } else {
            $mail->line('Votre livre « ' . $bookTitle . ' » a été refusé.')
                 ->line('Motif : ' . ($this->sellerBook->rejection_reason ?? 'Non précisé'))
                 ->action('Modifier et resoumettre', route('seller.books.index'));
        }

        return $mail->line('Merci d\'utiliser BuyYourBook !');
    }
}
