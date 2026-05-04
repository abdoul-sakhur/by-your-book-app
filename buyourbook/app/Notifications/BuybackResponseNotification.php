<?php

namespace App\Notifications;

use App\Models\SellerBook;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuybackResponseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public SellerBook $sellerBook,
        public string $action
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookTitle    = $this->sellerBook->officialBook->title ?? 'un livre';
        $sellerName   = $this->sellerBook->seller->name ?? 'Le vendeur';

        $subjects = [
            'accept'  => '✅ Offre acceptée — « ' . $bookTitle . ' »',
            'reject'  => '❌ Offre refusée — « ' . $bookTitle . ' »',
            'counter' => '🔄 Contre-offre reçue — « ' . $bookTitle . ' »',
        ];

        $mail = (new MailMessage)
            ->subject($subjects[$this->action] ?? 'Réponse de rachat — « ' . $bookTitle . ' »')
            ->greeting('Bonjour Admin !');

        match ($this->action) {
            'accept' => $mail
                ->line($sellerName . ' a **accepté** votre offre de rachat pour « ' . $bookTitle . ' ».')
                ->line('Vous pouvez maintenant marquer le paiement comme effectué.'),

            'reject' => $mail
                ->line($sellerName . ' a **refusé** votre offre de rachat pour « ' . $bookTitle . ' ».')
                ->line('Vous pouvez proposer une nouvelle offre depuis la fiche du livre.'),

            'counter' => $mail
                ->line($sellerName . ' a envoyé une **contre-offre** de **' . number_format($this->sellerBook->counter_price, 0, ',', ' ') . ' FCFA** pour « ' . $bookTitle . ' ».')
                ->line('Vous pouvez accepter ou proposer un nouveau prix depuis la fiche du livre.'),
        };

        return $mail
            ->action('Voir la fiche du livre', route('admin.seller-books.show', $this->sellerBook))
            ->line('Merci d\'utiliser BuyYourBook !');
    }
}
