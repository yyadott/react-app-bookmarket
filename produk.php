<?php include 'header.php'; ?>

<?php
if (!function_exists('esc')) {
    function esc($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
// ==========================================
// CONFIGURATION & LOGIKA PAGINATION (DINAMIS)
// ==========================================
$limit = 8; // Sesuai desain UI baru yang menampilkan kelipatan genap
$current_page = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
if ($current_page < 1) { $current_page = 1; }
$offset = ($current_page - 1) * $limit;

// 1. Ambil data kategori untuk sidebar filter
$kategori = mysqli_query($koneksi, "
    SELECT *
    FROM kategori
    ORDER BY namakategori ASC
");

// 2. MODIFIKASI: Logika filter kategori & pencarian multi-kolom (Judul ATAU Penulis)
$where_clauses = [];

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kategori_id = intval($_GET['kategori']);
    $where_clauses[] = "p.kategori_id='$kategori_id'";
}

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    // Menggunakan tanda kurung ( ) agar logika OR tidak merusak gabungan klausa kategori
    $where_clauses[] = "(p.namaproduk LIKE '%$search%' OR p.penulis LIKE '%$search%')";
}

// Rekonstruksi klausa WHERE database
$where = "";
if (count($where_clauses) > 0) {
    $where = "WHERE " . implode(" AND ", $where_clauses);
}

// 3. Logika Urutan Harga (Sesuai Radio Button di UI)
$order_by = "ORDER BY p.id DESC"; // Default urutan terbaru
if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'low_high') {
        $order_by = "ORDER BY p.harga ASC";
    } elseif ($_GET['sort'] == 'high_low') {
        $order_by = "ORDER BY p.harga DESC";
    }
}

// 4. Hitung total records untuk navigasi halaman
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk p $where");
$data_total = mysqli_fetch_assoc($query_total);
$total_buku = $data_total['total'];
$total_halaman = ceil($total_buku / $limit);

// 5. Query data produk utama
$produk = mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    $where 
    $order_by
    LIMIT $limit OFFSET $offset
");

// 6. Ikat query string URL agar filter tidak hilang saat klik pindah halaman
$query_params = $_GET;
unset($query_params['halaman']);
$base_url = "produk.php?" . http_build_query($query_params) . "&";
?>

<style>
    body {
        background-color: #f8fafc;
    }
    /* GAYA BANNER ATAS BUKUPEDIA */
    .bg-bukupedia-blue {
        background: linear-gradient(180deg, #5a67d8 0%, #4c51bf 100%);
        border-radius: 0 0 30px 30px;
    }
    .inner-banner-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 3.5rem 2rem;
    }
    
    /* SIDEBAR FILTER NAVIGATION */
    .sidebar-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #1a202c;
    }
    .filter-sidebar .nav-link {
        color: #4a5568;
        font-size: 0.95rem;
        padding: 0.4rem 0;
        transition: color 0.2s;
    }
    .filter-sidebar .nav-link:hover {
        color: #5a67d8;
    }
    .filter-sidebar .nav-link.active {
        color: #5a67d8;
        font-weight: 700;
        background-color: transparent;
    }

    /* CARD PRODUK GAYA MINIMALIS BARU */
    .book-card-item {
        border: none;
        border-radius: 12px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        padding: 1.25rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .book-card-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(90, 103, 216, 0.06);
    }
    .book-cover-wrap img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
    }
    .book-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1a202c;
        line-height: 1.3;
    }
    .text-orange {
        color: #f6ad55;
    }
    .btn-action-basket {
        border: 1px solid #edf2f7;
        background-color: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }
    .btn-action-basket:hover {
        background-color: #5a67d8;
        border-color: #5a67d8;
        color: #ffffff;
    }

    /* BARU: Gaya Elemen Form Pencarian */
    .search-input-group {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }
    .search-input-group .form-control {
        border: none;
        padding-left: 1.25rem;
    }
    .search-input-group .form-control:focus {
        box-shadow: none;
    }
    .search-input-group .btn {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        background-color: #5a67d8;
        color: #fff;
    }
    .search-input-group .btn:hover {
        background-color: #4c51bf;
    }
</style>

<div class="w-100 bg-bukupedia-blue py-5 mb-5 text-center text-white">
    <div class="container px-4 px-md-5">
        <div class="inner-banner-card mx-auto" style="max-width: 960px;">
            <h1 class="fw-bold mb-0 display-6" style="letter-spacing: -0.5px;">Daftar Buku</h1>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        
        <div class="col-md-3 filter-sidebar d-none d-md-block pe-4">
            
            <h6 class="sidebar-title mb-3">Kategori</h6>
            <ul class="nav flex-column mb-4">
                <li class="nav-item">
                    <a class="nav-link <?= !isset($_GET['kategori']) ? 'active' : '' ?>" href="produk.php<?= isset($_GET['search']) ? '?search='.urlencode($_GET['search']) : '' ?>">Semua Genre</a>
                </li>
                <?php 
                mysqli_data_seek($kategori, 0); // Reset pointer loop database
                while ($k = mysqli_fetch_assoc($kategori)) { 
                    $link_kategori = "produk.php?kategori=" . $k['id'];
                    if (isset($_GET['search'])) { $link_kategori .= "&search=" . urlencode($_GET['search']); }
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'active' : '' ?>" href="<?= $link_kategori ?>">
                            <?= $k['namakategori'] ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>

            <h6 class="sidebar-title mb-3">Urutan Harga</h6>
            <div class="mb-4">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="sortPrice" id="lowToHigh" <?= (isset($_GET['sort']) && $_GET['sort'] == 'low_high') ? 'checked' : '' ?> onclick="window.location.href='<?= $base_url ?>sort=low_high'">
                    <label class="form-check-label text-secondary small fw-medium" for="lowToHigh" style="cursor:pointer;">Rendah &rarr; Tinggi</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sortPrice" id="highToLow" <?= (isset($_GET['sort']) && $_GET['sort'] == 'high_low') ? 'checked' : '' ?> onclick="window.location.href='<?= $base_url ?>sort=high_low'">
                    <label class="form-check-label text-secondary small fw-medium" for="highToLow" style="cursor:pointer;">Tinggi &rarr; Rendah</label>
                </div>
            </div>

            <h6 class="sidebar-title mb-2">Total Koleksi</h6>
            <div class="text-muted small fw-semibold"><i class="bi bi-journal-bookmark-fill me-1"></i> <?= $total_buku ?> Koleksi Tersedia</div>
        </div>

        <div class="col-md-9">
            
            <div class="mb-4">
                <form action="produk.php" method="GET">
                    <?php if (isset($_GET['kategori'])): ?>
                        <input type="hidden" name="kategori" value="<?= intval($_GET['kategori']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= esc($_GET['sort']) ?>">
                    <?php endif; ?>
                    
                    <div class="input-group search-input-group">
                        <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari judul buku atau nama penulis di sini..." value="<?= isset($_GET['search']) ? esc($_GET['search']) : '' ?>" autocomplete="off">
                        <button class="btn fw-semibold" type="submit">Cari Buku</button>
                    </div>
                </form>
                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <div class="mt-2 text-muted small">
                        Menampilkan hasil pencarian untuk: <strong class="text-dark">"<?= esc($_GET['search']) ?>"</strong> 
                        <a href="produk.php<?= isset($_GET['kategori']) ? '?kategori='.intval($_GET['kategori']) : '' ?>" class="text-danger ms-2 text-decoration-none fw-medium"><i class="bi bi-x-circle-fill"></i> Bersihkan</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 g-4">
                
                <?php if (mysqli_num_rows($produk) > 0) { ?>
                    <?php while ($p = mysqli_fetch_assoc($produk)) { ?>
                        
                        <div class="col">
                            <div class="book-card-item h-100">
                                <div class="row g-0 h-100 align-items-center">
                                    
                                    <div class="col-4 text-center">
                                        <div class="book-cover-wrap">
                                            <?php if ($p['foto']) { ?>
                                                <img src="assets/uploads/produk/<?= $p['foto'] ?>" alt="Cover">
                                            <?php } else { ?>
                                                <img src="https://via.placeholder.com/150x200?text=No+Cover" alt="No Cover">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-8">
                                        <div class="ps-3 d-flex flex-column h-100 justify-content-between">
                                            <div>
                                                <h5 class="book-title mb-1 text-truncate" title="<?= $p['namaproduk'] ?>">
                                                    <?= $p['namaproduk'] ?>
                                                </h5>
                                                
                                                <p class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">
                                                    <i class="bi bi-person me-1"></i><?= $p['penulis'] ?? 'Anom Whani Wicaksono' ?>
                                                </p>
                                                
                                                <div class="d-flex align-items-center mb-2" style="font-size: 0.75rem;">
                                                    <div class="text-orange me-2">
                                                        <i class="bi bi-star-fill"></i>
                                                        <i class="bi bi-star-fill"></i>
                                                        <i class="bi bi-star-fill"></i>
                                                        <i class="bi bi-star-fill"></i>
                                                        <i class="bi bi-star-fill"></i>
                                                    </div>
                                                    <span class="text-muted fw-medium">4000 Terjual</span>
                                                </div>
                                                
                                                <h6 class="fw-bold text-dark mb-3" style="font-size: 1.1rem;">
                                                    Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                                </h6>
                                            </div>
                                            
                                            <a href="produkdetail.php?id=<?= $p['id'] ?>" class="btn btn-action-basket text-center w-100 py-2">
                                                Detail Buku
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    <?php } ?>
                <?php } else { ?>
                    <div class="col-12 w-100">
                        <div class="alert alert-white border text-center py-5 shadow-sm rounded-4">
                            <i class="bi bi-inboxes display-5 text-muted mb-3 d-block"></i>
                            <h5 class="fw-bold text-dark mb-1">Buku Tidak Ditemukan</h5>
                            <p class="text-muted small mb-0">Silakan pilih kategori lain atau periksa kembali kata kunci pencarian Anda.</p>
                        </div>
                    </div>
                <?php } ?>

            </div>

            <?php if ($total_halaman > 1) { ?>
                <nav class="mt-5 d-flex justify-content-center">
                    <ul class="pagination pagination-sm gap-1">
                        
                        <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle border-0" href="<?= $base_url ?>halaman=<?= $current_page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
                            <?php if ($i == 1 || $i == $total_halaman || ($i >= $current_page - 1 && $i <= $current_page + 1)) { ?>
                                <li class="page-item <?= ($current_page == $i) ? 'active' : '' ?>">
                                    <a class="page-link rounded-circle border-0" href="<?= $base_url ?>halaman=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php } elseif ($i == 2 || $i == $total_halaman - 1) { ?>
                                <li class="page-item disabled"><a class="page-link rounded-circle border-0 bg-transparent" href="#">..</a></li>
                            <?php } ?>
                        <?php } ?>
                        
                        <li class="page-item <?= ($current_page >= $total_halaman) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle border-0" href="<?= $base_url ?>halaman=<?= $current_page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
                        </li>
                        
                    </ul>
                </nav>
            <?php } ?>

        </div>

    </div>
</div>

<?php include 'footer.php'; ?>