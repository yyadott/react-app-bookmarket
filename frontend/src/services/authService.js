/**
 * authService.js
 * Pusat logika autentikasi (Login & Register) untuk sistem Bookmarket.
 * Sesuai dengan spesifikasi SDD (Sequence Diagram & Use Case Scenario).
 */

// URL Dasar API Backend sesuai dengan lokasi folder di Laragon [cite: 84]
const API_URL = "http://localhost/react-app-bookmarket/backend/api";

const authService = {
  
  /**
   * Fungsi Login (XA-01) [cite: 111, 329]
   * Mengirim kredensial ke API dan mengembalikan data pengguna/role.
   */
  login: async (email, password) => {
    try {
      const response = await fetch(`${API_URL}/login.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ email, password }),
      });

      const result = await response.json();
      
      // Jika login sukses, kita bisa menyimpan data sesi sederhana (opsional)
      if (result.success) {
        // Logika sesuai Skenario Utama langkah 4 [cite: 113, 331]
        console.log("Login Berhasil sebagai:", result.role);
      }
      
      return result;
    } catch (error) {
      console.error("AuthService Login Error:", error);
      return { success: false, message: "Terjadi kesalahan pada server. Coba lagi nanti." };
    }
  },

  /**
   * Fungsi Registrasi (XA-02) [cite: 118, 336]
   * Mengirim data pelanggan baru ke database tbl_user.
   */
  register: async (nama, email, password) => {
    try {
      const response = await fetch(`${API_URL}/register.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ nama, email, password }),
      });

      const result = await response.json();
      return result;
    } catch (error) {
      console.error("AuthService Register Error:", error);
      return { success: false, message: "Gagal terhubung ke server registrasi." };
    }
  },

  /**
   * Fungsi Logout
   * Menghapus sesi atau mengarahkan kembali ke halaman login.
   */
  logout: () => {
    // Di sisi Client-side React, kita cukup membersihkan state/session
    window.location.href = "/login";
  }
};

export default authService;