import React, { useState, useEffect } from "react";
import Sidebar from "../components/Sidebar";
import "./Produk.css"; // Menggunakan file CSS yang sama untuk konsistensi

export default function Transaksi() {
  const [transactions, setTransactions] = useState([]);
  const [filterDate, setFilterDate] = useState({ start: "", end: "" });
  
  // State untuk menyimpan ringkasan laporan
  const [summary, setSummary] = useState({
    totalPendapatan: 0,
    totalTransaksi: 0,
    bukuTerjual: 0
  });

  // Fungsi mengambil data dari Backend PHP
  const fetchTransaksi = async () => {
    try {
      // Sesuaikan URL ini dengan file PHP backend transaksi Anda nanti
      const response = await fetch("http://localhost/DASHBOARD_ADMIN/backend/transaksi/get.php");
      const data = await response.json();
      setTransactions(data);
    } catch (error) {
      console.error("Gagal mengambil data transaksi:", error);
      // MOCK DATA (Hapus bagian ini jika backend PHP Anda sudah siap & terhubung)
      const dataDummy = [
        { id_transaksi: "TRX-001", nama_pembeli: "Ramdani", total_harga: 150000, tanggal: "2026-06-01", status: "Selesai", qty_buku: 2 },
        { id_transaksi: "TRX-002", nama_pembeli: "Siti", total_harga: 85000, tanggal: "2026-06-02", status: "Selesai", qty_buku: 1 },
        { id_transaksi: "TRX-003", nama_pembeli: "Naufal Lembang", total_harga: 210000, tanggal: "2026-06-02", status: "Diproses", qty_buku: 3 },
      ];
      setTransactions(dataDummy);
    }
  };

  useEffect(() => {
    fetchTransaksi();
  }, []);

  // Logika Filter berdasarkan Tanggal
  const displayedTransactions = transactions.filter((item) => {
    if (!filterDate.start || !filterDate.end) return true;
    const trxDate = new Date(item.tanggal);
    const startDate = new Date(filterDate.start);
    const endDate = new Date(filterDate.end);
    return trxDate >= startDate && trxDate <= endDate;
  });

  // Menghitung otomatis Ringkasan Laporan berdasarkan data yang tampil
  useEffect(() => {
    let pendapatan = 0;
    let terjual = 0;

    displayedTransactions.forEach((item) => {
      if (item.status === "Selesai" || item.status === "Diproses") {
        pendapatan += Number(item.total_harga);
        terjual += Number(item.qty_buku || 1); // jika ada qty dari backend
      }
    });

    setSummary({
      totalPendapatan: pendapatan,
      totalTransaksi: displayedTransactions.length,
      bukuTerjual: terjual
    });
  }, [displayedTransactions]);

  // Fungsi untuk Cetak Laporan (Print to PDF/Printer)
  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="produk-container" style={{ display: "flex", minHeight: "100vh", backgroundColor: "#f4f7f6", position: "relative" }}>
      {/* Sidebar disembunyikan otomatis saat mode cetak/print agar rapi */}
      <div className="no-print">
        <Sidebar />
      </div>
      
      {/* --- WRAPPER KANAN (TIDAK BISA DI-SCROLL) --- */}
      <div style={{ flex: 1, position: "relative", height: "100vh", overflow: "hidden" }}>
        
        {/* --- LOGO BACKGROUND OVERLAY (DIAM DI TEMPAT) --- */}
        <div className="no-print" style={{
          position: "absolute",
          top: 0,
          left: 0,
          width: "100%",
          height: "100%",
          backgroundImage: "url('/bg2.png')",
          backgroundSize: "contain",
          backgroundPosition: "center",
          backgroundRepeat: "no-repeat",
          opacity: 0.15,
          pointerEvents: "none", 
          zIndex: 0
        }}></div>

        {/* --- AREA KONTEN UTAMA (BISA DI-SCROLL) --- */}
        <div id="scroll-area" className="main-content" style={{ 
          position: "absolute",
          top: 0,
          left: 0,
          width: "100%",
          height: "100%",
          padding: "30px", 
          overflowY: "auto", 
          boxSizing: "border-box", 
          zIndex: 1 
        }}>
          
          <header className="page-header" style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
            <div>
              <h2>Laporan & Riwayat Transaksi</h2>
              <p className="subtitle">Pantau performa penjualan dan unduh laporan di sini.</p>
            </div>
            {/* Tombol Cetak */}
            <button onClick={handlePrint} className="no-print" style={{ padding: "10px 20px", backgroundColor: "#2563eb", color: "white", border: "none", borderRadius: "5px", cursor: "pointer", fontWeight: "bold" }}>
              🖨️ Cetak Laporan
            </button>
          </header>

          {/* --- WIDGET CARDS RINGKASAN LAPORAN --- */}
          <div style={{ display: "flex", gap: "20px", marginBottom: "25px", flexWrap: "wrap" }}>
            <div style={{ flex: 1, minWidth: "220px", backgroundColor: "white", padding: "20px", borderRadius: "8px", boxShadow: "0 2px 4px rgba(0,0,0,0.05)", borderLeft: "5px solid #10b981" }}>
              <p style={{ color: "#6b7280", fontSize: "14px", margin: 0 }}>Total Pendapatan</p>
              <h3 style={{ margin: "5px 0 0 0", fontSize: "24px", color: "#111827" }}>Rp {summary.totalPendapatan.toLocaleString()}</h3>
            </div>
            <div style={{ flex: 1, minWidth: "220px", backgroundColor: "white", padding: "20px", borderRadius: "8px", boxShadow: "0 2px 4px rgba(0,0,0,0.05)", borderLeft: "5px solid #3b82f6" }}>
              <p style={{ color: "#6b7280", fontSize: "14px", margin: 0 }}>Total Transaksi</p>
              <h3 style={{ margin: "5px 0 0 0", fontSize: "24px", color: "#111827" }}>{summary.totalTransaksi} Pesanan</h3>
            </div>
            <div style={{ flex: 1, minWidth: "220px", backgroundColor: "white", padding: "20px", borderRadius: "8px", boxShadow: "0 2px 4px rgba(0,0,0,0.05)", borderLeft: "5px solid #f59e0b" }}>
              <p style={{ color: "#6b7280", fontSize: "14px", margin: 0 }}>Buku Terjual</p>
              <h3 style={{ margin: "5px 0 0 0", fontSize: "24px", color: "#111827" }}>{summary.bukuTerjual} Ekspl</h3>
            </div>
          </div>

          {/* --- FILTER RENTANG TANGGAL --- */}
          <div className="table-card" style={{ marginBottom: "25px" }}>
            <div className="no-print" style={{ padding: "15px", display: "flex", gap: "15px", alignItems: "center", flexWrap: "wrap", backgroundColor: "#fdfdfd", borderBottom: "1px solid #eee" }}>
              <span style={{ fontWeight: "bold", color: "#4b5563" }}>📅 Filter Tanggal:</span>
              <input 
                type="date" 
                value={filterDate.start} 
                onChange={(e) => setFilterDate({ ...filterDate, start: e.target.value })}
                style={{ padding: "8px", borderRadius: "5px", border: "1px solid #ccc" }}
              />
              <span style={{ color: "#9ca3af" }}>s/d</span>
              <input 
                type="date" 
                value={filterDate.end} 
                onChange={(e) => setFilterDate({ ...filterDate, end: e.target.value })}
                style={{ padding: "8px", borderRadius: "5px", border: "1px solid #ccc" }}
              />
              {(filterDate.start || filterDate.end) && (
                <button onClick={() => setFilterDate({ start: "", end: "" })} style={{ background: "none", border: "none", color: "#ef4444", cursor: "pointer", fontSize: "14px" }}>
                  ❌ Bersihkan Filter
                </button>
              )}
            </div>

            {/* --- TABEL TRANSAKSI --- */}
            <div className="table-responsive">
              <table className="product-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Nama Pembeli</th>
                    <th>Jumlah Buku</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  {displayedTransactions.length > 0 ? (
                    displayedTransactions.map((item, index) => (
                      <tr key={item.id_transaksi}>
                        <td>{index + 1}</td>
                        <td style={{ fontWeight: "bold", color: "#2563eb" }}>{item.id_transaksi}</td>
                        <td>{item.nama_pembeli}</td>
                        <td>{item.qty_buku || 1} Pcs</td>
                        <td>{item.tanggal}</td>
                        <td>Rp {Number(item.total_harga).toLocaleString()}</td>
                        <td>
                          <span style={{
                            padding: "4px 8px",
                            borderRadius: "4px",
                            fontSize: "12px",
                            fontWeight: "bold",
                            backgroundColor: item.status === "Selesai" ? "#d1fae5" : "#fef3c7",
                            color: item.status === "Selesai" ? "#065f46" : "#92400e"
                          }}>
                            {item.status}
                          </span>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan="7" style={{ textAlign: "center", padding: "20px" }}>
                        Tidak ada data transaksi pada rentang waktu ini.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

      {/* CSS Tambahan khusus untuk menyembunyikan elemen tertentu saat di-print (PDF) */}
      <style>{`
        @media print {
          .no-print { display: none !important; }
          body { background-color: white !important; }
          #scroll-area { position: static !important; height: auto !important; padding: 0 !important; overflow: visible !important; }
          .table-card { box-shadow: none !important; border: none !important; }
        }
      `}</style>
    </div>
  );
}