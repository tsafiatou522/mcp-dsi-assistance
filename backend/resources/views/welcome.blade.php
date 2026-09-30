<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Console de pilotage des opérations de la Direction des Systèmes d'Information.">
    <title>Console DSI | Pilotage des opérations</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ url('/') }}" aria-label="Accueil Console DSI"><span class="brand-mark">DS</span><span><strong>Console DSI</strong><small>Opérations & support</small></span></a>
        <nav class="main-nav" aria-label="Navigation principale">
            <p class="nav-label">Espace de travail</p>
            <a class="nav-item is-active" href="#apercu"><span class="nav-icon">⌂</span> Vue d'ensemble</a>
            <a class="nav-item" href="#demandes"><span class="nav-icon">▤</span> Demandes <span class="nav-count">12</span></a>
            <a class="nav-item" href="#interventions"><span class="nav-icon">＋</span> Interventions</a>
            <a class="nav-item" href="#equipements"><span class="nav-icon">▦</span> Équipements</a>
            <p class="nav-label nav-label-spaced">Organisation</p>
            <a class="nav-item" href="#equipes"><span class="nav-icon">◎</span> Équipes & présences</a>
            <a class="nav-item" href="#directions"><span class="nav-icon">⌘</span> Directions</a>
        </nav>
        <div class="sidebar-footer"><div class="service-status"><span class="status-dot"></span><span><strong>Services opérationnels</strong><small>Dernière vérification à l'instant</small></span></div><div class="profile-chip"><span class="avatar">AD</span><span><strong>Administrateur DSI</strong><small>Accès gestionnaire</small></span><span class="profile-more">•••</span></div></div>
    </aside>
    <main class="content" id="apercu">
        <header class="topbar"><div class="breadcrumb"><span>Console DSI</span><b>/</b><strong>Vue d'ensemble</strong></div><div class="top-actions"><button class="icon-button" aria-label="Rechercher">⌕</button><button class="icon-button has-alert" aria-label="Notifications">♢<i></i></button><span class="top-divider"></span><span class="top-date">Jeudi 24 septembre 2026</span></div></header>
        <section class="welcome-row"><div><p class="eyebrow">CENTRE DE PILOTAGE</p><h1>Bonjour, équipe DSI.</h1><p class="intro">Voici l'état des opérations et les sujets qui demandent votre attention aujourd'hui.</p></div><button class="primary-button" type="button"><span>＋</span> Nouvelle demande</button></section>
        <section class="metric-grid" aria-label="Indicateurs clés">
            <article class="metric-card"><div class="metric-heading"><span class="metric-icon teal">▤</span><span class="metric-trend positive">+8,4%</span></div><strong class="metric-value">24</strong><span class="metric-label">Demandes ouvertes</span><div class="sparkline teal-line"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div></article>
            <article class="metric-card"><div class="metric-heading"><span class="metric-icon amber">◷</span><span class="metric-trend warning">+3 aujourd'hui</span></div><strong class="metric-value">08</strong><span class="metric-label">En attente de prise en charge</span><div class="sparkline amber-line"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div></article>
            <article class="metric-card"><div class="metric-heading"><span class="metric-icon blue">✓</span><span class="metric-trend positive">-12,5%</span></div><strong class="metric-value">16</strong><span class="metric-label">Interventions en cours</span><div class="sparkline blue-line"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div></article>
            <article class="metric-card"><div class="metric-heading"><span class="metric-icon violet">⌁</span><span class="metric-trend neutral">Stable</span></div><strong class="metric-value">94<span class="metric-unit">%</span></strong><span class="metric-label">Disponibilité du parc</span><div class="sparkline violet-line"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div></article>
        </section>
        <section class="dashboard-grid">
            <article class="panel" id="demandes"><div class="panel-header"><div><p class="eyebrow">À TRAITER EN PRIORITÉ</p><h2>Demandes récentes</h2></div><a class="text-link" href="#demandes">Voir toutes <span>→</span></a></div><div class="request-list">@foreach ([['#DSI-2481', 'Accès VPN pour équipe finance', 'Direction financière · Il y a 18 min', 'Urgent', 'high'], ['#DSI-2480', 'Remplacement poste de travail', 'Bureau des achats · Il y a 42 min', 'En cours', 'medium'], ['#DSI-2479', 'Imprimante indisponible au 2e étage', 'Direction juridique · Il y a 1 h', 'À assigner', 'low'], ['#DSI-2478', "Création d'un compte collaborateur", 'Ressources humaines · Il y a 2 h', 'Résolu', 'done']] as $request)<div class="request-row"><span class="request-code">{{ $request[0] }}</span><div class="request-content"><strong>{{ $request[1] }}</strong><small>{{ $request[2] }}</small></div><span class="pill {{ $request[4] }}">{{ $request[3] }}</span><span class="row-arrow">→</span></div>@endforeach</div></article>
            <article class="panel" id="interventions"><div class="panel-header"><div><p class="eyebrow">SUIVI EN DIRECT</p><h2>Activité récente</h2></div><button class="more-button" aria-label="Plus d'options">•••</button></div><div class="activity-list">@foreach ([['✓', 'green', 'Intervention résolue', 'Marc a clôturé #DSI-2478', 'Il y a 8 min'], ['＋', 'blue', 'Nouvelle demande', 'Une demande a été créée par RH', 'Il y a 26 min'], ['↗', 'orange', 'Demande assignée', 'Inès prend en charge #DSI-2480', 'Il y a 42 min'], ['◎', 'violet', 'Présence enregistrée', 'Karim a démarré sa journée', 'Il y a 1 h']] as $activity)<div class="activity-item"><span class="activity-mark {{ $activity[1] }}">{{ $activity[0] }}</span><p><strong>{{ $activity[2] }}</strong><span>{{ $activity[3] }}</span><small>{{ $activity[4] }}</small></p></div>@endforeach</div></article>
        </section>
        <section class="bottom-grid">
            <article class="panel" id="equipes"><div class="panel-header"><div><p class="eyebrow">RESSOURCES HUMAINES</p><h2>Disponibilité des équipes</h2></div><a class="text-link" href="#equipes">Gérer <span>→</span></a></div><div class="availability-body"><div class="donut"><span>18<small>/ 22</small></span></div><div class="availability-copy"><strong>Techniciens disponibles</strong><p>Les équipes sont prêtes à intervenir.</p><div class="legend"><span><i class="available"></i>Disponible <b>18</b></span><span><i class="busy"></i>En intervention <b>3</b></span><span><i class="away"></i>Absents <b>1</b></span></div></div></div></article>
            <article class="panel" id="equipements"><div class="panel-header"><div><p class="eyebrow">ACCÈS RAPIDES</p><h2>Raccourcis opérationnels</h2></div></div><div class="quick-links"><a href="#demandes"><span class="quick-icon teal">＋</span><span><strong>Créer une demande</strong><small>Signaler un besoin au support</small></span><b>→</b></a><a href="#equipements"><span class="quick-icon blue">▦</span><span><strong>Inventaire équipements</strong><small>Consulter le parc informatique</small></span><b>→</b></a></div></article>
        </section>
        <footer class="footer"><span>Console DSI <b>•</b> Environnement local</span><span>Version 1.0 <b>•</b> Support informatique</span></footer>
    </main>
</div>
</body>
</html>
