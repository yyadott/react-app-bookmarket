import React, { useState, useEffect } from "react";
import Sidebar from "../components/Sidebar";
import "./Produk.css";

export default function Produk() {
  // 1. Tambahkan penerbit dan sinopsis ke state form
  const [formData, setFormData] = useState({
    nama_buku: "",
    harga: "",
    stok: "",
    kategori: "",
    penerbit: "",
    sinopsis: ""
  });
  
  const [imageFile, setImageFile] = useState(null);
  const [products, setProducts] = useState([]);
  const [editId, setEditId] = useState(null);

  const [searchTerm, setSearchTerm] = useState("");
  const [sortOrder, setSortOrder] = useState("");

  const fetchProducts = async () => {
    try {
      const response = await fetch("http://localhost/DASHBOARD_ADMIN/backend/produk/get.php");
      const data = await response.json();
      setProducts(data);
    } catch (error) { console.error(error); }
  };

  useEffect(() => { fetchProducts(); }, []);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleFileChange = (e) => {
    setImageFile(e.target.files[0]);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const dataToSend = new FormData();
    dataToSend.append("nama_buku", formData.nama_buku);
    dataToSend.append("harga", formData.harga);
    dataToSend.append("stok", formData.stok);
    dataToSend.append("kategori", formData.kategori);
    
    // 2. Append data baru saat submit
    dataToSend.append("penerbit", formData.penerbit);
    dataToSend.append("sinopsis", formData.sinopsis);

    if(imageFile) {
        dataToSend.append("gambar", imageFile);
    }

    let url = "http://localhost/DASHBOARD_ADMIN/backend/produk/add.php";
    if (editId !== null) {
        url = "http://localhost/DASHBOARD_ADMIN/backend/produk/update.php";
        dataToSend.append("id", editId);
    }

    try {
      const response = await fetch(url, { method: "POST", body: dataToSend });
      const result = await response.json();
      if (result.status === "success") {
        alert(result.message);
        // 3. Reset form secara penuh
        setFormData({ nama_buku: "", harga: "", stok: "", kategori: "", penerbit: "", sinopsis: "" });
        setImageFile(null);
        setEditId(null);
        document.getElementById("file-input").value = ""; 
        fetchProducts();
      } else {
        alert("Gagal: " + result.message);
      }
    } catch (error) { console.error(error); }
  };

  const handleEdit = (item) => {
    // 4. Masukkan data ke form saat tombol edit ditekan
    setFormData({
      nama_buku: item.nama_buku,
      harga: item.harga,
      stok: item.stok,
      kategori: item.kategori,
      penerbit: item.penerbit || "",
      sinopsis: item.sinopsis || ""
    });
    setEditId(item.id);
    
    // PERBAIKAN DI SINI: Scroll ke atas pada area yang tepat
    const scrollContainer = document.getElementById("scroll-area");
    if (scrollContainer) {
      scrollContainer.scrollTo({ top: 0, behavior: "smooth" });
    }
  };

  const handleCancelEdit = () => {
    setFormData({ nama_buku: "", harga: "", stok: "", kategori: "", penerbit: "", sinopsis: "" });
    setImageFile(null);
    setEditId(null);
    document.getElementById("file-input").value = ""; 
  };

  const handleDelete = async (id) => {
    if (window.confirm("Apakah Anda yakin ingin menghapus buku ini?")) {
      try {
        const response = await fetch("http://localhost/DASHBOARD_ADMIN/backend/produk/delete.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: id }),
        });
        const result = await response.json();
        if (result.status === "success") {
          alert(result.message);
          fetchProducts();
        } else {
          alert("Gagal: " + result.message);
        }
      } catch (error) { console.error(error); }
    }
  };

  // LOGIKA FILTERING & SORTING
  let displayedProducts = products.filter((item) =>
    item.nama_buku.toLowerCase().includes(searchTerm.toLowerCase())
  );

  if (sortOrder === "A-Z") {
    displayedProducts.sort((a, b) => a.nama_buku.localeCompare(b.nama_buku));
  } else if (sortOrder === "Z-A") {
    displayedProducts.sort((a, b) => b.nama_buku.localeCompare(a.nama_buku));
  } else if (sortOrder === "Harga-Tinggi") {
    displayedProducts.sort((a, b) => Number(b.harga) - Number(a.harga));
  } else if (sortOrder === "Harga-Rendah") {
    displayedProducts.sort((a, b) => Number(a.harga) - Number(b.harga));
  }

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
        {/* PERBAIKAN DI SINI: Menambahkan id="scroll-area" */}
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
          
          <header className="page-header"><h2>Manajemen Produk</h2></header>

          {/* --- FORM TAMBAH/EDIT --- */}
          <div className="form-card">
            <div className="card-header">
              <h3>{editId !== null ? "Edit Produk" : "Tambah Produk Baru"}</h3>
            </div>
            <form className="product-form" onSubmit={handleSubmit}>
              <div className="form-group">
                <label>Nama Buku</label>
                <input type="text" name="nama_buku" value={formData.nama_buku} onChange={handleChange} required />
              </div>
              
              <div className="form-group">
                <label>Penerbit / Penulis</label>
                <input type="text" name="penerbit" value={formData.penerbit} onChange={handleChange} placeholder="Contoh: Gramedia / Tere Liye" required />
              </div>

              <div className="form-group">
                <label>Kategori</label>
                <input type="text" name="kategori" value={formData.kategori} onChange={handleChange} list="kategori-options" required style={{ width: "100%", padding: "10px", marginTop: "5px", borderRadius: "5px", border: "1px solid #ccc", boxSizing: "border-box" }}/>
                <datalist id="kategori-options">
                  <option value="Fiksi" /><option value="Non-Fiksi" /><option value="Edukasi" />
                  <option value="Buku Anak" /><option value="Komik" /><option value="Novel" />
                </datalist>
              </div>

              <div className="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" value={formData.harga} onChange={handleChange} required />
              </div>

              <div className="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value={formData.stok} onChange={handleChange} required />
              </div>

              {/* Input Sinopsis (Area Teks Lebar) */}
              <div className="form-group full-width">
                <label>Sinopsis Buku</label>
                <textarea 
                  name="sinopsis" 
                  value={formData.sinopsis} 
                  onChange={handleChange} 
                  rows="4" 
                  placeholder="Tuliskan ringkasan cerita atau isi buku di sini..."
                  style={{ width: "100%", padding: "10px", marginTop: "5px", borderRadius: "5px", border: "1px solid #ccc", boxSizing: "border-box", fontFamily: "inherit" }}
                  required 
                />
              </div>

              <div className="form-group full-width">
                <label>Upload Gambar Cover {editId !== null && "(Biarkan kosong jika tidak ingin mengubah)"}</label>
                <input id="file-input" type="file" className="file-input" onChange={handleFileChange} accept="image/*" />
              </div>

              <div className="form-actions full-width" style={{ display: "flex", gap: "10px" }}>
                <button type="submit" className="btn-submit" style={{ flex: 1 }}>{editId !== null ? "Simpan Perubahan" : "+ Tambah Produk"}</button>
                {editId !== null && (
                  <button type="button" onClick={handleCancelEdit} className="btn-submit" style={{ flex: 1, backgroundColor: "#6b7280" }}>Batal Edit</button>
                )}
              </div>
            </form>
          </div>

          {/* --- DAFTAR BUKU --- */}
          <div className="table-card">
            <div className="card-header">
              <h3>Daftar Buku</h3>
            </div>
            
            <div style={{ padding: "15px", display: "flex", gap: "15px", flexWrap: "wrap", borderBottom: "1px solid #eee", backgroundColor: "#fdfdfd" }}>
              <input 
                type="text" 
                placeholder="🔍 Cari nama buku..." 
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                style={{ flex: 1, minWidth: "200px", padding: "10px", borderRadius: "5px", border: "1px solid #ccc" }}
              />
              <select 
                value={sortOrder} 
                onChange={(e) => setSortOrder(e.target.value)}
                style={{ padding: "10px", borderRadius: "5px", border: "1px solid #ccc", minWidth: "200px", cursor: "pointer" }}
              >
                <option value="">Sortir Default</option>
                <option value="A-Z">A - Z (Nama Buku)</option>
                <option value="Z-A">Z - A (Nama Buku)</option>
                <option value="Harga-Tinggi">Harga: Tertinggi - Termurah</option>
                <option value="Harga-Rendah">Harga: Termurah - Tertinggi</option>
              </select>
            </div>

            <div className="table-responsive">
              <table className="product-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Buku</th>
                    <th>Penerbit</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  {displayedProducts.length > 0 ? (
                    displayedProducts.map((item, index) => (
                      <tr key={item.id}>
                        <td>{index + 1}</td>
                        <td>
                          {item.gambar ? (
                            <img src={`http://localhost/DASHBOARD_ADMIN/backend/produk/uploads/${item.gambar}`} alt="cover" style={{ width: "50px", height: "70px", objectFit: "cover", borderRadius: "4px" }} />
                          ) : (
                            <div className="img-placeholder">🖼️</div>
                          )}
                        </td>
                        <td className="book-title">
                           {item.nama_buku}
                           {/* Menampilkan sedikit potongan sinopsis di bawah judul buku pada tabel agar terlihat menarik */}
                           {item.sinopsis && (
                             <div style={{ fontSize: "12px", color: "#7f8c8d", marginTop: "4px", maxWidth: "200px", whiteSpace: "nowrap", overflow: "hidden", textOverflow: "ellipsis" }}>
                               {item.sinopsis}
                             </div>
                           )}
                        </td>
                        <td>{item.penerbit || "-"}</td>
                        <td>{item.kategori}</td>
                        <td>Rp {Number(item.harga).toLocaleString()}</td>
                        <td><span className="badge-stock">{item.stok}</span></td>
                        <td>
                          <button onClick={() => handleEdit(item)} className="btn-action btn-edit">Edit</button>
                          <button onClick={() => handleDelete(item.id)} className="btn-action btn-delete">Hapus</button>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan="8" style={{textAlign: 'center', padding: "30px"}}>
                        {searchTerm ? "Buku yang Anda cari tidak ditemukan." : "Belum ada data produk."}
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  );
}