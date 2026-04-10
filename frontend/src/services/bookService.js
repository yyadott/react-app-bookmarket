const API_URL = "http://localhost/bookmarket/backend/api/buku.php";

const bookService = {
  getBooks: async () => {
    try {
      const response = await fetch(API_URL);
      if (!response.ok) throw new Error("Gagal mengambil data");
      return await response.json();
    } catch (error) {
      console.error(error);
      return []; // Kembalikan array kosong jika error agar web tidak putih
    }
  }
};

export default bookService;