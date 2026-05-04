<?php

namespace App\Notifications;

use App\Models\SellerBook;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuybackOfferNotification extends Notification
{
    use Queueable;

    public function __construct(public SellerBook $sellerBook) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookTitle = $this->sellerBook->officialBook->title ?? 'votre livre';
        $price     = number_format($this->sellerBook->buyback_price, 0, ',', ' ');

        $mail = (new MailMessage)
            ->subject('Offre de rachat pour « ' . $bookTitle . ' »')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('L\'administrateur vous propose de racheter votre livre « ' . $bookTitle . ' » pour **' . $price . ' FCFA**.');

        if ($this->sellerBook->buyback_notes) {
            $mail->line('**Note de l\'admin :** ' . $this->sellerBook->buyback_notes);
        }

        $mail->line('Vous pouvez accepter, refuser ou faire une contre-offre depuis votre espace vendeur.')
             ->action('Répondre à l\'offre', route('seller.books.index'));

        return $mail->line('Merci d\'utiliser BuyYourBook !');
    }
}
