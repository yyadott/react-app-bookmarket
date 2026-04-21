import React, { useState } from "react";
import { useParams, useNavigate } from "react-router-dom";

const books = [
  {
    id: 1,
    judul: "Ensiklopedia Kisah Planet Bumi",
    penulis: "ANNE RODNEY",
    tanggal: "30 Jun 2024",
    harga: 140000,
    rating: 4,
    stok: 121,
    penerbit: "Pustakawan Populer Gramedia",
    kategori: "Ensiklopedia",
    dimensi: "20 Cm X 25 Cm X 3 Cm",
    berat: "10g",
    nomorSeri: "978-1234-5678-90",
    sinopsis:
      "....",
    
},
  {
    id: 2,
    judul: "Habibie Story",
    penulis: "TOTO",
    tanggal: "12 Mar 2023",
    harga: 80000,
    rating: 5,
    stok: 45,
    penerbit: "Gramedia",
    kategori: "Biografi",
    dimensi: "15 Cm X 21 Cm X 2 Cm",
    berat: "8g",
    nomorSeri: "978-0000-1111-22",
    sinopsis: "Kisah hidup B.J. Habibie, dari masa kecil hingga menjadi Presiden RI ke-3.",
   
  },
  {
    id: 3,
    judul: "Ensiklopedia Dunia",
    penulis: "ARCTURUS",
    tanggal: "5 Jan 2024",
    harga: 60000,
    rating: 4,
    stok: 200,
    penerbit: "Mizan",
    kategori: "Ensiklopedia",
    dimensi: "20 Cm X 25 Cm X 4 Cm",
    berat: "12g",
    nomorSeri: "978-3333-4444-55",
    sinopsis: "Referensi lengkap pengetahuan dunia untuk anak dan remaja.",
    
  }
];

const related = [
  { judul: "Ensiklopedia Bumi Untuk Anak Cerdas", penulis: "Anna Claybourne", harga: 92000, rating: 4, terjual: 293, bg: "linear-gradient(135deg,#FFF3CD,#FFD980)" },
  { judul: "Matematika Untuk Anak Cerdas", penulis: "Lynn Huggins-Cooper", harga: 92000, rating: 4, terjual: 293, bg: "linear-gradient(135deg,#FFE0B2,#FFB74D)" },
  { judul: "Ensiklopedia Lengkap Pertamaku", penulis: "RENZO BARSDTTI", harga: 143200, rating: 4, terjual: 293, bg: "linear-gradient(135deg,#C8E6C9,#66BB6A)" },
  { judul: "Ensiklopedia Bumi", penulis: "Miles Kelly", harga: 252000, rating: 4, terjual: 293, bg: "linear-gradient(135deg,#FFCCBC,#FF7043)" },
];

const Stars = ({ count, size = "0.9rem" }) => (
  <div style={{ display: "flex", gap: 2 }}>
    {[1, 2, 3, 4, 5].map((i) => (
      <span key={i} style={{ color: i <= count ? "#F6C549" : "#ddd", fontSize: size }}>★</span>
    ))}
  </div>
);

const styles = {
  body: { minHeight: "100vh", backgroundColor: "#F2F4FC", fontFamily: "'DM Sans', sans-serif", color: "#1A1D35" },
  nav: {
    backgroundColor: "#4A5CDB", display: "flex", alignItems: "center",
    justifyContent: "space-between", padding: "0 40px", height: 60,
    boxShadow: "0 2px 16px rgba(74,92,219,.25)", position: "sticky", top: 0, zIndex: 100,
  },
  logo: { color: "#fff", fontSize: "1.2rem", fontWeight: 700, letterSpacing: 0.5, display: "flex", alignItems: "center", gap: 10 },
  navLinks: { display: "flex", gap: 28, listStyle: "none" },
  navLink: { color: "rgba(255,255,255,.8)", textDecoration: "none", fontSize: "0.88rem", fontWeight: 500 },
  navLinkActive: { color: "#fff", borderBottom: "2px solid #fff", paddingBottom: 2 },
  breadcrumb: { padding: "12px 40px", fontSize: "0.8rem", color: "#7B82A8" },
  page: { display: "flex", gap: 24, padding: "0 40px 40px", alignItems: "flex-start" },
  card: { flex: 1, background: "#fff", borderRadius: 18, padding: 28, boxShadow: "0 2px 20px rgba(74,92,219,.08)" },
  bookHero: { display: "flex", gap: 24, marginBottom: 28 },
  cover: {
    width: 148, height: 198, flexShrink: 0, borderRadius: 12, overflow: "hidden",
    background: "linear-gradient(135deg,#6B7AE8,#4A5CDB)",
    boxShadow: "4px 8px 24px rgba(74,92,219,.3)",
    display: "flex", alignItems: "center", justifyContent: "center", flexDirection: "column",
    color: "#fff", textAlign: "center", padding: 12, fontSize: "0.82rem", lineHeight: 1.4,
  },
  bookInfo: { flex: 1, display: "flex", flexDirection: "column", gap: 10, paddingTop: 4 },
  title: { fontSize: "1.45rem", fontWeight: 700, lineHeight: 1.3, color: "#1A1D35" },
  meta: { display: "flex", alignItems: "center", gap: 10, fontSize: "0.8rem", color: "#7B82A8" },
  badges: { display: "flex", gap: 8, flexWrap: "wrap", marginTop: 4 },
  badge: { padding: "4px 10px", borderRadius: 6, fontSize: "0.76rem", fontWeight: 500, border: "1px solid #E2E5F5" },
  priceRow: { display: "flex", alignItems: "center", justifyContent: "space-between", marginTop: 8 },
  price: { fontSize: "1.55rem", fontWeight: 700 },
  buyBtn: {
    background: "#4A5CDB", color: "#fff", border: "none", padding: "11px 22px",
    borderRadius: 10, fontSize: "0.93rem", fontWeight: 600, cursor: "pointer",
    boxShadow: "0 4px 14px rgba(74,92,219,.35)", display: "flex", alignItems: "center", gap: 8,
  },
  sectionTitle: { fontSize: "1rem", fontWeight: 700, marginBottom: 10, color: "#1A1D35" },
  sinopsis: { fontSize: "0.86rem", lineHeight: 1.8, color: "#555", marginBottom: 24 },
  grid: { display: "grid", gridTemplateColumns: "repeat(3,1fr)", border: "1px solid #E2E5F5", borderRadius: 12, overflow: "hidden" },
  cell: { padding: "12px 16px", borderRight: "1px solid #E2E5F5", borderBottom: "1px solid #E2E5F5" },
  cellLabel: { fontSize: "0.7rem", color: "#7B82A8", fontWeight: 500, marginBottom: 4, textTransform: "uppercase", letterSpacing: 0.4 },
  cellVal: { fontSize: "0.85rem", fontWeight: 600 },
  sidebar: { width: 272, flexShrink: 0 },
  sideTitle: { fontSize: "1.05rem", fontWeight: 700, marginBottom: 14 },
  relCard: {
    background: "#fff", borderRadius: 14, padding: 12, display: "flex", gap: 10,
    marginBottom: 10, cursor: "pointer", border: "1.5px solid transparent",
    boxShadow: "0 2px 12px rgba(74,92,219,.07)", transition: "all .2s",
  },
  thumb: { width: 58, height: 76, borderRadius: 8, flexShrink: 0, display: "flex", alignItems: "center", justifyContent: "center", fontSize: "1.5rem" },
};

export default function DetailBuku() {
  const { id } = useParams();
  const navigate = useNavigate();
  const book = books.find((b) => b.id == id) || books[0];
  const [hovered, setHovered] = useState(null);

  const detailItems = [
    { label: "Penerbit", val: book.penerbit },
    { label: "Stok Buku", val: book.stok },
    { label: "Kategori", val: book.kategori },
    { label: "Dimensi", val: book.dimensi },
    { label: "Berat", val: book.berat },
    { label: "Nomor Seri", val: book.nomorSeri },
  ];

  return (
    <div style={styles.body}>
      {/* NAVBAR */}
      <nav style={styles.nav}>
        <div style={styles.logo}>📖 BOOKMARKET</div>
        <ul style={styles.navLinks}>
          <li><a href="/" style={{ ...styles.navLink, ...styles.navLinkActive }}>HOME</a></li>
          <li><a href="#" style={styles.navLink}>History Transaksi</a></li>
        </ul>
        <div style={{ display: "flex", alignItems: "center", gap: 14 }}>
          <div style={{ background: "rgba(255,255,255,.15)", borderRadius: 8, padding: "6px 10px", color: "#fff", cursor: "pointer" }}>🛒</div>
          <div style={{ width: 34, height: 34, borderRadius: "50%", background: "rgba(255,255,255,.25)", border: "2px solid rgba(255,255,255,.5)", display: "flex", alignItems: "center", justifyContent: "center", fontSize: "1rem", cursor: "pointer" }}>👤</div>
        </div>
      </nav>

      <div style={styles.breadcrumb}>Detail Buku</div>

      <div style={styles.page}>
        {/* DETAIL CARD */}
        <div style={styles.card}>
          <div style={styles.bookHero}>
            <div style={styles.cover}>
              <div style={{ fontSize: "2.5rem", marginBottom: 8 }}>{book.emoji}</div>
              <div>{book.judul}</div>
            </div>
            <div style={styles.bookInfo}>
              <div style={styles.title}>{book.judul}</div>
              <div style={styles.meta}>
                <span>{book.penulis}</span>
                <span style={{ width: 4, height: 4, borderRadius: "50%", background: "#E2E5F5", display: "inline-block" }} />
                <span>{book.tanggal}</span>
              </div>
              <Stars count={book.rating} />
              <div style={styles.badges}>
                <span style={{ ...styles.badge, color: "#27AE60", background: "#F0FAF4", borderColor: "#C8EFD9" }}>✔ Tersedia</span>
                <span style={{ ...styles.badge, color: "#4A5CDB", background: "#EEF0FD" }}>📦 Fisik</span>
                
              </div>
              <div style={styles.priceRow}>
                <div style={styles.price}>Rp {book.harga.toLocaleString("id-ID")}</div>
                <button style={styles.buyBtn}>🛒 Beli Sekarang</button>
              </div>
            </div>
          </div>

          <div style={styles.sectionTitle}>Sinopsis</div>
          <div style={styles.sinopsis}>
            {book.sinopsis}{" "}
            
          </div>

          <div style={styles.sectionTitle}>Detail Buku</div>
          <div style={styles.grid}>
            {detailItems.map((item, i) => (
              <div key={i} style={{
                ...styles.cell,
                borderRight: (i + 1) % 3 === 0 ? "none" : "1px solid #E2E5F5",
                borderBottom: i >= 3 ? "none" : "1px solid #E2E5F5",
              }}>
                <div style={styles.cellLabel}>{item.label}</div>
                <div style={styles.cellVal}>{item.val}</div>
              </div>
            ))}
          </div>
        </div>

        {/* SIDEBAR */}
        <div style={styles.sidebar}>
          <div style={styles.sideTitle}>Cerita Serupa</div>
          {related.map((r, i) => (
            <div
              key={i}
              style={{ ...styles.relCard, borderColor: hovered === i ? "#4A5CDB" : "transparent" }}
              onMouseEnter={() => setHovered(i)}
              onMouseLeave={() => setHovered(null)}
            >
              <div style={{ ...styles.thumb, background: r.bg }}>{r.emoji}</div>
              <div style={{ flex: 1 }}>
                <div style={{ fontSize: "0.85rem", fontWeight: 700, lineHeight: 1.3, marginBottom: 3 }}>{r.judul}</div>
                <div style={{ fontSize: "0.73rem", color: "#7B82A8", marginBottom: 4 }}>{r.penulis}</div>
                <div style={{ display: "flex", alignItems: "center", gap: 6, marginBottom: 4 }}>
                  <Stars count={r.rating} size="0.68rem" />
                  <span style={{ fontSize: "0.68rem", color: "#7B82A8" }}>{r.terjual} Terjual</span>
                </div>
                <div style={{ fontSize: "0.88rem", fontWeight: 700 }}>Rp{r.harga.toLocaleString("id-ID")}</div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}