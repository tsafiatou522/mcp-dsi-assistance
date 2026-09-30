import { Outlet, Link, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import Notifications from './Notifications.jsx';

export default function Layout() {
  const { user, logout } = useAuth();
  const location = useLocation();

  const liens = [
    { to: '/', label: 'Tableau de bord', icon: '⌂' },
    { to: '/demandes', label: 'Demandes', icon: '▣' },
    { to: '/base-connaissances', label: 'Base de connaissances', icon: '?' },
    { to: '/problemes', label: 'Problèmes', icon: '!' },
  ];

  if (user?.role !== 'agent') {
    liens.push({ to: '/equipements', label: 'Équipements', icon: '◫' });
  }

  if (user?.role === 'admin') {
    liens.push({ to: '/administration', label: 'Administration', icon: '⚙' });
  }

  const pageActuelle = liens.find((lien) => lien.to === location.pathname)?.label || 'Détail de la demande';
  const initiales = user?.name?.split(' ').map((mot) => mot[0]).join('').slice(0, 2).toUpperCase() || 'DS';

  return (
    <div className="app-shell">
      <aside className="app-sidebar">
        <div className="brand">
          <img className="brand-crest" src="/armoirie-burkina.svg" alt="Armoirie du Burkina Faso" />
          <div><strong>DSI Assistance</strong><span>Centre de services</span></div>
        </div>
        <div className="sidebar-heading">Espace de travail</div>
        <nav className="sidebar-nav">
          {liens.map((lien) => (
            <Link
              key={lien.to}
              to={lien.to}
              className={location.pathname === lien.to ? 'active' : ''}
            >
              <span className="nav-icon">{lien.icon}</span>{lien.label}
            </Link>
          ))}
        </nav>
        {(user?.role === 'technicien' || user?.role === 'agent') && (
          <div className="sidebar-notifications">
            <Notifications />
          </div>
        )}
        <div className="sidebar-user">
          <div className="avatar avatar-small">{initiales}</div>
          <div className="user-copy"><strong>{user?.name}</strong><span>{user?.role}</span></div>
          <button className="logout-button" onClick={logout} aria-label="Déconnexion" title="Déconnexion">↪</button>
        </div>
      </aside>
      <main className="app-main">
        <header className="topbar">
          <div><span className="breadcrumb">Centre de services /</span><strong>{pageActuelle}</strong></div>
          <div className="topbar-status"><span className="status-dot" /> Service opérationnel</div>
        </header>
        <div className="content-wrap"><Outlet /></div>
      </main>
    </div>
  );
}
