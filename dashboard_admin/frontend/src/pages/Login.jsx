import React, { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import "./Auth.css"; // Memanggil CSS yang SAMA dengan Register

export default function Login() {
  const [formData, setFormData] = useState({ email: "", password: "" });
  const navigate = useNavigate();

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await fetch("http://localhost/DASHBOARD_ADMIN/backend/auth/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      });

      const result = await response.json();
      if (result.status === "success") {
        alert(result.message);
        
        // PERBAIKAN DI SINI: Menyimpan dengan key "username" agar cocok dengan Sidebar
        localStorage.setItem("username", result.data.nama); 
        
        navigate("/dashboard");
      } else {
        alert("Gagal: " + result.message);
      }
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className="auth-container">
      <div className="auth-card">
        <h2>Selamat Datang</h2>
        <p className="auth-subtitle">Login untuk masuk ke Dashboard Admin.</p>
        
        <form className="auth-form" onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Email</label>
            <input 
              type="email" 
              name="email" 
              value={formData.email} 
              onChange={handleChange} 
              placeholder="Masukkan email Anda" 
              required 
            />
          </div>

          <div className="form-group">
            <label>Password</label>
            <input 
              type="password" 
              name="password" 
              value={formData.password} 
              onChange={handleChange} 
              placeholder="Masukkan password" 
              required 
            />
          </div>

          <button type="submit" className="btn-auth">Login</button>
        </form>

        <p className="auth-link">
          Belum punya akun? <Link to="/register">Daftar di sini</Link>
        </p>
      </div>
    </div>
  );
}