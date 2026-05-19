import React from "react";
import Sidebar from "../components/Sidebar";
import "./Produk.css";

export default function Transaksi() {
  return (
    <div className="produk-container" style={{ display: "flex", minHeight: "100vh", backgroundColor: "#f4f7f6", position: "relative" }}>
      <Sidebar />
      
      {/* --- WRAPPER KANAN (Sebagai bingkai layar, TIDAK BISA DI-SCROLL) --- */}
      <div style={{ flex: 1, position: "relative", height: "100vh", overflow: "hidden" }}>
        
        {/* --- LOGO BACKGROUND OVERLAY (DIAM DI TEMPAT) --- */}
        <div style={{
          position: "absolute",
          top: 0,
          left: 0,
          width: "100%",
          height: "100%",
          backgroundImage: "url('/bg3.png')",
          backgroundSize: "contain",
          backgroundPosition: "center",
          backgroundRepeat: "no-repeat",
          opacity: 0.15,
          pointerEvents: "none", 
          zIndex: 0
        }}></div>

        {/* --- AREA KONTEN UTAMA (YANG BISA DI-SCROLL) --- */}
        <div className="main-content" style={{ 
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
          
          <header className="page-header">
            <h2>Daftar Transaksi</h2>
            <p className="subtitle">Pantau semua riwayat pembelian di sini.</p>
          </header>

          <div className="table-card">
            <div className="card-header">
              <h3>Riwayat Transaksi</h3>
            </div>
            <div className="table-responsive">
              <table className="product-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Nama Pembeli</th>
                    <th>Total Harga</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colSpan="6" style={{ textAlign: "center", padding: "20px" }}>
                      Belum ada data transaksi. (Fitur ini sedang dalam pengembangan)
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  );
}