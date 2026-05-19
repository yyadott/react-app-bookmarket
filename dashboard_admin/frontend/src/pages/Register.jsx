import React, { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import "./Auth.css"; // Memanggil CSS yang baru dibuat

export default function Register() {
  const [formData, setFormData] = useState({
    nama: "",
    email: "",
    password: ""
  });
  const navigate = useNavigate();

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await fetch("http://localhost/DASHBOARD_ADMIN/backend/auth/register.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      });

      const result = await response.json();
      if (result.status === "success") {
        alert(result.message);
        navigate("/login"); // Pindah ke halaman login setelah berhasil
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
        <h2>Daftar Akun</h2>
        <p className="auth-subtitle">Buat akun untuk mengakses Dashboard.</p>
        
        <form className="auth-form" onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value={formData.nama} onChange={handleChange} placeholder="Masukkan nama Anda" required />
          </div>

          <div className="form-group">
            <label>Email</label>
            <input type="email" name="email" value={formData.email} onChange={handleChange} placeholder="Masukkan email Anda" required />
          </div>

          <div className="form-group">
            <label>Password</label>
            <input type="password" name="password" value={formData.password} onChange={handleChange} placeholder="Buat password" required />
          </div>

          <button type="submit" className="btn-auth">Daftar Sekarang</button>
        </form>

        <p className="auth-link">
          Sudah punya akun? <Link to="/login">Login di sini</Link>
        </p>
      </div>
    </div>
  );
}