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
        $bookTitle   = $this->sellerBook->officialBook->title ?? 'votre livre';
        $phone       = $notifiable->phone ?? 'votre numéro enregistré';

        $mail = (new MailMessage)
            ->subject('Livre « ' . $bookTitle . ' » — ' . $statusLabel)
            ->greeting('Bonjour ' . $notifiable->name . ' !');

        switch ($this->sellerBook->status) {
            case \App\Enums\BookStatus::PickupPending:
                $mail->line('Bonne nouvelle ! Votre livre **« ' . $bookTitle . ' »** a été examiné et validé par notre équipe.')
                     ->line('**Prochaine étape — Collecte à domicile :**')
                     ->line('Notre livreur passera prochainement à votre adresse pour :')
                     ->line('&nbsp;&nbsp;• Récupérer le livre physiquement')
                     ->line('&nbsp;&nbsp;• Procéder au règlement')
                     ->line('Nous vous contacterons au **' . $phone . '** pour fixer le créneau de passage.')
                     ->line('Assurez-vous que le livre est en bon état et prêt à être remis.')
                     ->action('Voir mes livres', route('seller.books.index'));
                break;

            case \App\Enums\BookStatus::Approved:
                $mail->line('Votre livre **« ' . $bookTitle . ' »** est maintenant disponible à la vente sur BuyYourBook !')
                     ->line('Il est visible dans le catalogue et peut être commandé par les acheteurs.')
                     ->action('Voir mes livres', route('seller.books.index'));
                break;

            default: // Rejected
                $mail->line('Votre livre **« ' . $bookTitle . ' »** a été refusé.')
                     ->line('Motif : ' . ($this->sellerBook->rejection_reason ?? 'Non précisé'))
                     ->action('Modifier et resoumettre', route('seller.books.index'));
                break;
        }

        return $mail->line('Merci d\'utiliser BuyYourBook !');
    }
}
