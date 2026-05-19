import React, { useState, useEffect } from "react";
import Sidebar from "../components/Sidebar";

export default function Dashboard() {
  const [books, setBooks] = useState([]);
  const [totalUsers, setTotalUsers] = useState(0); // Dinamis dari database
  const [totalTransaksi, setTotalTransaksi] = useState(0);

  // State untuk Modal Sinopsis
  const [selectedBook, setSelectedBook] = useState(null);
  const [isModalOpen, setIsModalOpen] = useState(false);

  // =========================
  // FETCH DATA BUKU
  // =========================
  const fetchBooks = async () => {
    try {
      const response = await fetch(
        "http://localhost/DASHBOARD_ADMIN/backend/produk/get.php"
      );
      const data = await response.json();
      setBooks(data);
    } catch (error) {
      console.error("Gagal mengambil data buku:", error);
    }
  };

  // =========================
  // FETCH TOTAL USER
  // Mengambil data dari endpoint user/get.php
  // lalu menghitung jumlah datanya
  // =========================
  const fetchTotalUsers = async () => {
    try {
      const response = await fetch(
        "http://localhost/DASHBOARD_ADMIN/backend/user/get.php"
      );
      const data = await response.json();

      if (Array.isArray(data)) {
        setTotalUsers(data.length);
      } else {
        setTotalUsers(0);
      }
    } catch (error) {
      console.error("Gagal mengambil total user:", error);
      setTotalUsers(0);
    }
  };

  // =========================
  // LOAD DATA SAAT HALAMAN DIBUKA
  // =========================
  useEffect(() => {
    fetchBooks();
    fetchTotalUsers();

    // Auto refresh setiap 5 detik
    const interval = setInterval(() => {
      fetchBooks();
      fetchTotalUsers();
    }, 5000);

    return () => clearInterval(interval);
  }, []);

  // =========================
  // MODAL
  // =========================
  const openModal = (book) => {
    setSelectedBook(book);
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setSelectedBook(null);
  };

  return (
    <div
      style={{
        display: "flex",
        minHeight: "100vh",
        backgroundColor: "#f4f7f6",
        position: "relative",
      }}
    >
      <Sidebar />

      {/* WRAPPER KANAN */}
      <div
        style={{
          flex: 1,
          position: "relative",
          height: "100vh",
          overflow: "hidden",
        }}
      >
        {/* BACKGROUND LOGO */}
        <div
          style={{
            position: "absolute",
            top: 0,
            left: 0,
            width: "100%",
            height: "100%",
            backgroundImage: "url('/bg3.png')",
            backgroundSize: "contain",
            backgroundPosition: "center",
            backgroundRepeat: "no-repeat",
            opacity: 0.1,
            pointerEvents: "none",
            zIndex: 0,
          }}
        ></div>

        {/* KONTEN UTAMA */}
        <div
          style={{
            position: "absolute",
            top: 0,
            left: 0,
            width: "100%",
            height: "100%",
            padding: "30px",
            overflowY: "auto",
            boxSizing: "border-box",
            zIndex: 1,
          }}
        >
          {/* HEADER */}
          <div style={{ marginBottom: "30px" }}>
            <h1 style={{ color: "#2c3e50", margin: "0 0 10px 0" }}>
              Dashboard Overview
            </h1>
            <p style={{ color: "#7f8c8d", margin: 0 }}>
              Selamat datang kembali, Admin! Berikut ringkasan data hari ini.
            </p>
          </div>

          {/* CARD SUMMARY */}
          <div
            style={{
              display: "flex",
              gap: "20px",
              marginBottom: "30px",
              flexWrap: "wrap",
            }}
          >
            {/* TOTAL PRODUK */}
            <div
              style={{
                flex: 1,
                minWidth: "200px",
                backgroundColor: "#fff",
                borderRadius: "10px",
                padding: "20px",
                boxShadow: "0 4px 6px rgba(0,0,0,0.05)",
                borderLeft: "5px solid #3498db",
              }}
            >
              <h4
                style={{
                  margin: "0 0 10px 0",
                  color: "#7f8c8d",
                  fontSize: "14px",
                }}
              >
                TOTAL PRODUK
              </h4>
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                }}
              >
                <h2
                  style={{
                    margin: 0,
                    fontSize: "32px",
                    color: "#2c3e50",
                  }}
                >
                  {books.length}
                </h2>
                <span style={{ fontSize: "30px" }}>📚</span>
              </div>
            </div>

            {/* TOTAL USER */}
            <div
              style={{
                flex: 1,
                minWidth: "200px",
                backgroundColor: "#fff",
                borderRadius: "10px",
                padding: "20px",
                boxShadow: "0 4px 6px rgba(0,0,0,0.05)",
                borderLeft: "5px solid #2ecc71",
              }}
            >
              <h4
                style={{
                  margin: "0 0 10px 0",
                  color: "#7f8c8d",
                  fontSize: "14px",
                }}
              >
                TOTAL USER
              </h4>
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                }}
              >
                <h2
                  style={{
                    margin: 0,
                    fontSize: "32px",
                    color: "#2c3e50",
                  }}
                >
                  {totalUsers}
                </h2>
                <span style={{ fontSize: "30px" }}>👥</span>
              </div>
            </div>

            {/* TOTAL TRANSAKSI */}
            <div
              style={{
                flex: 1,
                minWidth: "200px",
                backgroundColor: "#fff",
                borderRadius: "10px",
                padding: "20px",
                boxShadow: "0 4px 6px rgba(0,0,0,0.05)",
                borderLeft: "5px solid #9b59b6",
              }}
            >
              <h4
                style={{
                  margin: "0 0 10px 0",
                  color: "#7f8c8d",
                  fontSize: "14px",
                }}
              >
                TOTAL TRANSAKSI
              </h4>
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                }}
              >
                <h2
                  style={{
                    margin: 0,
                    fontSize: "32px",
                    color: "#2c3e50",
                  }}
                >
                  {totalTransaksi}
                </h2>
                <span style={{ fontSize: "30px" }}>🛒</span>
              </div>
            </div>
          </div>

          {/* WELCOME BANNER */}
          <div
            style={{
              background: "linear-gradient(135deg, #2c3e50, #3498db)",
              borderRadius: "15px",
              padding: "40px 20px",
              color: "white",
              textAlign: "center",
              marginBottom: "40px",
              boxShadow: "0 10px 20px rgba(0,0,0,0.1)",
            }}
          >
            <h2
              style={{
                margin: "0 0 10px 0",
                fontSize: "36px",
                letterSpacing: "2px",
              }}
            >
              SELAMAT DATANG, ADMIN!
            </h2>
            <p
              style={{
                margin: 0,
                fontSize: "16px",
                opacity: 0.9,
              }}
            >
              Kelola data buku, pengguna, dan pantau transaksi Book Market
              dengan mudah melalui panel ini.
            </p>
          </div>

          {/* KATALOG BUKU */}
          <div>
            <h3
              style={{
                color: "#2c3e50",
                borderBottom: "2px solid #ecf0f1",
                paddingBottom: "10px",
                marginBottom: "20px",
              }}
            >
              Koleksi Buku Tersedia
            </h3>

            <div
              style={{
                display: "grid",
                gridTemplateColumns:
                  "repeat(auto-fill, minmax(200px, 1fr))",
                gap: "20px",
              }}
            >
              {books.length > 0 ? (
                books.map((book) => (
                  <div
                    key={book.id}
                    onClick={() => openModal(book)}
                    style={{
                      backgroundColor: "white",
                      borderRadius: "10px",
                      overflow: "hidden",
                      boxShadow: "0 4px 10px rgba(0,0,0,0.05)",
                      transition: "transform 0.2s, box-shadow 0.2s",
                      cursor: "pointer",
                      display: "flex",
                      flexDirection: "column",
                    }}
                  >
                    <div
                      style={{
                        width: "100%",
                        height: "250px",
                        backgroundColor: "#f8f9fa",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                      }}
                    >
                      {book.gambar ? (
                        <img
                          src={`http://localhost/DASHBOARD_ADMIN/backend/produk/uploads/${book.gambar}`}
                          alt={book.nama_buku}
                          style={{
                            width: "100%",
                            height: "100%",
                            objectFit: "cover",
                          }}
                        />
                      ) : (
                        <span style={{ fontSize: "40px" }}>🖼️</span>
                      )}
                    </div>

                    <div
                      style={{
                        padding: "15px",
                        display: "flex",
                        flexDirection: "column",
                        flex: 1,
                        justifyContent: "space-between",
                      }}
                    >
                      <div>
                        <h4
                          style={{
                            margin: "0 0 5px 0",
                            color: "#2c3e50",
                            fontSize: "16px",
                            whiteSpace: "nowrap",
                            overflow: "hidden",
                            textOverflow: "ellipsis",
                          }}
                          title={book.nama_buku}
                        >
                          {book.nama_buku}
                        </h4>

                        <p
                          style={{
                            margin: "0 0 10px 0",
                            color: "#95a5a6",
                            fontSize: "13px",
                          }}
                        >
                          {book.kategori}
                          {book.penerbit
                            ? ` • ${book.penerbit}`
                            : ""}
                        </p>
                      </div>

                      <div
                        style={{
                          display: "flex",
                          justifyContent: "space-between",
                          alignItems: "center",
                          marginTop: "10px",
                        }}
                      >
                        <p
                          style={{
                            margin: 0,
                            fontWeight: "bold",
                            color: "#e74c3c",
                            fontSize: "15px",
                          }}
                        >
                          Rp{" "}
                          {Number(book.harga).toLocaleString(
                            "id-ID"
                          )}
                        </p>

                        <span
                          style={{
                            fontSize: "12px",
                            backgroundColor:
                              Number(book.stok) > 0
                                ? "#e8f8f5"
                                : "#fdedec",
                            color:
                              Number(book.stok) > 0
                                ? "#1abc9c"
                                : "#e74c3c",
                            padding: "4px 8px",
                            borderRadius: "12px",
                            fontWeight: "bold",
                          }}
                        >
                          Stok: {book.stok}
                        </span>
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <p
                  style={{
                    color: "#7f8c8d",
                    fontStyle: "italic",
                  }}
                >
                  Belum ada buku yang ditambahkan.
                </p>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* MODAL SINOPSIS */}
      {isModalOpen && selectedBook && (
        <div
          onClick={closeModal}
          style={{
            position: "fixed",
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: "rgba(0,0,0,0.6)",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            zIndex: 1000,
            padding: "20px",
          }}
        >
          <div
            onClick={(e) => e.stopPropagation()}
            style={{
              backgroundColor: "white",
              borderRadius: "15px",
              width: "100%",
              maxWidth: "700px",
              maxHeight: "90vh",
              overflowY: "auto",
              display: "flex",
              flexDirection: "row",
              gap: "20px",
              padding: "25px",
              position: "relative",
            }}
          >
            <button
              onClick={closeModal}
              style={{
                position: "absolute",
                top: "15px",
                right: "15px",
                background: "none",
                border: "none",
                fontSize: "24px",
                cursor: "pointer",
              }}
            >
              &times;
            </button>

            <div style={{ flex: "0 0 200px" }}>
              {selectedBook.gambar ? (
                <img
                  src={`http://localhost/DASHBOARD_ADMIN/backend/produk/uploads/${selectedBook.gambar}`}
                  alt={selectedBook.nama_buku}
                  style={{
                    width: "100%",
                    borderRadius: "10px",
                  }}
                />
              ) : (
                <div
                  style={{
                    width: "100%",
                    height: "250px",
                    backgroundColor: "#f8f9fa",
                    borderRadius: "10px",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    fontSize: "40px",
                  }}
                >
                  🖼️
                </div>
              )}
            </div>

            <div style={{ flex: 1 }}>
              <h2
                style={{
                  margin: "0 0 5px 0",
                  color: "#2c3e50",
                }}
              >
                {selectedBook.nama_buku}
              </h2>

              <p
                style={{
                  margin: "0 0 15px 0",
                  color: "#7f8c8d",
                  fontSize: "14px",
                }}
              >
                Kategori: <strong>{selectedBook.kategori}</strong> |
                Penerbit:{" "}
                <strong>
                  {selectedBook.penerbit || "-"}
                </strong>
              </p>

              <h4
                style={{
                  margin: "0 0 8px 0",
                  color: "#34495e",
                }}
              >
                Sinopsis
              </h4>

              <p
                style={{
                  margin: 0,
                  color: "#555",
                  lineHeight: "1.6",
                  whiteSpace: "pre-wrap",
                }}
              >
                {selectedBook.sinopsis ||
                  "Belum ada sinopsis untuk buku ini."}
              </p>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}