import { useEffect, useState } from 'react';
import api from '../api/axios';

export default function Administration() {
  const [enAttente, setEnAttente] = useState([]);
  const [chargement, setChargement] = useState(true);

  async function charger() {
    const response = await api.get('/utilisateurs-en-attente');
    setEnAttente(response.data);
    setChargement(false);
  }

  useEffect(() => {
    charger();
  }, []);

  async function activer(id) {
    await api.post(`/users/${id}/activer`);
    charger();
  }

  const roleLabels = {
    agent: 'Agent',
    technicien: 'Technicien',
    responsable: 'Responsable',
    admin: 'Administrateur',
  };

  return (
    <div>
      <div className="page-header">
        <h1>Comptes en attente de validation</h1>
      </div>

      {chargement && <p>Chargement...</p>}

      {!chargement && enAttente.length === 0 && (
        <div className="vide">Aucun compte en attente.</div>
      )}

      <div style={{ display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
        {enAttente.map((u) => (
          <div key={u.id} className="carte-liste">
            <div className="carte-liste-lien">
              <div className="carte-liste-titre">
                <span>{u.name}</span>
                <span className="badge badge-nouvelle">{roleLabels[u.role]}</span>
              </div>
              <div className="carte-liste-meta">{u.email}</div>
            </div>
            <button className="btn-succes" onClick={() => activer(u.id)}>Activer</button>
          </div>
        ))}
      </div>
    </div>
  );
}
