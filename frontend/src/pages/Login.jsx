import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import authService from '../services/authService';
import bgImage from '../assets/bg1.jpg'; // Pastikan gambar ada

const Login = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    const response = await authService.login(email, password);
    if (response.success) {
      alert("Selamat Datang!");
      window.location.href = '/dashboard';
    } else {
      alert(response.message);
    }
  };

  return (
    <div style={styles.container}>
      {/* Sisi Kiri: Teks Besar */}
      <div style={styles.leftSide}>
        <div style={styles.leftContent}>
          <h1 style={styles.mainTitle}>Masuk ke<br/>Akun Anda</h1>
          <p style={styles.subTitle}>Silakan login untuk melanjutkan belanja buku favorit, melihat riwayat pesanan, dan mendapatkan promo eksklusif dari Book Market.</p>
        </div>
      </div>

      {/* Sisi Kanan: Form Transparan */}
      <div style={styles.rightSide}>
        <div style={styles.authCard}>
          <h2 style={styles.cardTitle}>Masuk</h2>
          <p style={styles.cardDesc}>Silakan Masukkan Email dan Password yang benar!</p>
          
          <form onSubmit={handleFormSubmit} style={styles.form}>
            <div style={styles.inputGroup}>
              <label style={styles.label}>Email</label>
              <input 
                type="email" 
                value={email} 
                onChange={(e) => setEmail(e.target.value)} 
                required 
                style={styles.input}
              />
            </div>
            <div style={styles.inputGroup}>
              <label style={styles.label}>Password</label>
              <input 
                type="password" 
                value={password} 
                onChange={(e) => setPassword(e.target.value)} 
                required 
                style={styles.input}
              />
            </div>
            
            <div style={styles.extraActions}>
              <label style={styles.checkboxLabel}><input type="checkbox" /> Verifikasi</label>
              <a href="#" style={styles.forgotPass}>Lupa Kata Sandi</a>
            </div>

            <button type="submit" style={styles.button}>Login</button>
          </form>

          <p style={styles.footerText}>
            Belum punya akun? <Link to="/register" style={styles.link}>Daftar</Link>
          </p>
        </div>
      </div>
    </div>
  );
};

// CSS pada JS
const styles = {
  container: { 
    display: 'flex', 
    height: '100vh', 
    width: '100vw', 
    backgroundImage: `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${bgImage})`, 
    backgroundSize: 'cover', 
    backgroundPosition: 'center',
    backgroundRepeat: 'no-repeat'
  },
  
  // Sisi Kiri
  leftSide: {
    flex: 1,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    padding: '0 10%'
  },
  leftContent: {
    color: 'white'
  },
  mainTitle: {
    fontSize: '64px',
    fontWeight: '700',
    lineHeight: '1.1',
    color: '#ffffff',
    marginBottom: '20px'
  },
  subTitle: {
    fontSize: '18px',
    fontWeight: '400',
    color: 'white',
    lineHeight: '1.6',
    maxWidth: '500px',
    color:'black'
  },

  // Sisi Kanan
  rightSide: {
    flex: 1,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center'
  },
  authCard: {
    background: 'rgba(255, 255, 255, 0.2)',
    backdropFilter: 'blur(10px)',
    border: '1px solid rgba(255, 255, 255, 0.3)',
    padding: '40px',
    borderRadius: '24px',
    width: '85%',
    maxWidth: '550px',
    color: 'white',
    maxHeight: '90vh',
  },
  cardTitle: {
    fontSize: '36px',
    fontWeight: '600',
    marginBottom: '10px',
    color:'black'
  },
  cardDesc: {
    fontSize: '16px',
    fontWeight: '300',
    marginBottom: '20px',
    color:'black'
  },
  // Form
  form: {
    display: 'flex',
    flexDirection: 'column'
  },
  inputGroup: {
    marginBottom: '20px'
  },
  label: {
    fontSize: '14px',
    marginBottom: '8px',
    display: 'block',
    color:'black'
  },
  input: {
    width: '100%',
    padding: '12px 15px',
    background: 'rgba(255,255,255,0.8)',
    border: 'none',
    borderRadius: '8px',
    fontSize: '16px'
  },
  extraActions: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    fontSize: '14px',
    marginBottom: '10px'
  },
  checkboxLabel: {
    display: 'flex',
    alignItems: 'center',
    gap: '8px',
    color:'black'
  },
  forgotPass: {
    color: 'white',
    textDecoration: 'none',
    opacity: '0.8',
    transition: 'opacity 0.3s',
    color:'black'
  },
  button: {
    width: '100%',
    padding: '10px',
    background: '#5A67D8',
    color: 'white',
    border: 'none',
    borderRadius: '8px',
    fontSize: '18px',
    fontWeight: '600',
    cursor: 'pointer',
    transition: 'background 0.3s'
  },
  footerText: {
    textAlign: 'center',
    marginTop: '20px',
    fontSize: '14px',
    fontWeight: '300',
    color:'black'
  },
  link: {
    color: '#ff0000',
    textDecoration: 'none',
    fontWeight: 'bold' }
};

export default Login;