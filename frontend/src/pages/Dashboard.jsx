import { useEffect, useState } from 'react';
import { Doughnut, Bar } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  ArcElement,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
} from 'chart.js';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';

ChartJS.register(ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

export default function Dashboard() {
  const { user } = useAuth();
  const [demandes, setDemandes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  async function charger() {
    try {
      const response = await api.get('/demandes');
      setDemandes(response.data);
      setError(null);
    } catch (err) {
      setError('Impossible de charger les demandes. Vérifiez que le serveur API est démarré.');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    charger();
    const interval = setInterval(charger, 5000);
    return () => clearInterval(interval);
  }, []);

  if (loading) return <p className="page-loading">Chargement...</p>;

  if (error) {
    return (
      <div className="loading-error">
        <h1>Le tableau de bord est indisponible</h1>
        <p>{error}</p>
        <button className="btn-primaire" onClick={charger}>Réessayer</button>
      </div>
    );
  }

  if (user?.role === 'agent') {
    return <TableauAgent user={user} demandes={demandes} />;
  }

  if (user?.role === 'technicien') {
    return <TableauTechnicien user={user} demandes={demandes} />;
  }

  return <TableauGlobal demandes={demandes} />;
}

function Carte({ icone, label, valeur, couleur, fond }) {
  return (
    <div className="stat-card">
      <div className="stat-card-icon" style={{ backgroundColor: fond, color: couleur }}>
        {icone}
      </div>
      <div>
        <div className="stat-card-valeur">{valeur}</div>
        <div className="stat-card-label">{label}</div>
      </div>
    </div>
  );
}

const STATUTS = ['nouvelle', 'prise_en_charge', 'en_intervention', 'en_attente_confirmation', 'cloturee'];
const STATUT_LABELS = { nouvelle: 'Nouvelle', prise_en_charge: 'Prise en charge', en_intervention: 'En intervention', en_attente_confirmation: 'À confirmer', cloturee: 'Clôturée' };
const STATUT_COULEURS = { nouvelle: '#93c5fd', prise_en_charge: '#60a5fa', en_intervention: '#3b82f6', en_attente_confirmation: '#1d4ed8', cloturee: '#1e3a8a' };

const PRIORITES = ['basse', 'normale', 'haute', 'urgente'];
const PRIORITE_LABELS = { basse: 'Basse', normale: 'Normale', haute: 'Haute', urgente: 'Urgente' };
const PRIORITE_COULEURS = { basse: '#cbd5e1', normale: '#93c5fd', haute: '#3b82f6', urgente: '#1e3a8a' };

function GraphiqueStatuts({ demandes }) {
  const data = {
    labels: STATUTS.map((s) => STATUT_LABELS[s]),
    datasets: [{
      data: STATUTS.map((s) => demandes.filter((d) => d.statut === s).length),
      backgroundColor: STATUTS.map((s) => STATUT_COULEURS[s]),
      borderWidth: 0,
    }],
  };
  return (
    <div style={{ background: 'white', borderRadius: 10, padding: '1.25rem', boxShadow: '0 1px 2px rgba(0,0,0,0.06)', border: '1px solid #f1f5f9' }}>
      <div style={{ fontWeight: 600, marginBottom: '1rem', fontSize: '0.9rem' }}>Répartition par statut</div>
      <div style={{ height: '200px', display: 'flex', justifyContent: 'center' }}>
        <Doughnut
          data={data}
          options={{
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 }, padding: 10 } } },
          }}
        />
      </div>
    </div>
  );
}

function GraphiquePriorites({ demandes }) {
  const data = {
    labels: PRIORITES.map((p) => PRIORITE_LABELS[p]),
    datasets: [{
      label: 'Demandes',
      data: PRIORITES.map((p) => demandes.filter((d) => d.priorite === p).length),
      backgroundColor: PRIORITES.map((p) => PRIORITE_COULEURS[p]),
      borderRadius: 4,
      maxBarThickness: 40,
    }],
  };
  return (
    <div style={{ background: 'white', borderRadius: 10, padding: '1.25rem', boxShadow: '0 1px 2px rgba(0,0,0,0.06)', border: '1px solid #f1f5f9' }}>
      <div style={{ fontWeight: 600, marginBottom: '1rem', fontSize: '0.9rem' }}>Répartition par priorité</div>
      <div style={{ height: '200px' }}>
        <Bar
          data={data}
          options={{
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } },
          }}
        />
      </div>
    </div>
  );
}

function TableauAgent({ user, demandes }) {
  const mesDemandes = demandes.filter((d) => d.agent_id === user.id);
  const enCours = mesDemandes.filter((d) => !['cloturee'].includes(d.statut)).length;
  const aConfirmer = mesDemandes.filter((d) => d.statut === 'en_attente_confirmation').length;
  const cloturees = mesDemandes.filter((d) => d.statut === 'cloturee').length;

  return (
    <div>
      <h1>Bienvenue, {user.name}</h1>
      <p className="page-subtitle">Vos demandes — actualisées toutes les 5 secondes</p>

      <div className="stats-grid">
        <Carte icone="📋" label="Total" valeur={mesDemandes.length} couleur="#2563eb" fond="#eff6ff" />
        <Carte icone="⏳" label="En cours" valeur={enCours} couleur="#7c3aed" fond="#f5f3ff" />
        <Carte icone="✔️" label="À confirmer" valeur={aConfirmer} couleur="#0891b2" fond="#ecfeff" />
        <Carte icone="✅" label="Clôturées" valeur={cloturees} couleur="#16a34a" fond="#f0fdf4" />
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1rem' }}>
        <GraphiqueStatuts demandes={mesDemandes} />
        <GraphiquePriorites demandes={mesDemandes} />
      </div>

    </div>
  );
}

function TableauTechnicien({ user, demandes }) {
  const disponibles = demandes.filter((d) => d.statut === 'nouvelle');
  const mesInterventions = demandes.filter((d) => d.technicien_id === user.id);
  const enCours = mesInterventions.filter((d) => ['prise_en_charge', 'en_intervention'].includes(d.statut)).length;
  const resolues = mesInterventions.filter((d) => ['en_attente_confirmation', 'cloturee'].includes(d.statut)).length;

  return (
    <div>
      <h1>Bienvenue, {user.name}</h1>
      <p className="page-subtitle">Vue technicien — actualisée toutes les 5 secondes</p>

      <div className="stats-grid">
        <Carte icone="🆕" label="Demandes disponibles" valeur={disponibles.length} couleur="#d97706" fond="#fffbeb" />
        <Carte icone="🔧" label="Mes interventions en cours" valeur={enCours} couleur="#7c3aed" fond="#f5f3ff" />
        <Carte icone="✅" label="Mes résolutions" valeur={resolues} couleur="#16a34a" fond="#f0fdf4" />
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1rem' }}>
        <GraphiqueStatuts demandes={mesInterventions} />
        <GraphiquePriorites demandes={disponibles} />
      </div>

    </div>
  );
}

function TableauGlobal({ demandes }) {
  const total = demandes.length;
  const nouvelles = demandes.filter((d) => d.statut === 'nouvelle').length;
  const enCours = demandes.filter((d) => ['prise_en_charge', 'en_intervention'].includes(d.statut)).length;
  const aConfirmer = demandes.filter((d) => d.statut === 'en_attente_confirmation').length;
  const cloturees = demandes.filter((d) => d.statut === 'cloturee').length;

  return (
    <div>
      <h1>Vue d'ensemble</h1>
      <p className="page-subtitle">Vue en temps réel — actualisée toutes les 5 secondes</p>

      <div className="stats-grid">
        <Carte icone="📋" label="Total" valeur={total} couleur="#2563eb" fond="#eff6ff" />
        <Carte icone="🆕" label="Nouvelles" valeur={nouvelles} couleur="#d97706" fond="#fffbeb" />
        <Carte icone="🔧" label="En cours" valeur={enCours} couleur="#7c3aed" fond="#f5f3ff" />
        <Carte icone="✔️" label="À confirmer" valeur={aConfirmer} couleur="#0891b2" fond="#ecfeff" />
        <Carte icone="✅" label="Clôturées" valeur={cloturees} couleur="#16a34a" fond="#f0fdf4" />
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1rem' }}>
        <GraphiqueStatuts demandes={demandes} />
        <GraphiquePriorites demandes={demandes} />
      </div>

    </div>
  );
}
