import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import authService from '../services/authService';
import bgImage from '../assets/bg1.jpg';

const Register = () => {
  const [formData, setFormData] = useState({ nama: '', email: '', noHp: '', jenisKelamin: '', alamat: '', password: '' });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    // Sesuaikan authService.register untuk menerima objek formData utuh jika perlu
    const response = await authService.register(formData.nama, formData.email, formData.password);
    if (response.success) {
      alert(response.message);
      window.location.href = '/login';
    } else {
      alert(response.message);
    }
  };

  return (
    <div style={styles.container}>
      {/* Sisi Kiri: Teks Besar */}
      <div style={styles.leftSide}>
        <div style={styles.leftContent}>
          <h1 style={styles.mainTitle}>Buat Akun<br/>Anda</h1>
          <p style={styles.subTitle}>Silakan daftar untuk mulai berbelanja buku favorit, menyimpan riwayat pesanan, dan mendapatkan promo eksklusif dari Book Market.</p>
        </div>
      </div>

      {/* Sisi Kanan: Form Transparan (Field Lebih Banyak) */}
      <div style={styles.rightSide}>
        <div style={styles.authCard}>
          <h2 style={styles.cardTitle}>Registrasi</h2>
          <p style={styles.cardDesc}>Silakan lengkapi data diri Anda untuk membuat akun baru</p>
          
          <form onSubmit={handleFormSubmit} style={styles.form}>
            <div style={styles.inputGroup}>
              <label style={styles.label}>Nama</label>
              <input type="text" name="nama" onChange={handleChange} required style={styles.input} />
            </div>
            <div style={styles.inputGroup}>
              <label style={styles.label}>Email</label>
              <input type="email" name="email" onChange={handleChange} required style={styles.input} />
            </div>

            {/* 
            <div style={styles.inputGroup}>
              <label style={styles.label}>No. HP</label>
              <input type="text" name="noHp" onChange={handleChange} required style={styles.input} />
            </div>
            */}

            {/* 
            <div style={styles.inputGroup}>
              <label style={styles.label}>Jenis Kelamin</label>
              <select name="jenisKelamin" onChange={handleChange} required style={styles.input}>
                <option value="">Pilih...</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            */}

            {/* 
            <div style={styles.inputGroup}>
              <label style={styles.label}>Alamat</label>
              <input type="text" name="alamat" onChange={handleChange} required style={styles.input} />
            </div>
            */}

            <div style={styles.inputGroup}>
              <label style={styles.label}>Password</label>
              <input type="password" name="password" onChange={handleChange} required style={styles.input} />
            </div>

            <button type="submit" style={styles.button}>Daftar</button>
          </form>

          <p style={styles.footerText}>
            Sudah punya akun? <Link to="/login" style={styles.link}>Login</Link>
          </p>
        </div>
      </div>
    </div>
  );
};

// Css pada JS
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
    color: 'white',
    marginBottom: '20px'
  },
  subTitle: {
    fontSize: '18px',
    fontWeight: '400',
    color: 'black',
    lineHeight: '1.6',
    maxWidth: '500px',
  },

  rightSide: {
    flex: 1,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    padding: '20px 0'
  }, // Tambah padding agar scroll di card
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
    fontSize: '32px',
    fontWeight: '600',
    marginBottom: '5px',
    color:'black'
  },
  cardDesc: {
    fontSize: '14px',
    fontWeight: '300',
    marginBottom: '15px',
    color:'black'
  },
  form: {
    display: 'flex',
    flexDirection: 'column'
  },
  inputGroup: {
    marginBottom: '15px'
  },
  label: {
    fontSize: '13px',
    marginBottom: '5px',
    display: 'block',
    color:'black'
  },
  input: {
    width: '100%',
    padding: '10px 12px',
    background: 'rgba(255,255,255,0.8)',
    border: 'none',
    borderRadius: '6px',
    fontSize: '15px'
  },
  button: {
    width: '100%',
    padding: '12px',
    background: '#5A67D8',
    color: 'white',
    border: 'none',
    borderRadius: '8px',
    fontSize: '16px',
    fontWeight: '600',
    cursor: 'pointer',
    marginTop: '10px'
  },
  footerText: {
    textAlign: 'center',
    marginTop: '15px',
    fontSize: '13px',
    fontWeight: '300',
    color:'black'
  },
  link: {
    color: '#E53E3E',
    textDecoration: 'none',
    fontWeight: 'bold'
  }
};

export default Register;