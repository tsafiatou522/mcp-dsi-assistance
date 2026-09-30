import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api/axios';

export default function Notifications() {
  const [notifications, setNotifications] = useState([]);
  const [ouvert, setOuvert] = useState(false);
  const navigate = useNavigate();

  async function charger() {
    const response = await api.get('/notifications');
    setNotifications(response.data);
  }

  useEffect(() => {
    charger();
    const interval = setInterval(charger, 5000);
    return () => clearInterval(interval);
  }, []);

  const nonLues = notifications.filter((n) => !n.read_at);

  async function ouvrirNotification(n) {
    if (!n.read_at) {
      await api.post(`/notifications/${n.id}/lue`);
      charger();
    }
    setOuvert(false);
    navigate(`/demandes/${n.data.demande_id}`);
  }

  return (
    <div style={{ position: 'relative' }}>
      <button
        onClick={() => setOuvert(!ouvert)}
        style={{
          background: 'none',
          border: '1px solid #475569',
          color: 'white',
          padding: '0.5rem',
          borderRadius: '6px',
          cursor: 'pointer',
          width: '100%',
          textAlign: 'left',
          position: 'relative',
        }}
      >
        🔔 Notifications
        {nonLues.length > 0 && (
          <span style={{
            position: 'absolute',
            top: '-6px',
            right: '-6px',
            backgroundColor: '#ef4444',
            color: 'white',
            borderRadius: '999px',
            fontSize: '0.7rem',
            padding: '0.1rem 0.4rem',
          }}>
            {nonLues.length}
          </span>
        )}
      </button>

      {ouvert && (
        <div style={{
          position: 'absolute',
          bottom: '110%',
          left: 0,
          width: '280px',
          background: 'white',
          color: '#1a1a1a',
          borderRadius: '8px',
          boxShadow: '0 2px 10px rgba(0,0,0,0.2)',
          maxHeight: '300px',
          overflowY: 'auto',
          zIndex: 10,
        }}>
          {notifications.length === 0 && (
            <p style={{ padding: '1rem', color: '#888' }}>Aucune notification.</p>
          )}
          {notifications.map((n) => (
            <div
              key={n.id}
              onClick={() => ouvrirNotification(n)}
              style={{
                padding: '0.75rem 1rem',
                borderBottom: '1px solid #eee',
                cursor: 'pointer',
                backgroundColor: n.read_at ? 'white' : '#eff6ff',
              }}
            >
              <div style={{ fontSize: '0.85rem' }}>{n.data.message}</div>
              <div style={{ fontSize: '0.75rem', color: '#888' }}>{n.data.direction} — {n.data.bureau}</div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
