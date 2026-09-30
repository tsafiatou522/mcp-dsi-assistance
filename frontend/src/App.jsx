import { Routes, Route } from 'react-router-dom'
import Login from './pages/Login.jsx'
import Register from './pages/Register.jsx'
import Dashboard from './pages/Dashboard.jsx'
import Demandes from './pages/Demandes.jsx'
import DemandeDetail from './pages/DemandeDetail.jsx'
import Equipements from './pages/Equipements.jsx'
import Administration from './pages/Administration.jsx'
import BaseConnaissances from './pages/BaseConnaissances.jsx'
import Problemes from './pages/Problemes.jsx'
import Layout from './components/Layout.jsx'
import RouteProtegee from './components/RouteProtegee.jsx'

function App() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route
        path="/"
        element={
          <RouteProtegee>
            <Layout />
          </RouteProtegee>
        }
      >
        <Route index element={<Dashboard />} />
        <Route path="demandes" element={<Demandes />} />
        <Route path="demandes/:id" element={<DemandeDetail />} />
        <Route path="base-connaissances" element={<BaseConnaissances />} />
        <Route path="problemes" element={<Problemes />} />
        <Route
          path="equipements"
          element={
            <RouteProtegee roles={['technicien', 'responsable', 'admin']}>
              <Equipements />
            </RouteProtegee>
          }
        />
        <Route
          path="administration"
          element={
            <RouteProtegee roles={['admin']}>
              <Administration />
            </RouteProtegee>
          }
        />
      </Route>
    </Routes>
  )
}

export default App
