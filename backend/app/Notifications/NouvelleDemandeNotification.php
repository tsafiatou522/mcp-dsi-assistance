<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeNotification extends Notification
{
    protected Demande $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nouvelle demande #{$this->demande->id}")
            ->greeting('Bonjour,')
            ->line("Une nouvelle demande vous a été attribuée : {$this->demande->description}")
            ->line("Priorité : {$this->demande->priorite}")
            ->action('Voir la demande', env('FRONTEND_URL', 'http://localhost:5174')."/demandes/{$this->demande->id}")
            ->line('Merci de prendre connaissance de cette demande.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'demande_id' => $this->demande->id,
            'description' => $this->demande->description,
            'direction' => $this->demande->direction->nom,
            'bureau' => $this->demande->bureau->nom,
            'categorie' => $this->demande->categorie->nom,
            'priorite' => $this->demande->priorite,
            'message' => "Nouvelle demande #{$this->demande->id} : {$this->demande->description}",
        ];
    }
}