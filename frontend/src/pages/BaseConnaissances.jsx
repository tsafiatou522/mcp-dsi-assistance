import { useEffect, useState } from 'react';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';

export default function BaseConnaissances() {
  const { user } = useAuth();
  const [articles, setArticles] = useState([]);
  const [recherche, setRecherche] = useState('');
  const [afficherForm, setAfficherForm] = useState(false);
  const [form, setForm] = useState({ titre: '', categorie: '', contenu: '' });

  async function charger() {
    const response = await api.get('/articles', { params: recherche ? { search: recherche } : {} });
    setArticles(response.data);
  }

  useEffect(() => {
    charger();
  }, [recherche]);

  async function publier(e) {
    e.preventDefault();
    await api.post('/articles', form);
    setForm({ titre: '', categorie: '', contenu: '' });
    setAfficherForm(false);
    charger();
  }

  return (
    <div>
      <div className="page-header">
        <div><h1>Base de connaissances</h1><p className="page-subtitle">Guides et solutions pour accélérer le support</p></div>
        {['responsable', 'admin'].includes(user?.role) && (
          <button className="btn-primaire" onClick={() => setAfficherForm(!afficherForm)}>
            {afficherForm ? 'Annuler' : '+ Nouvel article'}
          </button>
        )}
      </div>

      {afficherForm && (
        <form onSubmit={publier} className="article-form">
          <div><label>Titre</label><input value={form.titre} onChange={(e) => setForm({ ...form, titre: e.target.value })} required /></div>
          <div><label>Catégorie</label><input value={form.categorie} onChange={(e) => setForm({ ...form, categorie: e.target.value })} placeholder="Réseau, imprimante, logiciel..." /></div>
          <div><label>Contenu</label><textarea value={form.contenu} onChange={(e) => setForm({ ...form, contenu: e.target.value })} required /></div>
          <button type="submit">Publier l’article</button>
        </form>
      )}

      <input className="knowledge-search" type="search" placeholder="Rechercher un guide ou une solution..." value={recherche} onChange={(e) => setRecherche(e.target.value)} />
      <div className="knowledge-grid">
        {articles.length === 0 && <div className="vide">Aucun article trouvé.</div>}
        {articles.map((article) => (
          <article key={article.id} className="knowledge-card">
            <span className="knowledge-category">{article.categorie || 'Support'}</span>
            <h2>{article.titre}</h2>
            <p>{article.contenu}</p>
            <small>Publié par {article.auteur?.name || 'DSI'}</small>
          </article>
        ))}
      </div>
    </div>
  );
}