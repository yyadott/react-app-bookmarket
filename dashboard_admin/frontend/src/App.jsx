import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
// Pastikan semua file ini di-import dengan benar!
import Login from "./pages/Login";
import Register from "./pages/Register";
import Dashboard from "./pages/Dashboard";
import Produk from "./pages/Produk";
import User from "./pages/User";
import Transaksi from "./pages/Transaksi";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* Route default, arahkan ke login */}
        <Route path="/" element={<Navigate to="/login" replace />} />
        
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        
        {/* Route Dashboard & Menu Sidebar */}
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/produk" element={<Produk />} />
        <Route path="/user" element={<User />} />
        <Route path="/transaksi" element={<Transaksi />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;