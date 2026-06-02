<?php include 'header.php'; ?>

<?php
$id = intval($_GET['id']);

$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    WHERE p.id = '$id'
"));

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan'); location='index.php';</script>";
    exit;
}

$kategori_id = $produk['kategori_id'];
$cerita_serupa = mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE p.id != '$id' AND p.kategori_id = '$kategori_id'
    LIMIT 4
");

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
    .text-orange { color: #f39c12; }
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

        <!-- KIRI: Detail Produk -->
        <div class="col-lg-9 col-md-8">
            <div class="detail-container position-relative">

                <div class="mb-4">
                    <a href="produk.php" class="btn btn-link text-decoration-none text-secondary p-0 d-inline-flex align-items-center fw-semibold small">
                        <i class="bi bi-arrow-left me-2 fs-5"></i> Kembali ke Produk
                    </a>
                </div>

                <div class="row g-4 mb-5">

                    <!-- Foto Buku -->
                    <div class="col-md-4 text-center text-md-start">
                        <?php if (!empty($produk['foto'])) { ?>
                            <img src="assets/uploads/produk/<?= htmlspecialchars($produk['foto']) ?>" 
                                 class="book-cover-large img-fluid"
                                 alt="<?= htmlspecialchars($produk['namaproduk']) ?>">
                        <?php } else { ?>
                            <div class="book-cover-large d-flex align-items-center justify-content-center bg-light" 
                                 style="max-width:220px;">
                                <i class="bi bi-book" style="font-size:4rem; color:#cbd5e1;"></i>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Info Buku -->
                    <div class="col-md-8 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Judul -->
                            <h2 class="fw-bold text-dark mb-1" style="font-size:1.8rem; letter-spacing:-0.5px;">
                                <?= htmlspecialchars($produk['namaproduk']) ?>
                            </h2>

                            <!-- Penulis dari kontak_penulis -->
                            <?php if (!empty($produk['kontak_penulis'])) { ?>
                                <p class="text-muted mb-2" style="font-size:0.9rem;">
                                    <i class="bi bi-person me-1"></i>
                                    <?= htmlspecialchars($produk['kontak_penulis']) ?>
                                </p>
                            <?php } ?>

                            <!-- Penerbit -->
                            <?php if (!empty($produk['penerbit'])) { ?>
                                <p class="text-muted mb-3" style="font-size:0.85rem;">
                                    <i class="bi bi-building me-1"></i>
                                    <?= htmlspecialchars($produk['penerbit']) ?>
                                </p>
                            <?php } ?>

                            <!-- Badge Status Stok -->
                            <div class="d-flex gap-2 mb-4">
                                <?php if (intval($produk['stok']) > 0) { ?>
                                    <span class="badge badge-status-available d-flex align-items-center px-3 py-2 rounded-2">
                                        <i class="bi bi-check2 me-1"></i> Tersedia
                                    </span>
                                <?php } else { ?>
                                    <span class="badge badge-stok-habis d-flex align-items-center px-3 py-2 rounded-2">
                                        <i class="bi bi-x-circle me-1"></i> Stok Habis
                                    </span>
                                <?php } ?>
                                <span class="badge badge-status-type d-flex align-items-center px-3 py-2 rounded-2">
                                    <i class="bi bi-box-seam me-1"></i> Fisik
                                </span>
                                <?php if (!empty($produk['namakategori'])) { ?>
                                    <span class="badge badge-status-type d-flex align-items-center px-3 py-2 rounded-2">
                                        <i class="bi bi-tag me-1"></i> 
                                        <?= htmlspecialchars($produk['namakategori']) ?>
                                    </span>
                                <?php } ?>
                            </div>

                            <!-- Harga -->
                            <h3 class="fw-black text-dark mb-0" style="font-weight:800; font-size:1.9rem;">
                                Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                            </h3>
                        </div>

                        <!-- Qty & Tombol Beli -->
                        <div class="d-flex align-items-center justify-content-between mt-4 border-top pt-3">
                            <?php if (intval($produk['stok']) > 0) { ?>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-secondary small fw-bold">Jumlah Beli</span>
                                    <div class="d-flex align-items-center qty-counter">
                                        <button type="button" class="qty-btn" onclick="kurangKuantitas()">-</button>
                                        <input type="number" id="jumlah_beli" class="qty-input text-center" 
                                               value="1" min="1" 
                                               max="<?= intval($produk['stok']) ?>" readonly>
                                        <button type="button" class="qty-btn" onclick="tambahKuantitas()">+</button>
                                    </div>
                                    <span class="text-muted small">
                                        Stok: <strong><?= intval($produk['stok']) ?></strong>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <button onclick="eksekusiPembelian()" class="btn btn-buy-now px-4 py-2 shadow-sm">
                                        <i class="bi bi-wallet2 me-2"></i> Beli Sekarang
                                    </button>
                                </div>
                            <?php } else { ?>
                                <div class="w-100 text-center py-2">
                                    <span class="text-danger fw-semibold">
                                        <i class="bi bi-exclamation-circle me-1"></i> 
                                        Stok habis, produk tidak tersedia untuk dibeli saat ini.
                                    </span>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>

                <!-- Sinopsis / Deskripsi -->
                <?php if (!empty($produk['deskripsi'])) { ?>
                    <div class="mb-5">
                        <h5 class="fw-bold text-dark mb-3">Sinopsis</h5>
                        <p class="text-secondary lh-lg" style="font-size:0.95rem; text-align:justify;">
                            <?= nl2br(htmlspecialchars($produk['deskripsi'])) ?>
                        </p>
                    </div>
                <?php } ?>

                <!-- Detail / Spesifikasi Buku -->
                <div>
                    <h5 class="fw-bold text-dark mb-3">Detail Buku</h5>
                    <div class="container-fluid spec-table bg-white p-0">
                        <div class="row g-0">
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Penerbit</div>
                                <div class="spec-value">
                                    <?= !empty($produk['penerbit']) ? htmlspecialchars($produk['penerbit']) : '-' ?>
                                </div>
                            </div>
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Stok Buku</div>
                                <div class="spec-value" id="stok_maksimal">
                                    <?= intval($produk['stok']) ?>
                                </div>
                            </div>
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Kategori</div>
                                <div class="spec-value">
                                    <?= !empty($produk['namakategori']) ? htmlspecialchars($produk['namakategori']) : '-' ?>
                                </div>
                            </div>
                        </div>
                        <div class="row g-0">
                            <div class="col-4 spec-cell" style="border-bottom:none;">
                                <div class="spec-title">Dimensi</div>
                                <div class="spec-value">
                                    <?= !empty($produk['dimensi']) ? htmlspecialchars($produk['dimensi']) : '-' ?>
                                </div>
                            </div>
                            <div class="col-4 spec-cell" style="border-bottom:none;">
                                <div class="spec-title">Berat</div>
                                <div class="spec-value">
                                    <?= !empty($produk['berat']) ? htmlspecialchars($produk['berat']) : '-' ?>
                                </div>
                            </div>
                            <div class="col-4 spec-cell" style="border-bottom:none; border-right:none;">
                                <div class="spec-title">Nomor Seri</div>
                                <div class="spec-value">
                                    <?= !empty($produk['nomor_seri']) ? htmlspecialchars($produk['nomor_seri']) : '-' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- KANAN: Cerita Serupa -->
        <div class="col-lg-3 col-md-4">
            <h5 class="fw-bold text-dark mb-3">Buku Serupa</h5>
            <div class="d-flex flex-column gap-3">
                <?php 
                $ada_serupa = false;
                while ($cs = mysqli_fetch_assoc($cerita_serupa)) { 
                    $ada_serupa = true;
                ?>
                    <div class="card similar-card p-2">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <?php if (!empty($cs['foto'])) { ?>
                                    <img src="assets/uploads/produk/<?= htmlspecialchars($cs['foto']) ?>" 
                                         class="similar-cover"
                                         alt="<?= htmlspecialchars($cs['namaproduk']) ?>">
                                <?php } else { ?>
                                    <div class="similar-cover d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-book" style="font-size:1.5rem; color:#cbd5e1;"></i>
                                    </div>
                                <?php } ?>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h6 class="fw-bold text-dark mb-1 text-truncate" 
                                    style="font-size:0.85rem;" 
                                    title="<?= htmlspecialchars($cs['namaproduk']) ?>">
                                    <a href="produkdetail.php?id=<?= $cs['id'] ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($cs['namaproduk']) ?>
                                    </a>
                                </h6>
                                <?php if (!empty($cs['kontak_penulis'])) { ?>
                                    <p class="text-muted mb-1" style="font-size:0.75rem;">
                                        <?= htmlspecialchars($cs['kontak_penulis']) ?>
                                    </p>
                                <?php } ?>
                                <?php if (!empty($cs['namakategori'])) { ?>
                                    <p class="text-muted mb-1" style="font-size:0.72rem;">
                                        <i class="bi bi-tag me-1"></i>
                                        <?= htmlspecialchars($cs['namakategori']) ?>
                                    </p>
                                <?php } ?>
                                <span class="fw-bold text-dark" style="font-size:0.85rem;">
                                    Rp <?= number_format($cs['harga'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!$ada_serupa) { ?>
                    <p class="text-muted small">Tidak ada buku serupa.</p>
                <?php } ?>
            </div>
        </div>

    </div>
</div>

<script>
    const stokMax = parseInt(document.getElementById('stok_maksimal')?.innerText) || 0;
    const inputQty = document.getElementById('jumlah_beli');

    function tambahKuantitas() {
        let currentVal = parseInt(inputQty.value);
        if (currentVal < stokMax) {
            inputQty.value = currentVal + 1;
        } else {
            alert('Maaf, jumlah pembelian tidak boleh melebihi stok tersedia (' + stokMax + ' buku).');
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
</script>

<?php include 'footer.php'; ?>