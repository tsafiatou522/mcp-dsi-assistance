import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api/axios';

export default function Register() {
  const [form, setForm] = useState({
    name: '', email: '', password: '', role: 'agent', direction_id: '',
  });
  const [directions, setDirections] = useState([]);
  const [error, setError] = useState(null);
  const [succes, setSucces] = useState(null);
  useEffect(() => {
    api.get('/directions').then((res) => setDirections(res.data)).catch(() => {});
  }, []);

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    setSucces(null);
    try {
      const response = await api.post('/register', form);
      setSucces(response.data.message);
    } catch (err) {
      setError("Erreur lors de l'inscription. Vérifiez vos informations.");
    }
  }

  return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh' }}>
      <form onSubmit={handleSubmit}>
        <h1>Créer un compte</h1>
        <div>
          <label>Nom complet</label>
          <input name="name" value={form.name} onChange={handleChange} required />
        </div>
        <div>
          <label>Email</label>
          <input type="email" name="email" value={form.email} onChange={handleChange} required />
        </div>
        <div>
          <label>Mot de passe</label>
          <input type="password" name="password" value={form.password} onChange={handleChange} required />
        </div>
        <div>
          <label>Rôle</label>
          <select name="role" value={form.role} onChange={handleChange}>
            <option value="agent">Agent</option>
            <option value="technicien">Technicien</option>
            <option value="responsable">Responsable</option>
          </select>
        </div>
        <div>
          <label>Direction</label>
          <select name="direction_id" value={form.direction_id} onChange={handleChange}>
            <option value="">-- Choisir --</option>
            {directions.map((d) => (
              <option key={d.id} value={d.id}>{d.nom}</option>
            ))}
          </select>
        </div>
        {error && <p style={{ color: 'red' }}>{error}</p>}
        {succes && <p style={{ color: 'green' }}>{succes}</p>}
        <button type="submit">S'inscrire</button>
        <p style={{ marginTop: '1rem', fontSize: '0.9rem' }}>
          Déjà un compte ? <Link to="/login">Se connecter</Link>
        </p>
      </form>
    </div>
  );
}
