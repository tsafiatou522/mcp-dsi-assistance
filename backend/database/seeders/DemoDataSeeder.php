<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Bureau;
use App\Models\Categorie;
use App\Models\Demande;
use App\Models\Direction;
use App\Models\Equipement;
use App\Models\Probleme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $direction = Direction::updateOrCreate(['nom' => 'Direction des services numériques'], ['description' => 'Support informatique et services numériques']);
        $directionRh = Direction::updateOrCreate(['nom' => 'Direction des ressources humaines'], ['description' => 'Administration du personnel']);
        $bureau = Bureau::updateOrCreate(['nom' => 'Bureau informatique central', 'direction_id' => $direction->id]);
        $bureauRh = Bureau::updateOrCreate(['nom' => 'Bureau RH', 'direction_id' => $directionRh->id]);

        $reseau = Categorie::updateOrCreate(['nom' => 'Réseau'], ['description' => 'Connexion et accès réseau']);
        $materiel = Categorie::updateOrCreate(['nom' => 'Matériel'], ['description' => 'Ordinateurs et périphériques']);
        $logiciel = Categorie::updateOrCreate(['nom' => 'Logiciel'], ['description' => 'Applications et logiciels']);

        $password = Hash::make('password');
        $admin = $this->utilisateur('admin.demo@dsi.local', 'Administrateur Demo', 'admin', $direction, $password);
        $responsable = $this->utilisateur('responsable.demo@dsi.local', 'Responsable Demo', 'responsable', $direction, $password);
        $agent = $this->utilisateur('agent.demo@dsi.local', 'Agent Demo', 'agent', $directionRh, $password);
        $technicien = $this->utilisateur('technicien.demo@dsi.local', 'Technicien Demo', 'technicien', $direction, $password);

        $ordinateur = Equipement::updateOrCreate(
            ['numero_serie' => 'DEMO-DSI-001'],
            ['nom' => 'Poste accueil DSI', 'type' => 'ordinateur', 'marque' => 'Dell', 'modele' => 'Latitude 5440', 'direction_id' => $direction->id, 'bureau_id' => $bureau->id, 'etat' => 'fonctionnel', 'date_acquisition' => now()->subMonths(8)]
        );

        $this->demande('DEMO-001', $agent, $directionRh, $bureauRh, $materiel, 'Ordinateur qui ne démarre plus', 'urgente', 'nouvelle', null, null);
        $this->demande('DEMO-002', $agent, $directionRh, $bureauRh, $logiciel, 'Installation du logiciel de paie', 'normale', 'prise_en_charge', $technicien, null);
        $this->demande('DEMO-003', $agent, $direction, $bureau, $reseau, 'Connexion Wi-Fi instable au bureau central', 'haute', 'en_intervention', $technicien, null);
        $this->demande('DEMO-004', $agent, $direction, $bureau, $materiel, 'Remplacement du clavier', 'basse', 'cloturee', $technicien, $ordinateur);

        Article::firstOrCreate(['titre' => 'Rétablir une connexion Wi-Fi'], ['user_id' => $responsable->id, 'categorie' => 'Réseau', 'contenu' => 'Vérifier le câble réseau, redémarrer le point d’accès et contrôler les voyants. Si le problème persiste, créer une demande urgente.', 'publie' => true]);
        Probleme::firstOrCreate(['titre' => 'Instabilité Wi-Fi récurrente'], ['user_id' => $responsable->id, 'description' => 'Plusieurs bureaux signalent des coupures Wi-Fi.', 'priorite' => 'haute', 'statut' => 'en_analyse']);

        $this->command?->info('Données de démonstration créées. Mot de passe des comptes demo : password');
    }

    private function utilisateur(string $email, string $name, string $role, Direction $direction, string $password): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password, 'role' => $role, 'direction_id' => $direction->id, 'actif' => true, 'email_verified_at' => now()]
        );
    }

    private function demande(string $reference, User $agent, Direction $direction, Bureau $bureau, Categorie $categorie, string $description, string $priorite, string $statut, ?User $technicien, ?Equipement $equipement): Demande
    {
        return Demande::updateOrCreate(
            ['description' => "[$reference] $description"],
            ['agent_id' => $agent->id, 'technicien_id' => $technicien?->id, 'direction_id' => $direction->id, 'bureau_id' => $bureau->id, 'category_id' => $categorie->id, 'equipement_id' => $equipement?->id, 'priorite' => $priorite, 'statut' => $statut, 'date_echeance' => now()->addHours($priorite === 'urgente' ? 4 : ($priorite === 'haute' ? 8 : 24))]
        );
    }
}