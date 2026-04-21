import React, { useEffect, useState } from 'react';
import bookService from '../services/bookService';
import heroImage from '../assets/hero2.png'; 
import { useNavigate } from "react-router-dom";

const Dashboard = () => {
  const [books, setBooks] = useState([]);
  const navigate = useNavigate();

  // Data Dummy Manual sesuai UI yang diupload
  const dummyBooks = [
    {
      id_buku: 1,
      judul: "Biografi Lengkap Negarawan Sejati",
      penulis: "Anom Whani Wicaksono",
      harga: 63750,
      img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6A7R5w7t0Zz1Z1aB-G7-n5C4p_n9z5H-D_Q&s"
    },
    {
      id_buku: 2,
      judul: "Habibie : Dari Malari Sampai Reformasi",
      penulis: "Raden Toto Sugiharto",
      harga: 80750,
      img: "https://perpustakaan.gunungkidulkab.go.id/opac/lib/minigalnano/createthumb.php?filename=images/docs/Habibie.jpg&width=200"
    },
    {
      id_buku: 3,
      judul: "Ensiklopedia Atlas Dunia Junior",
      penulis: "Arcturus Publishing",
      harga: 63200,
      img: "https://cdn.gramedia.com/uploads/items/9786022495680_Ensiklopedia-Atlas-Dunia-Junior.jpg"
    },
    {
      id_buku: 4,
      judul: "Ensiklopedia Kisah Planet Bumi",
      penulis: "ANNE ROONEY",
      harga: 140000,
      img: "https://m.media-amazon.com/images/I/91eA3b6B89L._AC_UF1000,1000_QL80_.jpg"
    },
    {
      id_buku: 5,
      judul: "Komik Kecil-Kecil Punya Karya (KKPK)",
      penulis: "Adnin Zoqiyah, Dkk",
      harga: 33150,
      img: "https://m.media-amazon.com/images/I/51wXkG2lF4L._SL500_.jpg"
    },
    {
      id_buku: 6,
      judul: "Komik Dongeng Mancanegara",
      penulis: "Cyan Agency",
      harga: 55250,
      img: "https://cdn.gramedia.com/uploads/items/img041_8E1D3V8.jpg"
    }
  ];

  useEffect(() => {
    const fetchBooks = async () => {
      try {
        const data = await bookService.getBooks();
        // Jika DB kosong, gunakan data dummy
        setBooks(data.length > 0 ? data : dummyBooks);
      } catch (e) {
        setBooks(dummyBooks); // Fallback ke dummy jika error koneksi
      }
    };
    fetchBooks();
  }, []);

  return (
    <div style={styles.dashboardContainer}>
      {/* 1. Header & Blue Curve Area */}
      <div style={styles.blueHeaderSection}>
        <header style={styles.header}>
          <div style={styles.logo}>BOOK MARKET 🛒</div>
          <nav style={styles.nav}>
            <a href="#" style={styles.activeLink}>HOME</a>
            <a href="#" style={styles.navLink}>History Transaksi</a>
            <div style={styles.profileIcon}>👤</div>
            <a href="/login" style={styles.loginBtn}>Login</a>
          </nav>
        </header>

        <section style={styles.heroSection}>
          <div style={styles.heroText}>
            <h1 style={styles.heroTitle}>TEMUKAN BUKU FAVORITMU DI BOOK MARKET</h1>
            <p style={styles.heroDesc}>
              Book Market Menyediakan Berbagai Koleksi Buku Berkualitas Mulai Dari Novel, 
              Buku Pendidikan, Bisnis, Pengembangan Diri, Hingga Buku Anak. 
              Nikmati Pengalaman Belanja Buku Yang Mudah, Cepat, Dan Terpercaya.
            </p>
            <div style={styles.searchContainer}>
              <span style={styles.searchIcon}>🔍</span>
              <input type="text" placeholder="Cari Buku" style={styles.searchInput} />
            </div>
          </div>
          <div style={styles.heroImageWrapper}>
            <img src={heroImage} alt="Hero" style={styles.heroImg} />
          </div>
        </section>
      </div>

      {/* 2. Main Content */}
      <div style={styles.mainContent}>
        {/* Sidebar */}
        <aside style={styles.sidebar}>
          <h3 style={styles.sidebarTitle}>KATEGORI</h3>
          <ul style={styles.categoryList}>
            <li style={styles.activeCategory}>Semua Genre</li>
            <li>Biografi / Naskah</li>
            <li>Ensiklopedia</li>
            <li>Kamus</li>
            <li>Kitab Suci</li>
            <li>Komik / Manga</li>
            <li>Majalah</li>
            <li>Novel</li>
            <li>Panduan / Hobi</li>
            <li>Pelajaran / Perkuliahan</li>
            <li>Lainnya</li>
          </ul>

          <h3 style={styles.sidebarTitle}>URUTAN HARGA</h3>
          <div style={styles.checkboxGroup}>
            <label style={styles.label}><input type="checkbox" /> Rendah → Tinggi</label>
            <label style={styles.label}><input type="checkbox" /> Tinggi → Rendah</label>
          </div>
          <h3 style={styles.sidebarTitle}>TOTAL TERJUAL</h3>
        </aside>

        {/* Catalog Grid */}
        <div style={styles.catalogWrapper}>
          <section style={styles.catalogGrid}>
            {books.map((book) => (
              <div 
                key={book.id_buku} 
                style={{ ...styles.bookCard, cursor: "pointer" }}
                onClick={() => navigate(`/buku/${book.id_buku}`)}
              >
                <div style={styles.bookImageArea}>
                   <img 
                    src={book.img} 
                    alt={book.judul} 
                    style={{ width: '100%', height: '100%', borderRadius: '8px', objectFit: 'cover' }} 
                    onError={(e) => { e.target.src = 'https://via.placeholder.com/150'; }} // Fallback jika link gambar mati
                   />
                </div>
                <div style={styles.bookInfo}>
                  <div>
                    <h4 style={styles.bookTitle}>{book.judul}</h4>
                    <p style={styles.author}>{book.penulis}</p>
                    <div style={styles.rating}>
                        ⭐⭐⭐⭐ <span style={styles.terjual}>4000 Terjual</span>
                    </div>
                  </div>
                  <div>
                    <p style={styles.price}>Rp{parseInt(book.harga).toLocaleString('id-ID')}</p>
                    <button style={styles.cartBtn}>Masukkan Keranjang</button>
                  </div>
                </div>
              </div>
            ))}
          </section>

          {/* Pagination */}
          <div style={styles.pagination}>
              <button style={styles.pagBtn}>&lt;</button>
              <button style={{...styles.pagBtn, ...styles.pagActive}}>1</button>
              <button style={styles.pagBtn}>2</button>
              <button style={styles.pagBtn}>3</button>
              <button style={styles.pagBtn}>4</button>
              <button style={styles.pagBtn}>...</button>
              <button style={styles.pagBtn}>21</button>
              <button style={styles.pagBtn}>&gt;</button>
          </div>
        </div>
      </div>

      {/* 3. Footer */}
      <footer style={styles.footer}>
        <p>2025 BUKUPEDIA</p>
        <div style={styles.footerLinks}>
          <a href="#" style={{color:'white'}}>Home</a>
          <a href="#" style={{color:'white'}}>Riwayat Transaksi</a>
        </div>
      </footer>
    </div>
  );
};

// --- STYLES (Diperbarui untuk Image Area) ---
const styles = {
  dashboardContainer: { backgroundColor: '#f5f7fb', minHeight: '100vh', width: '100%', overflowX: 'hidden' },
  blueHeaderSection: { 
    backgroundColor: 'white', 
    backgroundImage: 'radial-gradient(circle at top right, #5A67D8 40%, transparent 40.5%)',
    minHeight: '500px',
    padding: '0 8%'
  },
  header: { display: 'flex', justifyContent: 'space-between', padding: '25px 0', alignItems: 'center' },
  logo: { fontSize: '24px', fontWeight: 'bold', color: '#333' },
  nav: { display: 'flex', gap: '30px', alignItems: 'center' },
  navLink: { textDecoration: 'none', color: 'white', fontSize: '14px' },
  activeLink: { textDecoration: 'none', color: '#5A67D8', borderBottom: '2px solid #5A67D8', paddingBottom: '5px', fontSize: '14px', fontWeight: 'bold' },
  profileIcon: { backgroundColor: '#FFD700', borderRadius: '50%', padding: '8px 12px', fontSize: '18px' },
  loginBtn: { backgroundColor: '#FFD700', borderRadius: '8px', padding: '5px 15px', color: '#333', cursor: 'pointer', textDecoration: 'none', fontWeight: 'bold' },

  heroSection: { display: 'flex', paddingTop: '40px', gap: '40px', alignItems: 'center' },
  heroText: { flex: 1.2 },
  heroTitle: { fontSize: '48px', fontWeight: '800', color: '#1a1a1a', marginBottom: '20px', maxWidth: '600px' },
  heroDesc: { fontSize: '16px', color: '#555', marginBottom: '35px', lineHeight: '1.6' },
  searchContainer: { display: 'flex', alignItems: 'center', backgroundColor: '#f0f2f5', borderRadius: '8px', padding: '10px 20px', width: '70%', boxShadow: '0 2px 4px rgba(0,0,0,0.05)' },
  searchIcon: { marginRight: '10px', color: '#888' },
  searchInput: { border: 'none', background: 'transparent', width: '100%', outline: 'none', fontSize: '16px' },
  heroImageWrapper: { flex: 1 },
  heroImg: { width: '100%', borderRadius: '30px' },

  mainContent: { display: 'flex', padding: '60px 8%', gap: '50px' },
  sidebar: { width: '250px' },
  sidebarTitle: { fontSize: '16px', fontWeight: 'bold', color: '#333', marginBottom: '15px' },
  categoryList: { listStyle: 'none', padding: 0, marginBottom: '30px', lineHeight: '2.5', color: '#666' },
  activeCategory: { backgroundColor: 'white', color: '#5A67D8', padding: '5px 15px', borderRadius: '5px', fontWeight: 'bold', boxShadow: '0 2px 5px rgba(0,0,0,0.05)' },
  label: { display: 'block', fontSize: '14px', color: '#555', marginBottom: '10px' },
  
  catalogWrapper: { flex: 1 },
  catalogGrid: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '30px' },
  bookCard: { display: 'flex', background: 'white', borderRadius: '15px', padding: '20px', boxShadow: '0 4px 15px rgba(0,0,0,0.03)', transition: 'transform 0.2s' },
  bookImageArea: { width: '130px', height: '180px', borderRadius: '10px', backgroundColor: '#f0f0f0', flexShrink: 0, boxShadow: '2px 2px 10px rgba(0,0,0,0.1)' },
  bookInfo: { marginLeft: '20px', display: 'flex', flexDirection: 'column', justifyContent: 'space-between' },
  bookTitle: { fontSize: '16px', fontWeight: 'bold', color: '#333', marginBottom: '5px', lineHeight: '1.3' },
  author: { fontSize: '12px', color: '#888', marginBottom: '8px' },
  rating: { fontSize: '12px', color: '#f1c40f', marginBottom: '10px' },
  terjual: { color: '#bbb', marginLeft: '5px' },
  price: { fontSize: '18px', fontWeight: 'bold', color: '#333', marginBottom: '10px' },
  cartBtn: { border: '1px solid #f3e5d8', backgroundColor: '#fffaf5', color: '#8d6e63', padding: '8px 12px', borderRadius: '8px', cursor: 'pointer', fontSize: '12px', width: '100%' },

  pagination: { display: 'flex', justifyContent: 'center', marginTop: '50px', gap: '10px' },
  pagBtn: { border: 'none', background: 'white', width: '35px', height: '35px', borderRadius: '50%', cursor: 'pointer', color: '#888', boxShadow: '0 2px 4px rgba(0,0,0,0.05)' },
  pagActive: { backgroundColor: '#5A67D8', color: 'white' },

  footer: { backgroundColor: '#5A67D8', padding: '25px 8%', display: 'flex', justifyContent: 'space-between', color: 'white', fontSize: '13px' },
  footerLinks: { display: 'flex', gap: '30px' }
};

export default Dashboard;