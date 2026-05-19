import React, { useState, useEffect } from "react";
import Sidebar from "../components/Sidebar";
import "./Produk.css"; 

export default function User() {
  const [users, setUsers] = useState([]);

  const fetchUsers = async () => {
    try {
      const response = await fetch("http://localhost/DASHBOARD_ADMIN/backend/user/get.php");
      const data = await response.json();
      setUsers(data);
    } catch (error) {
      console.error(error);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  return (
    <div className="produk-container">
      <Sidebar />
      <div className="main-content">
        <header className="page-header">
          <h2>Daftar User Admin</h2>
          <p className="subtitle">Kelola pengguna yang memiliki akses ke dalam sistem.</p>
        </header>

        <div className="table-card">
          <div className="card-header"><h3>Data Admin</h3></div>
          <div className="table-responsive">
            <table className="product-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Lengkap</th>
                  <th>Email</th>
                  <th>Role</th>
                </tr>
              </thead>
              <tbody>
                {users.length > 0 ? (
                  users.map((item, index) => (
                    <tr key={item.id}>
                      <td>{index + 1}</td>
                      <td style={{ fontWeight: "bold" }}>{item.nama || "Belum ada nama"}</td>
                      <td>{item.email}</td>
                      <td><span className="badge-stock" style={{ backgroundColor: "#e0e7ff", color: "#3730a3" }}>Admin</span></td>
                    </tr>
                  ))
                ) : (
                  <tr><td colSpan="4" style={{ textAlign: "center" }}>Belum ada data user.</td></tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}