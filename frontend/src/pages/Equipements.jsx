import { useEffect, useState } from 'react';
import api from '../api/axios';

export default function Equipements() {
  const [equipements, setEquipements] = useState([]);
  const [directions, setDirections] = useState([]);
  const [bureaux, setBureaux] = useState([]);
  const [afficherForm, setAfficherForm] = useState(false);
  const [form, setForm] = useState({
    nom: '',
    type: 'ordinateur',
    numero_serie: '',
    marque: '',
    modele: '',
    direction_id: '',
    bureau_id: '',
    etat: 'fonctionnel',
  });
  const [error, setError] = useState(null);

  async function charger() {
    const response = await api.get('/equipements');
    setEquipements(response.data);
  }

  useEffect(() => {
    charger();
    api.get('/directions').then((res) => setDirections(res.data));
    api.get('/bureaux').then((res) => setBureaux(res.data));
  }, []);

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    try {
      await api.post('/equipements', form);
      setForm({
        nom: '', type: 'ordinateur', numero_serie: '', marque: '', modele: '',
        direction_id: '', bureau_id: '', etat: 'fonctionnel',
      });
      setAfficherForm(false);
      charger();
    } catch (err) {
      setError("Erreur lors de la création de l'équipement.");
    }
  }

  const etatLabels = {
    fonctionnel: 'Fonctionnel',
    en_panne: 'En panne',
    en_maintenance: 'En maintenance',
    hors_service: 'Hors service',
  };

  return (
    <div>
      <div className="page-header">
        <h1>Équipements</h1>
        <button className="btn-primaire" onClick={() => setAfficherForm(!afficherForm)}>
          {afficherForm ? 'Annuler' : '+ Nouvel équipement'}
        </button>
      </div>

      {afficherForm && (
        <form onSubmit={handleSubmit} style={{ marginBottom: '1.5rem' }}>
          <div>
            <label>Nom</label>
            <input name="nom" value={form.nom} onChange={handleChange} required />
          </div>
          <div>
            <label>Type</label>
            <select name="type" value={form.type} onChange={handleChange}>
              <option value="ordinateur">Ordinateur</option>
              <option value="imprimante">Imprimante</option>
              <option value="scanner">Scanner</option>
              <option value="reseau">Réseau</option>
              <option value="autre">Autre</option>
            </select>
          </div>
          <div>
            <label>Numéro de série</label>
            <input name="numero_serie" value={form.numero_serie} onChange={handleChange} />
          </div>
          <div>
            <label>Marque</label>
            <input name="marque" value={form.marque} onChange={handleChange} />
          </div>
          <div>
            <label>Modèle</label>
            <input name="modele" value={form.modele} onChange={handleChange} />
          </div>
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
            <select name="bureau_id" value={form.bureau_id} onChange={handleChange}>
              <option value="">-- Aucun --</option>
              {bureaux.map((b) => (
                <option key={b.id} value={b.id}>{b.nom}</option>
              ))}
            </select>
          </div>
          <div>
            <label>État</label>
            <select name="etat" value={form.etat} onChange={handleChange}>
              <option value="fonctionnel">Fonctionnel</option>
              <option value="en_panne">En panne</option>
              <option value="en_maintenance">En maintenance</option>
              <option value="hors_service">Hors service</option>
            </select>
          </div>
          {error && <p style={{ color: 'red' }}>{error}</p>}
          <button type="submit">Ajouter l'équipement</button>
        </form>
      )}

      {equipements.length === 0 && <div className="vide">Aucun équipement enregistré.</div>}
      <div style={{ display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
        {equipements.map((e) => (
          <div key={e.id} className="carte-liste">
            <div className="carte-liste-lien">
              <div className="carte-liste-titre">
                <span>{e.nom} — {e.marque} {e.modele}</span>
                <span className={`badge badge-${e.etat === 'fonctionnel' ? 'cloturee' : e.etat === 'en_panne' ? 'nouvelle' : 'en_intervention'}`}>
                  {etatLabels[e.etat]}
                </span>
              </div>
              <div className="carte-liste-meta">
                {e.type} — {e.numero_serie || 'Pas de N/S'} — {e.direction?.nom} {e.bureau ? `— ${e.bureau.nom}` : ''}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
