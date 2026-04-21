import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Login from './pages/Login';
import Register from './pages/Register';
import Dashboard from './pages/Dashboard'; // Pastikan sudah di-import
import DetailBuku from './pages/DetailBuku';

function App() {
  return (
    <Router>
      <Routes>
        {/* Arahkan halaman awal ke login */}
        <Route path="/" element={<Navigate to="/login" />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/buku/:id" element={<DetailBuku />} />
      </Routes>
    </Router>
  );
}

export default App;