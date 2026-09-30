import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';

export default function Demandes() {
  const { user } = useAuth();
  const [demandes, setDemandes] = useState([]);
  const [directions, setDirections] = useState([]);
  const [bureaux, setBureaux] = useState([]);
  const [categories, setCategories] = useState([]);
  const [techniciens, setTechniciens] = useState([]);
  const [recherche, setRecherche] = useState('');
  const [filtreStatut, setFiltreStatut] = useState('');
  const [filtrePriorite, setFiltrePriorite] = useState('');
  const [afficherForm, setAfficherForm] = useState(false);
  const [form, setForm] = useState({
    direction_id: '',
    bureau_id: '',
    category_id: '',
    description: '',
    priorite: 'normale',
  });
  const [error, setError] = useState(null);

  async function chargerDemandes() {
    const response = await api.get('/demandes');
    setDemandes(response.data);
  }

  useEffect(() => {
    chargerDemandes();
    api.get('/directions').then((res) => setDirections(res.data));
    api.get('/bureaux').then((res) => setBureaux(res.data));
    api.get('/categories').then((res) => setCategories(res.data));
    if (['responsable', 'admin'].includes(user?.role)) {
      api.get('/users?role=technicien').then((res) => setTechniciens(res.data));
    }
  }, [user?.role]);

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    try {
      await api.post('/demandes', form);
      setForm({ direction_id: '', bureau_id: '', category_id: '', description: '', priorite: 'normale' });
      setAfficherForm(false);
      chargerDemandes();
    } catch (err) {
      setError('Erreur lors de la création de la demande.');
    }
  }

  async function prendreEnCharge(id) {
    await api.post(`/demandes/${id}/prendre-en-charge`);
    chargerDemandes();
  }

  async function affecter(id, technicienId) {
    if (!technicienId) return;
    try {
      await api.put(`/demandes/${id}/affecter`, { technicien_id: technicienId });
      chargerDemandes();
    } catch (err) {
      setError('Impossible d’affecter cette demande.');
    }
  }

  async function exporterDemandes() {
    const response = await api.get('/demandes/export', { responseType: 'blob' });
    const url = URL.createObjectURL(response.data);
    const lien = document.createElement('a');
    lien.href = url;
    lien.download = 'demandes.csv';
    lien.click();
    URL.revokeObjectURL(url);
  }

  const demandesFiltrees = demandes.filter((demande) => {
    const texte = `${demande.id} ${demande.description} ${demande.direction?.nom || ''} ${demande.bureau?.nom || ''}`.toLowerCase();
    return texte.includes(recherche.toLowerCase())
      && (!filtreStatut || demande.statut === filtreStatut)
      && (!filtrePriorite || demande.priorite === filtrePriorite);
  });

  return (
    <div>
      <div className="page-header">
        <h1>Demandes</h1>
        <div className="page-actions">
          {['responsable', 'admin'].includes(user?.role) && (
            <button className="btn-export" onClick={exporterDemandes}>Exporter CSV</button>
          )}
          {user?.role === 'agent' && (
            <button className="btn-primaire" onClick={() => setAfficherForm(!afficherForm)}>
              {afficherForm ? 'Annuler' : '+ Nouvelle demande'}
            </button>
          )}
        </div>
      </div>

      {afficherForm && user?.role === 'agent' && (
        <form onSubmit={handleSubmit} style={{ marginBottom: '1.5rem' }}>
          <div>
            <label>Direction</label>
            <select name="direction_id" value={form.direction_id} onChange={handleChange} required>
              <option value="">-- Choisir --</option>
              {directions.map((d) => (
                <option key={d.id} value={d.id}>{d.nom}</option>
              ))}
            </select>
          </div>
          <div>
            <label>Bureau</label>
            <select name="bureau_id" value={form.bureau_id} onChange={handleChange} required>
              <option value="">-- Choisir --</option>
              {bureaux.map((b) => (
                <option key={b.id} value={b.id}>{b.nom}</option>
              ))}
            </select>
          </div>
          <div>
            <label>Catégorie</label>
            <select name="category_id" value={form.category_id} onChange={handleChange} required>
              <option value="">-- Choisir --</option>
              {categories.map((c) => (
                <option key={c.id} value={c.id}>{c.nom}</option>
              ))}
            </select>
          </div>
          <div>
            <label>Priorité</label>
            <select name="priorite" value={form.priorite} onChange={handleChange}>
              <option value="basse">Basse</option>
              <option value="normale">Normale</option>
              <option value="haute">Haute</option>
              <option value="urgente">Urgente</option>
            </select>
          </div>
          <div>
            <label>Description</label>
            <textarea name="description" value={form.description} onChange={handleChange} required />
          </div>
          {error && <p style={{ color: 'red' }}>{error}</p>}
          <button type="submit">Créer la demande</button>
        </form>
      )}

      {demandes.length === 0 && <div className="vide">Aucune demande pour le moment.</div>}

      {demandes.length > 0 && (
        <div className="filtres-demandes">
          <input
            type="search"
            placeholder="Rechercher une demande..."
            value={recherche}
            onChange={(e) => setRecherche(e.target.value)}
            aria-label="Rechercher une demande"
          />
          <select value={filtreStatut} onChange={(e) => setFiltreStatut(e.target.value)} aria-label="Filtrer par statut">
            <option value="">Tous les statuts</option>
            <option value="nouvelle">Nouvelle</option>
            <option value="prise_en_charge">Prise en charge</option>
            <option value="en_intervention">En intervention</option>
            <option value="en_attente_confirmation">À confirmer</option>
            <option value="cloturee">Clôturée</option>
          </select>
          <select value={filtrePriorite} onChange={(e) => setFiltrePriorite(e.target.value)} aria-label="Filtrer par priorité">
            <option value="">Toutes les priorités</option>
            <option value="basse">Basse</option>
            <option value="normale">Normale</option>
            <option value="haute">Haute</option>
            <option value="urgente">Urgente</option>
          </select>
        </div>
      )}

      {demandes.length > 0 && demandesFiltrees.length === 0 && (
        <div className="vide">Aucune demande ne correspond aux critères.</div>
      )}

      <div style={{ display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
        {demandesFiltrees.map((d) => (
          <div key={d.id} className="carte-liste">
            <Link to={`/demandes/${d.id}`} className="carte-liste-lien">
              <div className="carte-liste-titre">
                <span>#{d.id} — {d.description}</span>
                <span className={`badge badge-${d.statut}`}>{d.statut}</span>
              </div>
              <div className="carte-liste-meta">
                Priorité : {d.priorite}
                {d.date_echeance && ` — Échéance : ${new Date(d.date_echeance).toLocaleDateString('fr-FR')}`}
              </div>
            </Link>
            {user?.role === 'technicien' && d.statut === 'nouvelle' && (
              <button className="btn-secondaire" onClick={() => prendreEnCharge(d.id)}>Prendre en charge</button>
            )}
            {['responsable', 'admin'].includes(user?.role) && (
              <select
                className="select-affectation"
                value={d.technicien_id || ''}
                onChange={(e) => affecter(d.id, e.target.value)}
                aria-label={`Affecter la demande ${d.id}`}
              >
                <option value="">Affecter à...</option>
                {techniciens.map((technicien) => (
                  <option key={technicien.id} value={technicien.id}>{technicien.name}</option>
                ))}
              </select>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
