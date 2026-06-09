import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Dashboard from './pages/Dashboard';
import Login from './pages/Login';
import Register from './pages/Register';
import DetailBuku from './pages/DetailBuku';

function App() {
  return (
    <Router>
      <Routes>
        {/* Arahkan halaman awal ke dashboard */}
        <Route path="/" element={<Navigate to="/dashboard" />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/buku/:id" element={<DetailBuku />} />
      </Routes>
    </Router>
  );
}

export default App;