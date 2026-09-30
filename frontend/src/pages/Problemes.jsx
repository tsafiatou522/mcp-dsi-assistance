import { useEffect, useState } from 'react';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';

export default function Problemes() {
  const { user } = useAuth();
  const [problemes, setProblemes] = useState([]);
  const [afficherForm, setAfficherForm] = useState(false);
  const [form, setForm] = useState({ titre: '', description: '', priorite: 'normale' });

  async function charger() {
    const response = await api.get('/problemes');
    setProblemes(response.data);
  }

  useEffect(() => { charger(); }, []);

  async function creer(e) {
    e.preventDefault();
    await api.post('/problemes', form);
    setForm({ titre: '', description: '', priorite: 'normale' });
    setAfficherForm(false);
    charger();
  }

  return (
    <div>
      <div className="page-header">
        <div><h1>Problèmes récurrents</h1><p className="page-subtitle">Analyse des causes et résolution durable</p></div>
        {['responsable', 'admin'].includes(user?.role) && <button className="btn-primaire" onClick={() => setAfficherForm(!afficherForm)}>{afficherForm ? 'Annuler' : '+ Nouveau problème'}</button>}
      </div>
      {afficherForm && <form onSubmit={creer} className="article-form">
        <div><label>Titre</label><input value={form.titre} onChange={(e) => setForm({ ...form, titre: e.target.value })} required /></div>
        <div><label>Description</label><textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} required /></div>
        <div><label>Priorité</label><select value={form.priorite} onChange={(e) => setForm({ ...form, priorite: e.target.value })}><option value="basse">Basse</option><option value="normale">Normale</option><option value="haute">Haute</option><option value="urgente">Urgente</option></select></div>
        <button type="submit">Créer le problème</button>
      </form>}
      <div className="knowledge-grid">
        {problemes.length === 0 && <div className="vide">Aucun problème enregistré.</div>}
        {problemes.map((probleme) => <article key={probleme.id} className="knowledge-card">
          <span className="knowledge-category">{probleme.statut} · {probleme.priorite}</span>
          <h2>{probleme.titre}</h2>
          <p>{probleme.description}</p>
          {probleme.cause_racine && <p><strong>Cause :</strong> {probleme.cause_racine}</p>}
          {probleme.solution && <p><strong>Solution :</strong> {probleme.solution}</p>}
          <small>Ouvert par {probleme.auteur?.name || 'DSI'}</small>
        </article>)}
      </div>
    </div>
  );
}