<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeResolueNotification extends Notification
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
            ->subject("Demande #{$this->demande->id} résolue")
            ->greeting('Bonjour,')
            ->line("La demande #{$this->demande->id} a été déclarée résolue par le technicien.")
            ->action('Confirmer la résolution', env('FRONTEND_URL', 'http://localhost:5174')."/demandes/{$this->demande->id}")
            ->line('Merci de confirmer si le problème est bien résolu.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'demande_id' => $this->demande->id,
            'description' => $this->demande->description,
            'technicien' => $this->demande->technicien->name,
            'message' => "Votre demande #{$this->demande->id} a été déclarée résolue. Merci de confirmer.",
        ];
    }
}
