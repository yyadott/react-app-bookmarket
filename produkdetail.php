<?php 
include 'header.php'; 

// Mengamankan ID Produk dari manipulasi URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data detail produk beserta nama kategorinya
$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    WHERE p.id = '$id'
"));

if (!$produk) {
    echo "<script>alert('Produk buku tidak ditemukan'); location='index.php';</script>";
    exit;
}

$kategori_id = $produk['kategori_id'];

// CORE REKOMENDASI: Ambil 4 buku serupa berdasarkan kategori yang sama
$cerita_serupa = mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE p.id != '$id' AND p.kategori_id = '$kategori_id'
    LIMIT 4
");

// FALLBACK REKOMENDASI: Jika kategori yang sama kurang dari 4, campur acak dengan buku lain
if (mysqli_num_rows($cerita_serupa) < 4) {
    $cerita_serupa = mysqli_query($koneksi, "
        SELECT p.*, k.namakategori 
        FROM produk p
        LEFT JOIN kategori k ON p.kategori_id = k.id
        WHERE p.id != '$id'
        ORDER BY RAND()
        LIMIT 4
    ");
}

// Validasi status otentikasi login user
$sudah_login = isset($_SESSION['user']);
?>

<style>
    body { background-color: #f4f6fa; }
    .detail-container {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        padding: 2.5rem;
    }
    .book-cover-large {
        width: 100%;
        max-width: 220px;
        height: 310px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(74,99,184,0.25);
    }
    .badge-status-available {
        background-color: #e8f9ee;
        color: #27ae60;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .badge-status-type {
        background-color: #f0f3ff;
        color: #4a63b8;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .btn-buy-now {
        background-color: #4a63b8;
        color: #ffffff;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        box-shadow: 0 4px 14px rgba(74,99,184,0.4);
        transition: all 0.2s;
    }
    .btn-buy-now:hover {
        background-color: #384fa1;
        color: #ffffff;
        transform: translateY(-2px);
    }
    .btn-need-login {
        background-color: #e53e3e;
        color: #ffffff;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        box-shadow: 0 4px 14px rgba(229,62,62,0.3);
        transition: all 0.2s;
    }
    .btn-need-login:hover {
        background-color: #c53030;
        color: #ffffff;
        transform: translateY(-2px);
    }
    .spec-table {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eaeaea;
    }
    .spec-cell {
        border-bottom: 1px solid #eaeaea;
        border-right: 1px solid #eaeaea;
        padding: 1rem;
    }
    .spec-cell:last-child { border-right: none; }
    .spec-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #a0a0a0;
        font-weight: 700;
        margin-bottom: 0.25rem;
        letter-spacing: 0.5px;
    }
    .spec-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .similar-card {
        background: #ffffff;
        border-radius: 14px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: transform 0.2s;
    }
    .similar-card:hover { transform: translateY(-3px); }
    .similar-cover {
        width: 75px;
        height: 105px;
        object-fit: cover;
        border-radius: 6px;
    }
    .qty-counter {
        max-width: 130px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        overflow: hidden;
        background: #f8fafc;
    }
    .qty-btn {
        background: transparent;
        border: none;
        padding: 0.4rem 0.75rem;
        color: #4a63b8;
        font-weight: bold;
        transition: background 0.2s;
    }
    .qty-btn:hover { background: #e2e8f0; }
    .qty-input {
        border: none;
        background: transparent;
        font-weight: 600;
        color: #1e293b;
        width: 45px;
        outline: none;
        text-align: center;
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .badge-stok-habis {
        background-color: #fdecea;
        color: #e74c3c;
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>

<div class="container py-4">
    <div class="row g-4">

        <div class="col-lg-9 col-md-8">
            <div class="detail-container position-relative">

                <div class="mb-4">
                    <a href="index.php" class="btn btn-link text-decoration-none text-secondary p-0 d-inline-flex align-items-center fw-semibold small">
                        <i class="bi bi-arrow-left me-2 fs-5"></i> Kembali ke Beranda
                    </a>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-4 text-center text-md-start">
                        <?php if (!empty($produk['foto'])) { ?>
                            <img src="assets/uploads/produk/<?= htmlspecialchars($produk['foto']) ?>" 
                                 class="book-cover-large img-fluid"
                                 alt="<?= htmlspecialchars($produk['namaproduk']) ?>">
                        <?php } else { ?>
                            <div class="book-cover-large d-flex align-items-center justify-content-center bg-light" style="max-width:220px; height:310px; margin: 0 auto;">
                                <i class="bi bi-book" style="font-size:4rem; color:#cbd5e1;"></i>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-8 d-flex flex-column justify-content-between">
                        <div>
                            <h2 class="fw-bold text-dark mb-1" style="font-size:1.8rem; letter-spacing:-0.5px;">
                                <?= htmlspecialchars($produk['namaproduk']) ?>
                            </h2>

                            <?php if (!empty($produk['kontak_penulis'])) { ?>
                                <p class="text-muted mb-2" style="font-size:0.9rem;">
                                    <i class="bi bi-person me-1"></i> Penulis: <strong><?= htmlspecialchars($produk['kontak_penulis']) ?></strong>
                                </p>
                            <?php } ?>

                            <div class="d-flex gap-2 mb-4 flex-wrap">
                                <?php if (intval($produk['stok']) > 0) { ?>
                                    <span class="badge badge-status-available d-flex align-items-center px-3 py-2 rounded-2">
                                        <i class="bi bi-check2 me-1"></i> Tersedia
                                    </span>
                                    <?php if (!$sudah_login) { ?>
                                        <span class="badge bg-warning text-dark d-flex align-items-center px-3 py-2 rounded-2 fw-semibold">
                                            <i class="bi bi-lock-fill me-1"></i> Harus Login
                                        </span>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="badge badge-stok-habis d-flex align-items-center px-3 py-2 rounded-2">
                                        <i class="bi bi-x-circle me-1"></i> Stok Habis
                                    </span>
                                <?php } ?>
                                <span class="badge badge-status-type d-flex align-items-center px-3 py-2 rounded-2">
                                    <i class="bi bi-box-seam me-1"></i> Buku Fisik
                                </span>
                                <?php if (!empty($produk['namakategori'])) { ?>
                                    <span class="badge badge-status-type d-flex align-items-center px-3 py-2 rounded-2">
                                        <i class="bi bi-tag me-1"></i> <?= htmlspecialchars($produk['namakategori']) ?>
                                    </span>
                                <?php } ?>
                            </div>

                            <h3 class="fw-black text-primary mb-0" style="font-weight:800; font-size:1.9rem;">
                                Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                            </h3>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 border-top pt-3 flex-wrap gap-3">
                            <?php if (intval($produk['stok']) > 0) { ?>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-secondary small fw-bold">Jumlah</span>
                                    <div class="d-flex align-items-center qty-counter">
                                        <button type="button" class="qty-btn" onclick="<?= $sudah_login ? 'kurangKuantitas()' : 'peringatanLogin()' ?>">-</button>
                                        <input type="number" id="jumlah_beli" class="qty-input text-center" value="1" readonly>
                                        <button type="button" class="qty-btn" onclick="<?= $sudah_login ? 'tambahKuantitas()' : 'peringatanLogin()' ?>">+</button>
                                    </div>
                                    <span class="text-muted small">
                                        Sisa Stok: <strong><?= intval($produk['stok']) ?></strong>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <?php if ($sudah_login) { ?>
                                        <button onclick="eksekusiPembelian()" class="btn btn-buy-now px-4 py-2 shadow-sm">
                                            <i class="bi bi-cart-plus me-2"></i> Masukkan Keranjang
                                        </button>
                                    <?php } else { ?>
                                        <button onclick="peringatanLogin()" class="btn btn-need-login px-4 py-2 shadow-sm">
                                            <i class="bi bi-box-arrow-in-right me-2"></i> Login untuk Membeli
                                        </button>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="w-100 text-center py-2 bg-light rounded-3 border border-danger-subtle">
                                    <span class="text-danger fw-semibold">
                                        <i class="bi bi-exclamation-circle me-1"></i> Maaf, stok buku sedang kosong. Cek kembali beberapa saat lagi!
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($produk['deskripsi'])) { ?>
                    <div class="mb-5">
                        <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Sinopsis Buku</h5>
                        <p class="text-secondary lh-lg" style="font-size:0.95rem; text-align:justify;">
                            <?= nl2br(htmlspecialchars($produk['deskripsi'])) ?>
                        </p>
                    </div>
                <?php } ?>

                <div>
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Detail Spesifikasi</h5>
                    <div class="container-fluid spec-table bg-white p-0">
                        <div class="row g-0">
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Penerbit</div>
                                <div class="spec-value"><?= !empty($produk['penerbit']) ? htmlspecialchars($produk['penerbit']) : '-' ?></div>
                            </div>
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Ketersediaan</div>
                                <div class="spec-value"><?= intval($produk['stok']) ?> Buku</div>
                            </div>
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Kategori</div>
                                <div class="spec-value"><?= !empty($produk['namakategori']) ? htmlspecialchars($produk['namakategori']) : '-' ?></div>
                            </div>
                        </div>
                        <div class="row g-0">
                            <div class="col-4 spec-cell" style="border-bottom:none;">
                                <div class="spec-title">Dimensi</div>
                                <div class="spec-value"><?= !empty($produk['dimensi']) ? htmlspecialchars($produk['dimensi']) : '-' ?></div>
                            </div>
                            <div class="col-4 spec-cell" style="border-bottom:none;">
                                <div class="spec-title">Berat</div>
                                <div class="spec-value"><?= !empty($produk['berat']) ? intval($produk['berat']).' gram' : '-' ?></div>
                            </div>
                            <div class="col-4 spec-cell" style="border-bottom:none; border-right:none;">
                                <div class="spec-title">ISBN / Nomor Seri</div>
                                <div class="spec-value"><?= !empty($produk['nomor_seri']) ? htmlspecialchars($produk['nomor_seri']) : '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

       <div class="col-lg-3 col-md-4">
    <div class="p-3 bg-white rounded-4 shadow-sm border border-light-subtle">
        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2" style="font-size: 1.1rem;">
            <i class="bi bi-stars text-warning fs-5"></i> Rekomendasi Buku
        </h5>
        <div class="d-flex flex-column gap-3">
            <?php 
            $ada_serupa = false;
            while ($cs = mysqli_fetch_assoc($cerita_serupa)) { 
                $ada_serupa = true;
            ?>
                <div class="card similar-card border-0 bg-light-subtle p-2 rounded-3 border border-light">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <?php if (!empty($cs['foto'])) { ?>
                                <img src="assets/uploads/produk/<?= htmlspecialchars($cs['foto']) ?>" 
                                     class="similar-cover rounded-2"
                                     style="width: 65px; height: 95px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.05);"
                                     alt="<?= htmlspecialchars($cs['namaproduk']) ?>">
                            <?php } else { ?>
                                <div class="similar-cover d-flex align-items-center justify-content-center bg-light rounded-2" style="width: 65px; height: 95px;">
                                    <i class="bi bi-book text-muted" style="font-size:1.2rem;"></i>
                                </div>
                            <?php } ?>
                        </div>
                        
                        <div class="flex-grow-1 min-w-0 pt-1">
                            <h6 class="fw-bold mb-1 text-truncate" style="font-size:0.88rem;">
                                <a href="produkdetail.php?id=<?= $cs['id'] ?>" class="text-decoration-none text-dark hover-primary" title="<?= htmlspecialchars($cs['namaproduk']) ?>">
                                    <?= htmlspecialchars($cs['namaproduk']) ?>
                                </a>
                            </h6>
                            
                            <p class="text-muted mb-1 text-truncate" style="font-size:0.75rem;">
                                <i class="bi bi-person small"></i> <?= !empty($cs['kontak_penulis']) ? htmlspecialchars($cs['kontak_penulis']) : 'Anonim' ?>
                            </p>
                            
                            <div class="mb-1">
                                <?php if (!empty($cs['namakategori'])) { ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.65rem; font-weight: 600; padding: 3px 8px;">
                                        <?= htmlspecialchars($cs['namakategori']) ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill" style="font-size: 0.65rem; font-weight: 600; padding: 3px 8px;">
                                        Umum
                                    </span>
                                <?php } ?>
                            </div>
                            
                            <span class="fw-bold text-danger d-block" style="font-size:0.9rem;">
                                Rp <?= number_format($cs['harga'], 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (!$ada_serupa) { ?>
                <div class="text-center py-4 text-muted small italic">
                    <i class="bi bi-book-half display-6 d-block mb-2 text-black-50"></i>
                    Belum ada rekomendasi sejenis.
                </div>
            <?php } ?>
        </div>
    </div>
    </div>
</div>

<script>
    const stokMax = <?= intval($produk['stok']) ?>;
    const inputQty = document.getElementById('jumlah_beli');

    function tambahKuantitas() {
        let currentVal = parseInt(inputQty.value);
        if (currentVal < stokMax) {
            inputQty.value = currentVal + 1;
        } else {
            alert('Maaf, stok hanya tersedia ' + stokMax + ' buku saja.');
        }
    }

    function kurangKuantitas() {
        let currentVal = parseInt(inputQty.value);
        if (currentVal > 1) {
            inputQty.value = currentVal - 1;
        }
    }

    function eksekusiPembelian() {
        const qtyVal = inputQty.value;
        window.location.href = "keranjangtambah.php?id=<?= $produk['id'] ?>&qty=" + qtyVal;
    }

    function peringatanLogin() {
        alert('Silakan login terlebih dahulu untuk mulai memesan buku pilihan Anda.');
        window.location.href = "login.php";
    }
</script>
 
<?php include 'footer.php'; ?>