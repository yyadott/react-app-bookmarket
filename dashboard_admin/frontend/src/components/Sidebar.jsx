import React, { useState, useEffect } from "react";

export default function Sidebar() {
  const [isOpen, setIsOpen] = useState(true);
  const [userName, setUserName] = useState("Admin");

  useEffect(() => {
    const storedUser = localStorage.getItem("username");
    if (storedUser) {
      setUserName(storedUser);
    }
  }, []);

  const toggleSidebar = () => {
    setIsOpen(!isOpen);
  };

  const menuItems = [
    { name: "Dashboard", icon: "📊", path: "/dashboard" },
    { name: "Produk", icon: "📦", path: "/produk" },
    { name: "User", icon: "👥", path: "/user" },
    { name: "Daftar Transaksi", icon: "🛒", path: "/transaksi" },
  ];

  return (
    <div style={{ 
      width: isOpen ? "250px" : "80px", 
      backgroundColor: "#2c3e50", 
      color: "white", 
      
      // --- PERUBAHAN DI SINI UNTUK MEMBUAT SIDEBAR DIAM ---
      height: "100vh",       // Memaksa tinggi sidebar pas 1 layar penuh
      position: "sticky",    // Membuat elemen menempel
      top: 0,                // Menempel di batas paling atas layar
      // ---------------------------------------------------

      transition: "width 0.3s ease", 
      display: "flex", 
      flexDirection: "column",
      boxShadow: "2px 0 5px rgba(0,0,0,0.1)",
      zIndex: 10
    }}>
      
      {/* --- TOMBOL PANAH TOGGLE --- */}
      <button 
        onClick={toggleSidebar}
        style={{
          position: "absolute",
          top: "30px",
          right: "-15px", 
          backgroundColor: "#3498db",
          color: "white",
          border: "none",
          borderRadius: "50%",
          width: "30px",
          height: "30px",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          cursor: "pointer",
          boxShadow: "0 2px 5px rgba(0,0,0,0.2)",
          zIndex: 100,
          transition: "transform 0.3s ease"
        }}
        title={isOpen ? "Tutup Sidebar" : "Buka Sidebar"}
      >
        <span style={{ transform: isOpen ? "rotate(0deg)" : "rotate(180deg)", transition: "transform 0.3s ease", fontWeight: "bold" }}>
          ◀
        </span>
      </button>

      {/* --- HEADER LOGO --- */}
      <div style={{ padding: "30px 20px", borderBottom: "1px solid rgba(255,255,255,0.1)", textAlign: isOpen ? "left" : "center" }}>
        <h2 style={{ margin: 0, color: "white", fontSize: isOpen ? "20px" : "14px", whiteSpace: "nowrap", overflow: "hidden" }}>
          {isOpen ? "BOOK MARKET" : "BM"}
        </h2>
      </div>

      {/* --- INFO USER --- */}
      <div style={{ padding: "20px", borderBottom: "1px solid rgba(255,255,255,0.1)", display: "flex", alignItems: "center", justifyContent: isOpen ? "flex-start" : "center" }}>
        {isOpen ? (
          <div>
            <p style={{ margin: "0 0 5px 0", fontSize: "12px", color: "#95a5a6" }}>Halo,</p>
            <p style={{ margin: 0, fontWeight: "bold", color: "#3498db", textTransform: "capitalize" }}>{userName}</p>
          </div>
        ) : (
          <span style={{ fontSize: "24px" }} title={`Halo, ${userName}`}>👤</span>
        )}
      </div>

      {/* --- MENU NAVIGASI & LOGOUT --- */}
      <div style={{ padding: "20px 0", flex: 1 }}>
        
        {/* Render Menu Utama */}
        {menuItems.map((item, index) => (
          <a 
            key={index} 
            href={item.path} 
            style={{ 
              display: "flex", 
              alignItems: "center", 
              padding: "15px 20px", 
              color: "white", 
              textDecoration: "none",
              transition: "background 0.2s",
              justifyContent: isOpen ? "flex-start" : "center"
            }}
            onMouseOver={(e) => e.currentTarget.style.backgroundColor = "rgba(255,255,255,0.1)"}
            onMouseOut={(e) => e.currentTarget.style.backgroundColor = "transparent"}
            title={!isOpen ? item.name : ""} 
          >
            <span style={{ fontSize: "20px", marginRight: isOpen ? "15px" : "0" }}>{item.icon}</span>
            {isOpen && <span style={{ whiteSpace: "nowrap" }}>{item.name}</span>}
          </a>
        ))}

        {/* Garis Pembatas Tipis */}
        <div style={{ height: "1px", backgroundColor: "rgba(39, 3, 248, 0.1)", margin: "10px 20px" }}></div>

        {/* Tombol Logout */}
        <a 
          href="/login" 
          onClick={() => {
            localStorage.removeItem("username"); 
          }}
          style={{ 
            display: "flex", 
            alignItems: "center", 
            padding: "15px 20px", 
            color: "#e74c3c", 
            textDecoration: "none",
            fontWeight: "bold",
            transition: "background 0.2s",
            justifyContent: isOpen ? "flex-start" : "center"
          }}
          onMouseOver={(e) => {
            e.currentTarget.style.backgroundColor = "rgba(231, 76, 60, 0.1)"; 
          }}
          onMouseOut={(e) => {
            e.currentTarget.style.backgroundColor = "transparent";
          }}
          title={!isOpen ? "Logout" : ""}
        >
          <span style={{ fontSize: "20px", marginRight: isOpen ? "15px" : "0" }}>🚪</span>
          {isOpen && <span style={{ whiteSpace: "nowrap" }}>Logout</span>}
        </a>

      </div>

    </div>
  );
}