<?php include 'header.php'; ?>

<?php
$limit = 6;
$current_page = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
if ($current_page < 1) { $current_page = 1; }
$offset = ($current_page - 1) * $limit;

<<<<<<< HEAD
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");

$where = "";
if (isset($_GET['kategori'])) {
=======
// 1. Ambil data kategori untuk sidebar
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY namakategori ASC");

// 2. MODIFIKASI: Logika pencarian multi-kolom (Judul ATAU Penulis) & Filter Kategori
$where_clauses = [];

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
>>>>>>> 98b21cc60852a2f876ef3da950f9e6cb8f8c9438
    $kategori_id = intval($_GET['kategori']);
    $where_clauses[] = "p.kategori_id='$kategori_id'";
}

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    // Menggunakan tanda kurung ( ) agar logika OR tidak merusak klausa AND kategori
    $where_clauses[] = "(p.namaproduk LIKE '%$search%' OR p.penulis LIKE '%$search%')";
}

// Rekonstruksi klausa WHERE untuk kueri SQL
$where = "";
if (count($where_clauses) > 0) {
    $where = "WHERE " . implode(" AND ", $where_clauses);
}

$order_by = "ORDER BY p.id DESC";
if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'low_high') {
        $order_by = "ORDER BY p.harga ASC";
    } elseif ($_GET['sort'] == 'high_low') {
        $order_by = "ORDER BY p.harga DESC";
    }
}

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk p $where");
$data_total = mysqli_fetch_assoc($query_total);
$total_buku = $data_total['total'];
$total_halaman = ceil($total_buku / $limit);

$produk = mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    $where 
    $order_by
    LIMIT $limit OFFSET $offset
");

$query_params = $_GET;
unset($query_params['halaman']);
$base_query_string = http_build_query($query_params);
$base_url = "index.php" . ($base_query_string ? "?" . $base_query_string . "&" : "?");
?>

<style>
    .bg-custom-blue {
        background: linear-gradient(135deg, #ffffff 60%, #4a63b8 60%);
    }
    .sidebar-filter .nav-link {
        color: #495057;
        padding: 0.4rem 0;
        font-size: 0.95rem;
    }
    .sidebar-filter .nav-link.active {
        color: #4a63b8;
        font-weight: bold;
        background: transparent;
    }
    .product-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(74, 99, 184, 0.08);
    }
    .product-card img {
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        height: 200px;
        object-fit: cover;
    }
    .btn-outline-basket {
        border: 1px solid #f7e4df;
        background-color: #fffaf9;
        color: #b8624a;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .btn-outline-basket:hover {
        background-color: #b8624a;
        color: #ffffff;
        border-color: #b8624a;
    }
    .badge-kategori {
        background: #f0f3ff;
        color: #4a63b8;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .badge-stok-ada {
        background: #e8f9ee;
        color: #27ae60;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .badge-stok-habis {
        background: #fdecea;
        color: #e74c3c;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
    }
</style>

<div class="w-100 bg-custom-blue py-5 mb-5 position-relative" style="min-height: 380px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-7 z-3">
                <h1 class="fw-black text-dark display-5 mb-3" style="font-weight:800; letter-spacing:-1px;">
                    TEMUKAN BUKU FAVORITMU<br>DI BOOK MARKET
                </h1>
                <p class="text-secondary mb-4 col-lg-10 px-0" style="font-size:0.95rem; line-height:1.6;">
                    Book Market menyediakan berbagai koleksi buku berkualitas mulai dari novel, buku pendidikan, 
                    bisnis, pengembangan diri, hingga buku anak.
                </p>
                
                <form method="GET" action="index.php" class="col-lg-9 px-0">
                    <input type="hidden" name="page" value="home">
                    <?php if (isset($_GET['kategori'])): ?>
                        <input type="hidden" name="kategori" value="<?= intval($_GET['kategori']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    
                    <div class="input-group bg-light rounded-2 p-1 border shadow-sm">
<<<<<<< HEAD
                        <span class="input-group-text bg-transparent border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 small" 
                               placeholder="Cari Buku..." 
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
=======
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 small" placeholder="Cari berdasarkan judul buku atau penulis..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>" autocomplete="off">
                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <a href="index.php?page=home<?= isset($_GET['kategori']) ? '&kategori='.intval($_GET['kategori']) : '' ?>" class="btn btn-transparent border-0 text-danger d-flex align-items-center"><i class="bi bi-x-circle-fill"></i></a>
                        <?php endif; ?>
>>>>>>> 98b21cc60852a2f876ef3da950f9e6cb8f8c9438
                    </div>
                </form>
            </div>
            <div class="col-lg-6 col-md-5 d-none d-md-block text-end position-absolute end-0 bottom-0" 
                 style="max-height:100%;">
                <img src="assets/foto/hero2.png" alt="Hero Image" style="height:auto; object-fit:cover;">
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">

        <!-- SIDEBAR FILTER -->
        <div class="col-md-3 sidebar-filter d-none d-md-block pe-4 mt-5">
            <h6 class="fw-bold text-dark mb-3 text-uppercase small" style="letter-spacing:1px;">Kategori</h6>
            <ul class="nav flex-column mb-4">
                <li class="nav-item">
<<<<<<< HEAD
                    <a class="nav-link <?= !isset($_GET['kategori']) ? 'active' : '' ?>" 
                       href="index.php?page=home">Semua</a>
=======
                    <a class="nav-link <?= !isset($_GET['kategori']) ? 'active' : '' ?>" href="index.php?page=home<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>">Semua</a>
>>>>>>> 98b21cc60852a2f876ef3da950f9e6cb8f8c9438
                </li>
                <?php
                mysqli_data_seek($kategori, 0);
<<<<<<< HEAD
                while ($k = mysqli_fetch_assoc($kategori)) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'active' : '' ?>"
                           href="index.php?page=home&kategori=<?= $k['id'] ?>">
                            <?= htmlspecialchars($k['namakategori']) ?>
=======
                while ($k = mysqli_fetch_assoc($kategori)) { 
                    $link_kategori = "index.php?page=home&kategori=" . $k['id'];
                    if (isset($_GET['search'])) { $link_kategori .= "&search=" . urlencode($_GET['search']); }
                    if (isset($_GET['sort'])) { $link_kategori .= "&sort=" . urlencode($_GET['sort']); }
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'active' : '' ?>" href="<?= $link_kategori ?>">
                            <?= $k['namakategori'] ?>
>>>>>>> 98b21cc60852a2f876ef3da950f9e6cb8f8c9438
                        </a>
                    </li>
                <?php } ?>
            </ul>

            <h6 class="fw-bold text-dark mb-3 text-uppercase small" style="letter-spacing:1px;">Urutan Harga</h6>
            <div class="mb-4">
                <div class="form-check mb-2">
<<<<<<< HEAD
                    <input class="form-check-input" type="radio" name="sortPrice" id="sortLow"
                        <?= (isset($_GET['sort']) && $_GET['sort'] == 'low_high') ? 'checked' : '' ?>
                        onclick="window.location.href='index.php?page=home<?= isset($_GET['kategori']) ? '&kategori='.$_GET['kategori'] : '' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>&sort=low_high'">
                    <label class="form-check-label text-secondary small" for="sortLow" style="cursor:pointer;">
                        Rendah &rarr; Tinggi
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sortPrice" id="sortHigh"
                        <?= (isset($_GET['sort']) && $_GET['sort'] == 'high_low') ? 'checked' : '' ?>
                        onclick="window.location.href='index.php?page=home<?= isset($_GET['kategori']) ? '&kategori='.$_GET['kategori'] : '' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>&sort=high_low'">
                    <label class="form-check-label text-secondary small" for="sortHigh" style="cursor:pointer;">
                        Tinggi &rarr; Rendah
                    </label>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3 text-uppercase small" style="letter-spacing:1px;">Total Koleksi</h6>
            <div class="text-muted small"><?= $total_buku ?> Koleksi Buku</div>
=======
                    <input class="form-check-input border border-dark border-bottom" type="radio" name="sortPrice" id="sortLow" <?= (isset($_GET['sort']) && $_GET['sort'] == 'low_high') ? 'checked' : '' ?> onclick="window.location.href='index.php?page=home<?= isset($_GET['kategori']) ? '&kategori='.$_GET['kategori'] : '' ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>&sort=low_high'">
                    <label class="form-check-label text-secondary small" for="sortLow" style="cursor: pointer;">Rendah &rarr; Tinggi</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input border border-dark border-bottom" type="radio" name="sortPrice" id="sortHigh" <?= (isset($_GET['sort']) && $_GET['sort'] == 'high_low') ? 'checked' : '' ?> onclick="window.location.href='index.php?page=home<?= isset($_GET['kategori']) ? '&kategori='.$_GET['kategori'] : '' ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>&sort=high_low'">
                    <label class="form-check-label text-secondary small" for="sortHigh" style="cursor: pointer;">Tinggi &rarr; Rendah</label>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3 text-uppercase small" style="letter-spacing: 1px;">Total Koleksi</h6>
            <div class="text-muted small"><i class="bi bi-journal-bookmark-fill me-1"></i> <?= $total_buku ?> Koleksi Buku</div>
>>>>>>> 98b21cc60852a2f876ef3da950f9e6cb8f8c9438
        </div>

        <!-- DAFTAR PRODUK -->
        <div class="col-md-9 mt-5">
            
            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                <div class="mb-3 text-muted small">
                    Hasil pencarian untuk kata kunci: <strong class="text-dark">"<?= htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') ?>"</strong>
                </div>
            <?php endif; ?>

            <div class="row row-cols-1 row-cols-lg-2 g-4">

                <?php if (mysqli_num_rows($produk) > 0) { ?>
                    <?php while ($p = mysqli_fetch_assoc($produk)) { ?>

                        <div class="col">
                            <div class="card h-100 product-card bg-white p-2">
                                <div class="row g-0 h-100 align-items-center">

                                    <!-- Cover Buku -->
                                    <div class="col-sm-4 text-center">
                                        <?php if (!empty($p['foto'])) { ?>
                                            <img src="assets/uploads/produk/<?= htmlspecialchars($p['foto']) ?>" 
                                                 class="img-fluid"
                                                 alt="<?= htmlspecialchars($p['namaproduk']) ?>">
                                        <?php } else { ?>
                                            <div class="d-flex align-items-center justify-content-center bg-light" 
                                                 style="height:200px; border-radius:4px;">
                                                <i class="bi bi-book" style="font-size:2rem; color:#cbd5e1;"></i>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <!-- Info Buku -->
                                    <div class="col-sm-8">
                                        <div class="card-body py-1 px-3 d-flex flex-column h-100 justify-content-between">
                                            <div>
<<<<<<< HEAD
                                                <!-- Judul -->
                                                <h6 class="fw-bold text-dark mb-1" 
                                                    style="font-size:0.95rem; line-height:1.3;">
                                                    <?= htmlspecialchars($p['namaproduk']) ?>
                                                </h6>

                                                <!-- Penulis & Kategori dari DB -->
                                                <p class="text-muted mb-2" style="font-size:0.75rem;">
                                                    <?= !empty($p['kontak_penulis']) ? htmlspecialchars($p['kontak_penulis']) : '-' ?>
                                                    <?php if (!empty($p['namakategori'])) { ?>
                                                        / <?= htmlspecialchars($p['namakategori']) ?>
                                                    <?php } ?>
=======
                                                <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.95rem; line-height: 1.3;" title="<?= $p['namaproduk'] ?>">
                                                    <?= $p['namaproduk'] ?>
                                                </h6>
                                                
                                                <p class="text-muted mb-1 text-truncate" style="font-size: 0.75rem;">
                                                    <i class="bi bi-person me-1"></i><?= !empty($p['penulis']) ? $p['penulis'] : 'Anonim' ?> 
                                                    <span class="mx-1">|</span> 
                                                    <span class="badge bg-secondary opacity-75"><?= $p['namakategori'] ?? 'Umum' ?></span>
>>>>>>> 98b21cc60852a2f876ef3da950f9e6cb8f8c9438
                                                </p>

                                                <!-- Badge Stok dari DB -->
                                                <div class="mb-2">
                                                    <?php if (intval($p['stok']) > 0) { ?>
                                                        <span class="badge-stok-ada">Tersedia</span>
                                                    <?php } else { ?>
                                                        <span class="badge-stok-habis">Stok Habis</span>
                                                    <?php } ?>
                                                </div>

                                                <!-- Harga dari DB -->
                                                <h5 class="fw-bold text-dark mb-3" style="font-size:1.05rem;">
                                                    Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                                </h5>
                                            </div>

                                            <a href="produkdetail.php?id=<?= $p['id'] ?>" 
                                               class="btn btn-outline-basket w-100 text-center py-2">
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
                        <div class="alert alert-light border text-center py-4 rounded-3">
                            <i class="bi bi-book display-6 text-muted mb-2 d-block"></i>
                            Buku tidak ditemukan di kategori atau kata kunci ini.
                        </div>
                    </div>
                <?php } ?>

            </div>

            <!-- PAGINATION -->
            <?php if ($total_halaman > 1) { ?>
                <nav class="mt-5 d-flex justify-content-center">
                    <ul class="pagination pagination-sm gap-1">

                        <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle border-0" 
                               href="<?= $base_url ?>halaman=<?= $current_page - 1 ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
                            <?php if ($i == 1 || $i == $total_halaman || ($i >= $current_page - 1 && $i <= $current_page + 1)) { ?>
                                <li class="page-item <?= ($current_page == $i) ? 'active' : '' ?>">
                                    <a class="page-link rounded-circle border-0" 
                                       href="<?= $base_url ?>halaman=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php } elseif ($i == 2 || $i == $total_halaman - 1) { ?>
                                <li class="page-item disabled">
                                    <a class="page-link rounded-circle border-0 bg-transparent" href="#">..</a>
                                </li>
                            <?php } ?>
                        <?php } ?>

                        <li class="page-item <?= ($current_page >= $total_halaman) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-circle border-0" 
                               href="<?= $base_url ?>halaman=<?= $current_page + 1 ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>

                    </ul>
                </nav>
            <?php } ?>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>