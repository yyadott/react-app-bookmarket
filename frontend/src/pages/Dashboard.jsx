import { useEffect, useState } from 'react';
import bookService from '../services/bookService';
import heroImage from '../assets/hero2.png';
import { useNavigate } from 'react-router-dom';

const Dashboard = () => {
  // 1. State untuk navigasi dan data utama
  const [books, setBooks] = useState([]);
  const navigate = useNavigate();

  // 2. State untuk autentikasi
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [userName, setUserName] = useState('');
  
  // 3. State untuk fitur filter & pagination
  const [selectedCategory, setSelectedCategory] = useState('Semua Genre'); // Nama disamakan dengan logika filter
  const [currentPage, setCurrentPage] = useState(1);

  // 4. State untuk fitur sorting harga
  const [sortOrder, setSortOrder] = useState('');

  const dummyBooks = [
    {
    id_buku: 1,
    judul: "Biografi Lengkap Negarawan Sejati",
    penulis: "Anom Whani Wicaksono",
    harga: 63750,
    kategori: "Ensiklopedia", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1544640808-32ca72ac7f37?w=400"
  },
  {
    id_buku: 2,
    judul: "Habibie : Dari Malari Sampai Reformasi",
    penulis: "Raden Toto Sugiharto",
    harga: 80750,
    kategori: "Kamus", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1589998059171-988d887df646?w=400"
  },
  {
    id_buku: 3,
    judul: "Ensiklopedia Atlas Dunia Junior",
    penulis: "Arcturus Publishing",
    harga: 63200,
    kategori: "Komik / Manga", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=400"
  },
  {
    id_buku: 4,
    judul: "Biografi Lengkap Negarawan Sejati",
    penulis: "Anom Whani Wicaksono",
    harga: 63750,
    kategori: "Kamus", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1544640808-32ca72ac7f37?w=400"
  },
  {
    id_buku: 5,
    judul: "Habibie : Dari Malari Sampai Reformasi",
    penulis: "Raden Toto Sugiharto",
    harga: 80750,
    kategori: "Biografi / Naskah", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1589998059171-988d887df646?w=400"
  },
  {
    id_buku: 6,
    judul: "Ensiklopedia Atlas Dunia Junior",
    penulis: "Arcturus Publishing",
    harga: 63200,
    kategori: "Ensiklopedia", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=400"
  },
  {
    id_buku: 7,
    judul: "Biografi Lengkap Negarawan Sejati",
    penulis: "Anom Whani Wicaksono",
    harga: 63750,
    kategori: "Kamus", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1544640808-32ca72ac7f37?w=400"
  },
  {
    id_buku: 8,
    judul: "Habibie : Dari Malari Sampai Reformasi",
    penulis: "Raden Toto Sugiharto",
    harga: 80750,
    kategori: "Biografi / Naskah", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1589998059171-988d887df646?w=400"
  },
  {
    id_buku: 9,
    judul: "Ensiklopedia Atlas Dunia Junior",
    penulis: "Arcturus Publishing",
    harga: 63200,
    kategori: "Ensiklopedia", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=400"
  },
  {
    id_buku: 10,
    judul: "Biografi Lengkap Negarawan Sejati",
    penulis: "Anom Whani Wicaksono",
    harga: 63750,
    kategori: "Kamus", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1544640808-32ca72ac7f37?w=400"
  },
  {
    id_buku: 11,
    judul: "Habibie : Dari Malari Sampai Reformasi",
    penulis: "Raden Toto Sugiharto",
    harga: 80750,
    kategori: "Biografi / Naskah", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1589998059171-988d887df646?w=400"
  },
  {
    id_buku: 12,
    judul: "Ensiklopedia Atlas Dunia Junior",
    penulis: "Arcturus Publishing",
    harga: 63200,
    kategori: "Ensiklopedia", // Tambahkan ini
    img: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=400"
  }
  ];

  const categories = ['Semua Genre', 'Biografi / Naskah', 'Ensiklopedia', 'Kamus', 'Kitab Suci', 'Komik / Manga', 'Majalah', 'Novel', 'Panduan / Hobi', 'Pelajaran / Perkuliahan', 'Lainnya'];

  useEffect(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
      try {
        const user = JSON.parse(userData);
        setIsLoggedIn(true);
        setUserName(user.nama || 'User');
      } catch (error) {
        localStorage.removeItem('user');
      }
    }
    const fetchBooks = async () => {
      try {
        const data = await bookService.getBooks();
        setBooks(data.length > 0 ? data : dummyBooks);
      } catch (e) {
        setBooks(dummyBooks);
      }
    };
    fetchBooks();
  }, []);

  const handleLogout = () => {
    if (window.confirm("Apakah Anda yakin ingin logout?")) {
      localStorage.removeItem('user');
      setIsLoggedIn(false);
      setUserName('');
      navigate('/login');
    }
  };

  const StarRating = ({ rating = 4 }) => (
    <div style={styles.stars}>
      {[1, 2, 3, 4, 5].map(i => (
        <span key={i} style={{ color: i <= rating ? '#f5a623' : '#ddd', fontSize: '13px' }}>★</span>
      ))}
      <span style={styles.soldText}>4000 Terjual</span>
    </div>
  );


 // --- PINDAHKAN BLOK INI KE BAWAH ---
const booksPerPage = 10;

// 1. Filter berdasarkan kategori
const filteredBooks = selectedCategory === 'Semua Genre'
  ? books 
  : books.filter(book => book.kategori === selectedCategory);

// 2. Tambahkan Logika Urutan Harga (Sorting)
const sortedBooks = [...filteredBooks].sort((a, b) => {
  if (sortOrder === 'rendah') {
    return a.harga - b.harga;
  } else if (sortOrder === 'tinggi') {
    return b.harga - a.harga;
  }
  return 0;
});

// 3. Hitung total halaman berdasarkan hasil sortedBooks
const totalPages = Math.ceil(sortedBooks.length / booksPerPage);

// 4. BARU KEMUDIAN buat daftar nomor halaman (displayPages)
// Letakkan ini setelah totalPages dihitung agar tidak ERROR
const displayPages = totalPages > 3 
  ? [1, 2, 3] 
  : Array.from({ length: totalPages }, (_, i) => i + 1);

// 5. Potong data untuk ditampilkan
const currentBooks = sortedBooks.slice(
  (currentPage - 1) * booksPerPage, 
  currentPage * booksPerPage
);

  return (
    <div style={styles.dashboardContainer}>
      {/* HEADER */}
      <div style={styles.blueHeaderSection}>
        <header style={styles.header}>
          <div style={styles.logo}>BOOK MARKET 🛒</div>
          <nav style={styles.nav}>
            <a href="/dashboard" style={styles.activeLink}>HOME</a>
            <a href="#" style={styles.navLink}>History Transaksi</a>
            {isLoggedIn ? (
              <>
                <div style={styles.profileIcon} title={userName}>{userName.charAt(0).toUpperCase()}</div>
                <button onClick={handleLogout} style={styles.loginBtn}>Logout</button>
              </>
            ) : (
              <>
                <div style={styles.profileIcon}>👤</div>
                <button onClick={() => navigate('/login')} style={styles.loginBtn}>Login</button>
              </>
            )}
          </nav>
        </header>

        <section style={styles.heroSection}>
          <div style={styles.heroText}>
            <h1 style={styles.heroTitle}>TEMUKAN BUKU FAVORITMU DI BOOK MARKET</h1>
            <p style={styles.heroDesc}>Book Market Menyediakan Berbagai Koleksi Buku Berkualitas...</p>
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

      {/* MAIN CONTENT */}
      <div style={styles.mainContent}>
        {/* ===== SIDEBAR ===== */}
        <aside style={styles.sidebar}>
          <h3 style={styles.sidebarTitle}>KATEGORI</h3>
          <ul style={styles.categoryList}>
  {['Semua Genre', 'Biografi / Naskah', 'Ensiklopedia', 'Kamus', 'Komik / Manga'].map((cat) => (
    <li 
      key={cat}
      onClick={() => {
        setSelectedCategory(cat); // Update kategori yang dipilih
        setCurrentPage(1);        // Reset ke halaman 1 saat kategori berubah
      }}
      style={selectedCategory === cat ? styles.activeCategory : styles.categoryItem}
    >
      {cat}
    </li>
  ))}
</ul>

    <h3 style={styles.sidebarTitle}>URUTAN HARGA</h3>
<div style={styles.checkboxGroup}>
  <label style={styles.label}>
    <input 
      type="checkbox" 
      style={{ marginRight: '8px' }} 
      checked={sortOrder === 'rendah'}
      onChange={() => {
        setSortOrder(sortOrder === 'rendah' ? '' : 'rendah');
        setCurrentPage(1); // Reset ke halaman 1 saat urutan berubah
      }}
    /> 
    Rendah → Tinggi
  </label>
  <label style={styles.label}>
    <input 
      type="checkbox" 
      style={{ marginRight: '8px' }} 
      checked={sortOrder === 'tinggi'}
      onChange={() => {
        setSortOrder(sortOrder === 'tinggi' ? '' : 'tinggi');
        setCurrentPage(1);
      }}
    /> 
    Tinggi → Rendah
  </label>
</div>


          <h3 style={{ ...styles.sidebarTitle, marginTop: '20px' }}>TOTAL TERJUAL</h3>
        </aside>

        {/* ===== CATALOG ===== */}
       <div style={styles.catalogWrapper}>
          <section style={styles.catalogGrid}>
          {currentBooks.map((book) => (
          <div key={book.id_buku} style={styles.bookCard}>
                {/* Gambar Buku */}
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
                    style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                    onError={(e) => { e.target.onerror = null; e.target.src = 'https://placehold.co/100x140?text=No+Image'; }}
                  />
                </div>

                {/* Info Buku */}
                <div style={styles.bookInfo}>
                  <div>
                    <h4 style={styles.bookTitle}>{book.judul}</h4>
                    <p style={styles.author}>{book.penulis}</p>
                    <StarRating rating={4} />
                  </div>
                  <div style={styles.cardFooter}>
                    <p style={styles.price}>Rp{parseInt(book.harga).toLocaleString('id-ID')}</p>
                    <button style={styles.cartBtn}>Masukkan Keranjang</button>
                  </div>
                </div>
              </div>
            ))}
          </section>

         {/* Pagination */}
<div style={styles.pagination}>
  {/* Tombol Panah Kiri (Back) */}
  <button 
    onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
    style={styles.pagBtn}
    disabled={currentPage === 1}
  >
    &lt;
  </button>

  {/* Nomor Halaman Dinamis */}
  {displayPages.map((page) => (
    <button
      key={page}
      onClick={() => setCurrentPage(page)}
      style={currentPage === page ? { ...styles.pagBtn, ...styles.pagActive } : styles.pagBtn}
    >
      {page}
    </button>
  ))}

  {/* Tampilkan "..." jika total halaman lebih dari 3 */}
  {totalPages > 3 && <span style={{ color: '#888', margin: '0 5px' }}>...</span>}

  {/* Tombol Panah Kanan (Next) */}
  <button 
    onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
    style={styles.pagBtn}
    disabled={currentPage === totalPages || totalPages === 0}
  >
    &gt;
  </button>
</div>
        </div>
      </div>

      {/* FOOTER */}
      <footer style={styles.footer}>
        <p>2025 BUKUPEDIA</p>
        <div style={styles.footerLinks}>
          <a href="/dashboard" style={{ color: 'white' }}>Home</a>
          <a href="#" style={{ color: 'white' }}>Riwayat Transaksi</a>
        </div>
      </footer>
    </div>
  );
};

const styles = {
  dashboardContainer: { backgroundColor: '#f4f6fa', minHeight: '100vh', width: '100%', overflowX: 'hidden', fontFamily: "'Segoe UI', sans-serif" },

  // HEADER
  blueHeaderSection: { backgroundColor: '#ffffff', backgroundImage: 'radial-gradient(circle at top right, #5A67D8 40%, transparent 40.5%)', minHeight: '500px', padding: '0 8%' },
  header: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '25px 0' },
  logo: { fontSize: '24px', fontWeight: 'bold', color: '#333' },
  nav: { display: 'flex', alignItems: 'center', gap: '30px' },
  navLink: { textDecoration: 'none', color: '#ffffff', fontSize: '14px' },
  activeLink: { textDecoration: 'none', color: '#ffffff', borderBottom: '2px solid white', paddingBottom: '5px', fontSize: '14px', fontWeight: 'bold' },
  profileIcon: { backgroundColor: '#FFD700', borderRadius: '50%', width: '40px', height: '40px', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '18px', fontWeight: 'bold', color: '#333' },
  loginBtn: { backgroundColor: '#FFD700', color: '#333', borderRadius: '8px', padding: '8px 20px', fontSize: '14px', fontWeight: 'bold', border: 'none', cursor: 'pointer' },

  // HERO
  heroSection: { display: 'flex', alignItems: 'center', paddingTop: '40px', gap: '40px' },
  heroText: { flex: 1.2 },
  heroTitle: { fontSize: '48px', fontWeight: '800', color: '#1a1a1a', lineHeight: '1.2', marginBottom: '20px' },
  heroDesc: { fontSize: '16px', color: '#555', lineHeight: '1.6', marginBottom: '35px' },
  searchContainer: { display: 'flex', alignItems: 'center', backgroundColor: '#f0f2f5', borderRadius: '8px', padding: '10px 20px', width: '70%' },
  searchIcon: { marginRight: '10px', color: '#888' },
  searchInput: { border: 'none', background: 'transparent', width: '100%', outline: 'none', fontSize: '16px' },
  heroImageWrapper: { flex: 1 },
  heroImg: { width: '100%', borderRadius: '30px' },

  // LAYOUT
  mainContent: { display: 'flex', padding: '50px 8%', gap: '40px' },

  // ===== SIDEBAR (DIPERBARUI) =====
  sidebar: { width: '210px', flexShrink: 0 },
  sidebarTitle: { fontSize: '13px', fontWeight: '700', color: '#1a1a2e', letterSpacing: '0.5px', marginBottom: '12px', textTransform: 'uppercase' },
  categoryList: { listStyle: 'none', padding: 0, marginBottom: '28px' },
  categoryItem: {
    fontSize: '13px',
    color: '#666',
    padding: '8px 12px',
    borderRadius: '6px',
    cursor: 'pointer',
    marginBottom: '2px',
    transition: 'background 0.15s',
  },
  activeCategory: {
    fontSize: '13px',
    color: '#5A67D8',
    fontWeight: '600',
    padding: '8px 12px',
    borderRadius: '6px',
    cursor: 'pointer',
    marginBottom: '2px',
    backgroundColor: '#ffffff',
    border: '1.5px solid #e0e3ff',
    boxShadow: '0 1px 4px rgba(90,103,216,0.07)',
  },
  checkboxGroup: { display: 'flex', flexDirection: 'column', gap: '8px' },
  label: { display: 'flex', alignItems: 'center', fontSize: '13px', color: '#555', cursor: 'pointer' },

  // ===== CATALOG (DIPERBARUI) =====
  catalogWrapper: { flex: 1 },
  catalogGrid: {
    display: 'grid',
    gridTemplateColumns: 'repeat(2, 1fr)',
    gap: '18px',
    marginBottom: '30px',
  },
  bookCard: {
    display: 'flex',
    backgroundColor: '#ffffff',
    borderRadius: '14px',
    padding: '16px',
    boxShadow: '0 1px 4px rgba(0,0,0,0.05)',
    border: '1px solid #eee',
    gap: '14px',
  },
  bookImageArea: {
    width: '100px',
    height: '140px',
    borderRadius: '8px',
    backgroundColor: '#dde2f0',
    flexShrink: 0,
    overflow: 'hidden',
  },
  bookInfo: {
    display: 'flex',
    flexDirection: 'column',
    justifyContent: 'space-between',
    flex: 1,
    minWidth: 0,
  },
  bookTitle: { fontSize: '14px', fontWeight: '700', color: '#1a1a2e', lineHeight: '1.35', marginBottom: '4px' },
  author: { fontSize: '12px', color: '#999', marginBottom: '6px' },
  stars: { display: 'flex', alignItems: 'center', gap: '1px', marginBottom: '8px' },
  soldText: { fontSize: '11px', color: '#aaa', marginLeft: '6px' },
  cardFooter: {},
  price: { fontSize: '16px', fontWeight: '700', color: '#1a1a2e', marginBottom: '8px' },
  cartBtn: {
    width: '100%',
    padding: '8px 10px',
    backgroundColor: '#fff9f5',
    color: '#8d6e63',
    border: '1px solid #f3e5d8',
    borderRadius: '7px',
    cursor: 'pointer',
    fontSize: '12px',
    fontWeight: '500',
  },

  // PAGINATION (DIPERBARUI)
  pagination: { display: 'flex', gap: '6px', justifyContent: 'center', alignItems: 'center', marginTop: '10px' },
  pagBtn: {
    width: '34px',
    height: '34px',
    borderRadius: '50%',
    border: '1.5px solid #e0e3ff',
    backgroundColor: '#fff',
    color: '#555',
    fontSize: '13px',
    cursor: 'pointer',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
  },
  pagActive: { backgroundColor: '#5A67D8', color: '#fff', borderColor: '#5A67D8', fontWeight: '700' },

  // FOOTER
 footer: { backgroundColor: '#5A67D8', padding: '25px 8%', display: 'flex', justifyContent: 'space-between', color: 'white', fontSize: '13px' },
  footerLinks: { display: 'flex', gap: '30px' }
};

export default Dashboard;