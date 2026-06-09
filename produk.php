<?php include 'header.php'; ?>

<?php
$limit = 8;
$current_page = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
if ($current_page < 1) { $current_page = 1; }
$offset = ($current_page - 1) * $limit;

$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY namakategori ASC");

$where = "";
if (isset($_GET['kategori'])) {
    $kategori_id = intval($_GET['kategori']);
    $where = "WHERE p.kategori_id='$kategori_id'";
}

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $where .= $where ? " AND p.namaproduk LIKE '%$search%'" : "WHERE p.namaproduk LIKE '%$search%'";
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
$base_url = "produk.php?" . http_build_query($query_params) . "&";
?>

<style>
    body { background-color: #f8fafc; }
    .bg-bukupedia-blue {
        background: linear-gradient(180deg, #5a67d8 0%, #4c51bf 100%);
        border-radius: 0 0 30px 30px;
    }
    .inner-banner-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 3.5rem 2rem;
    }
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
    .filter-sidebar .nav-link:hover { color: #5a67d8; }
    .filter-sidebar .nav-link.active {
        color: #5a67d8;
        font-weight: 700;
        background-color: transparent;
    }
    .book-card-item {
        border: none;
        border-radius: 12px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        padding: 1.25rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .book-card-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(90,103,216,0.06);
    }
    .book-cover-wrap img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.08);
    }
    .book-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1a202c;
        line-height: 1.3;
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

<div class="w-100 bg-bukupedia-blue py-5 mb-5 text-center text-white">
    <div class="container px-4 px-md-5">
        <div class="inner-banner-card mx-auto" style="max-width: 960px;">
            <h1 class="fw-bold mb-2 display-6" style="letter-spacing: -0.5px;">Daftar Buku</h1>
            <p class="mb-0 text-white-50">Temukan koleksi buku favoritmu</p>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">

        <!-- SIDEBAR FILTER -->
        <div class="col-md-3 filter-sidebar d-none d-md-block pe-4">

            <h6 class="sidebar-title mb-3">Kategori</h6>
            <ul class="nav flex-column mb-4">
                <li class="nav-item">
                    <a class="nav-link <?= !isset($_GET['kategori']) ? 'active' : '' ?>" href="produk.php">
                        Semua Genre
                    </a>
                </li>
                <?php
                mysqli_data_seek($kategori, 0);
                while ($k = mysqli_fetch_assoc($kategori)) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'active' : '' ?>"
                           href="produk.php?kategori=<?= $k['id'] ?>">
                            <?= htmlspecialchars($k['namakategori']) ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>

            <h6 class="sidebar-title mb-3">Urutan Harga</h6>
            <div class="mb-4">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="sortPrice" id="lowToHigh"
                        <?= (isset($_GET['sort']) && $_GET['sort'] == 'low_high') ? 'checked' : '' ?>
                        onclick="window.location.href='<?= $base_url ?>sort=low_high'">
                    <label class="form-check-label text-secondary small fw-medium" for="lowToHigh" style="cursor:pointer;">
                        Rendah &rarr; Tinggi
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sortPrice" id="highToLow"
                        <?= (isset($_GET['sort']) && $_GET['sort'] == 'high_low') ? 'checked' : '' ?>
                        onclick="window.location.href='<?= $base_url ?>sort=high_low'">
                    <label class="form-check-label text-secondary small fw-medium" for="highToLow" style="cursor:pointer;">
                        Tinggi &rarr; Rendah
                    </label>
                </div>
            </div>

            <h6 class="sidebar-title mb-2">Total Koleksi</h6>
            <div class="text-muted small fw-semibold">
                <i class="bi bi-journal-bookmark-fill me-1"></i> 
                <?= $total_buku ?> Koleksi Tersedia
            </div>
        </div>

        <!-- DAFTAR PRODUK -->
        <div class="col-md-9">
            <div class="row row-cols-1 row-cols-sm-2 g-4">

                <?php if (mysqli_num_rows($produk) > 0) { ?>
                    <?php while ($p = mysqli_fetch_assoc($produk)) { ?>

                        <div class="col">
                            <div class="book-card-item h-100">
                                <div class="row g-0 h-100 align-items-center">

                                    <!-- Cover Buku -->
                                    <div class="col-4 text-center">
                                        <div class="book-cover-wrap">
                                            <?php if (!empty($p['foto'])) { ?>
                                                <img src="assets/uploads/produk/<?= htmlspecialchars($p['foto']) ?>" 
                                                     alt="<?= htmlspecialchars($p['namaproduk']) ?>">
                                            <?php } else { ?>
                                                <div class="d-flex align-items-center justify-content-center bg-light" 
                                                     style="height:190px; border-radius:6px;">
                                                    <i class="bi bi-book" style="font-size:2.5rem; color:#cbd5e1;"></i>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Info Buku -->
                                    <div class="col-8">
                                        <div class="ps-3 d-flex flex-column h-100 justify-content-between">
                                            <div>
                                                <!-- Judul -->
                                                <h5 class="book-title mb-1 text-truncate" 
                                                    title="<?= htmlspecialchars($p['namaproduk']) ?>">
                                                    <?= htmlspecialchars($p['namaproduk']) ?>
                                                </h5>

                                                <!-- Penulis dari DB -->
                                                <p class="text-muted mb-2" style="font-size:0.8rem; font-weight:500;">
                                                    <?= !empty($p['kontak_penulis']) ? htmlspecialchars($p['kontak_penulis']) : '-' ?>
                                                </p>

                                                <!-- Kategori & Stok dari DB -->
                                                <div class="d-flex align-items-center gap-1 mb-2 flex-wrap">
                                                    <?php if (!empty($p['namakategori'])) { ?>
                                                        <span class="badge-kategori">
                                                            <?= htmlspecialchars($p['namakategori']) ?>
                                                        </span>
                                                    <?php } ?>
                                                    <?php if (intval($p['stok']) > 0) { ?>
                                                        <span class="badge-stok-ada">Tersedia</span>
                                                    <?php } else { ?>
                                                        <span class="badge-stok-habis">Stok Habis</span>
                                                    <?php } ?>
                                                </div>

                                                <!-- Harga dari DB -->
                                                <h6 class="fw-bold text-dark mb-3" style="font-size:1.1rem;">
                                                    Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                                </h6>
                                            </div>

                                            <a href="produkdetail.php?id=<?= $p['id'] ?>" 
                                               class="btn btn-action-basket text-center w-100 py-2">
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
                            <p class="text-muted small mb-0">Silakan pilih kategori lain atau periksa kembali kata kunci pencarian.</p>
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