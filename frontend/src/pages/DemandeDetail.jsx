import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';

export default function DemandeDetail() {
  const { id } = useParams();
  const { user } = useAuth();
  const navigate = useNavigate();
  const [demande, setDemande] = useState(null);
  const [historiques, setHistoriques] = useState([]);
  const [commentaires, setCommentaires] = useState([]);
  const [nouveauCommentaire, setNouveauCommentaire] = useState('');
  const [compteRendu, setCompteRendu] = useState({
    diagnostic: '',
    actions_realisees: '',
    materiel_utilise: '',
    difficultes_rencontrees: '',
    solution_apportee: '',
    observations: '',
  });

  async function charger() {
    const response = await api.get(`/demandes/${id}`);
    setDemande(response.data);
    const historiqueResponse = await api.get(`/demandes/${id}/historique`);
    setHistoriques(historiqueResponse.data);
    const commentairesResponse = await api.get(`/demandes/${id}/commentaires`);
    setCommentaires(commentairesResponse.data);
  }

  useEffect(() => {
    charger();
  }, [id]);

  const estMonIntervention = demande?.technicien_id === user?.id;
  const intervention = demande?.interventions?.[demande.interventions.length - 1];
  const echeanceDepassee = demande?.date_echeance && new Date(demande.date_echeance) < new Date()
    && !['cloturee'].includes(demande.statut);

  function handleChangeCompteRendu(e) {
    setCompteRendu({ ...compteRendu, [e.target.name]: e.target.value });
  }

  async function ajouterCommentaire(e) {
    e.preventDefault();
    if (!nouveauCommentaire.trim()) return;
    await api.post(`/demandes/${id}/commentaires`, { contenu: nouveauCommentaire });
    setNouveauCommentaire('');
    charger();
  }

  async function demarrerIntervention(e) {
    e.preventDefault();
    await api.post(`/demandes/${id}/interventions`, compteRendu);
    charger();
  }

  async function marquerResolue() {
    await api.post(`/interventions/${intervention.id}/resoudre`);
    charger();
  }

  async function confirmer() {
    await api.post(`/demandes/${id}/confirmer`);
    charger();
  }

  async function signalerPersistance() {
    await api.post(`/demandes/${id}/signaler-persistance`);
    charger();
  }

  if (!demande) return <p>Chargement...</p>;

  return (
    <div>
      <button className="bouton-retour" onClick={() => navigate('/demandes')}>
        ← Retour
      </button>

      <h1>Demande #{demande.id}</h1>

      <div className="carte-detail">
        <div className="ligne">
          <span className="cle">Statut</span>
          <span className={`badge badge-${demande.statut}`}>{demande.statut}</span>
        </div>
        <div className="ligne">
          <span className="cle">Priorité</span>
          <span>{demande.priorite}</span>
        </div>
        <div className="ligne">
          <span className="cle">Échéance SLA</span>
          <span className={echeanceDepassee ? 'sla-retard' : ''}>
            {demande.date_echeance ? new Date(demande.date_echeance).toLocaleString('fr-FR') : 'Non définie'}
            {echeanceDepassee && ' · En retard'}
          </span>
        </div>
        <div className="ligne">
          <span className="cle">Description</span>
          <span>{demande.description}</span>
        </div>
        <div className="ligne">
          <span className="cle">Direction</span>
          <span>{demande.direction?.nom}</span>
        </div>
        <div className="ligne">
          <span className="cle">Bureau</span>
          <span>{demande.bureau?.nom}</span>
        </div>
        <div className="ligne">
          <span className="cle">Catégorie</span>
          <span>{demande.categorie?.nom}</span>
        </div>
        <div className="ligne">
          <span className="cle">Agent</span>
          <span>{demande.agent?.name}</span>
        </div>
        <div className="ligne">
          <span className="cle">Technicien</span>
          <span>{demande.technicien?.name || 'Non assigné'}</span>
        </div>
      </div>

      <div className="carte-detail historique-card">
        <h2>Historique de la demande</h2>
        {historiques.length === 0 ? (
          <p className="historique-vide">Aucune action enregistrée.</p>
        ) : (
          <div className="historique-liste">
            {historiques.map((historique) => (
              <div key={historique.id} className="historique-item">
                <span className="historique-point" />
                <div>
                  <strong>{historique.action}</strong>
                  {historique.commentaire && <p>{historique.commentaire}</p>}
                  <small>
                    {historique.user?.name || 'Système'} · {new Date(historique.created_at).toLocaleString('fr-FR')}
                  </small>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      <div className="carte-detail commentaires-card">
        <h2>Suivi de la demande</h2>
        <div className="commentaires-liste">
          {commentaires.length === 0 && <p className="historique-vide">Aucun commentaire pour le moment.</p>}
          {commentaires.map((commentaire) => (
            <div key={commentaire.id} className="commentaire-item">
              <div className="commentaire-entete">
                <strong>{commentaire.user?.name || 'Utilisateur'}</strong>
                <small>{new Date(commentaire.created_at).toLocaleString('fr-FR')}</small>
              </div>
              <p>{commentaire.contenu}</p>
            </div>
          ))}
        </div>
        <form className="commentaire-form" onSubmit={ajouterCommentaire}>
          <label htmlFor="nouveau-commentaire">Ajouter un commentaire</label>
          <textarea
            id="nouveau-commentaire"
            value={nouveauCommentaire}
            onChange={(e) => setNouveauCommentaire(e.target.value)}
            placeholder="Écrire une information ou une question..."
            maxLength={2000}
          />
          <button type="submit">Publier le commentaire</button>
        </form>
      </div>

      {estMonIntervention && demande.statut === 'prise_en_charge' && (
        <form onSubmit={demarrerIntervention}>
          <h2>Compte rendu d'intervention</h2>
          <div>
            <label>Diagnostic</label>
            <textarea name="diagnostic" value={compteRendu.diagnostic} onChange={handleChangeCompteRendu} />
          </div>
          <div>
            <label>Actions réalisées</label>
            <textarea name="actions_realisees" value={compteRendu.actions_realisees} onChange={handleChangeCompteRendu} />
          </div>
          <div>
            <label>Matériel utilisé</label>
            <input name="materiel_utilise" value={compteRendu.materiel_utilise} onChange={handleChangeCompteRendu} />
          </div>
          <div>
            <label>Difficultés rencontrées</label>
            <textarea name="difficultes_rencontrees" value={compteRendu.difficultes_rencontrees} onChange={handleChangeCompteRendu} />
          </div>
          <div>
            <label>Solution apportée</label>
            <textarea name="solution_apportee" value={compteRendu.solution_apportee} onChange={handleChangeCompteRendu} />
          </div>
          <div>
            <label>Observations</label>
            <textarea name="observations" value={compteRendu.observations} onChange={handleChangeCompteRendu} />
          </div>
          <button type="submit">Démarrer l'intervention</button>
        </form>
      )}

      {estMonIntervention && demande.statut === 'en_intervention' && intervention && (
        <div className="carte-detail">
          <h2 style={{ marginTop: 0 }}>Intervention en cours</h2>
          <p><strong>Diagnostic :</strong> {intervention.diagnostic}</p>
          <p><strong>Solution apportée :</strong> {intervention.solution_apportee}</p>
          <button onClick={marquerResolue}>Déclarer résolue</button>
        </div>
      )}

      {user?.id === demande.agent_id && demande.statut === 'en_attente_confirmation' && (
        <div className="carte-detail">
          <h2 style={{ marginTop: 0 }}>Confirmation</h2>
          <p>Le technicien a déclaré cette demande résolue. Confirmez-vous ?</p>
          <button onClick={confirmer}>Confirmer la résolution</button>
          <button className="bouton-secondaire" onClick={signalerPersistance}>
            Le problème persiste
          </button>
        </div>
      )}
    </div>
  );
}
