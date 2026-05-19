<?php include 'header.php'; ?>

<?php
$id = intval($_GET['id']);

// Ambil data produk detail beserta spesifikasi tambahan jika ada di database
$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    WHERE p.id='$id'
"));

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan'); location='index.php';</script>";
    exit;
}

// CERITA SERUPA (Sisi Kanan UI) - Mengambil buku dari kategori yang sama
$kategori_id = $produk['kategori_id'];
$cerita_serupa = mysqli_query($koneksi, "
    SELECT p.*, k.namakategori 
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE p.id != '$id' AND p.kategori_id = '$kategori_id'
    LIMIT 4
");

// Jika kategori serupa kurang dari 4, ambil acak sisanya
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
    body {
        background-color: #f4f6fa;
    }
    .detail-container {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        padding: 2.5rem;
    }
    .book-cover-large {
        width: 100%;
        max-width: 220px;
        height: 310px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(74, 99, 184, 0.25);
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
        box-shadow: 0 4px 14px rgba(74, 99, 184, 0.4);
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
    .spec-cell:last-child {
        border-right: none;
    }
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
    /* Sisi Kanan: Cerita Serupa Card */
    .similar-card {
        background: #ffffff;
        border-radius: 14px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: transform 0.2s;
    }
    .similar-card:hover {
        transform: translateY(-3px);
    }
    .similar-cover {
        width: 75px;
        height: 105px;
        object-fit: cover;
        border-radius: 6px;
    }
    .text-orange {
        color: #f39c12;
    }
    
    /* GAYA BARU KUNCI PENGATUR JUMLAH BELI */
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
    .qty-btn:hover {
        background: #e2e8f0;
    }
    .qty-input {
        border: none;
        background: transparent;
        text-center: center;
        font-weight: 600;
        color: #1e293b;
        width: 45px;
        outline: none;
    }
    /* Menghilangkan panah spinner bawaan browser pada input number */
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<div class="container py-4">
    <div class="row g-4">
        
        <div class="col-lg-9 col-md-8">
            <div class="detail-container position-relative">
                
                <div class="mb-4">
                    <a href="produk.php" class="btn btn-link text-decoration-none text-secondary p-0 d-inline-flex align-items-center fw-semibold small">
                        <i class="bi bi-arrow-left me-2 fs-5"></i> Kembali ke Produk
                    </a>
                </div>

                <div class="row g-4 mb-5">
                    
                    <div class="col-md-4 text-center text-md-start">
                        <?php if ($produk['foto']) { ?>
                            <img src="assets/uploads/produk/<?= $produk['foto'] ?>" class="book-cover-large img-fluid">
                        <?php } else { ?>
                            <img src="https://via.placeholder.com/220x310?text=No+Cover" class="book-cover-large img-fluid">
                        <?php } ?>
                    </div>
                    
                    <div class="col-md-8 d-flex flex-column justify-content-between">
                        <div>
                            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.8rem; letter-spacing: -0.5px;">
                                <?= $produk['namaproduk'] ?>
                            </h2>
                            
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                <?= $produk['penulis'] ?? 'ANNE RODNEY' ?> <span class="mx-2">&bull;</span> <?= $produk['tanggal_terbit'] ?? '30 Jun 2024' ?>
                            </p>
                            
                            <div class="text-orange mb-3" style="font-size: 0.85rem;">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star text-muted"></i>
                            </div>
                            
                            <div class="d-flex gap-2 mb-4">
                                <span class="badge badge-status-available d-flex align-items-center px-3 py-2 rounded-2">
                                    <i class="bi bi-check2 me-1"></i> Tersedia
                                </span>
                                <span class="badge badge-status-type d-flex align-items-center px-3 py-2 rounded-2">
                                    <i class="bi bi-box-seam me-1"></i> Fisik
                                </span>
                            </div>
                            
                            <h3 class="fw-black text-dark mb-0" style="font-weight: 800; font-size: 1.9rem;">
                                Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                            </h3>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mt-4 border-top pt-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-secondary small fw-bold">Jumlah Beli</span>
                                <div class="d-flex align-items-center qty-counter">
                                    <button type="button" class="qty-btn" onclick="kurangKuantitas()">-</button>
                                    <input type="number" id="jumlah_beli" class="qty-input text-center" value="1" min="1" max="<?= $produk['stok'] ?>" readonly>
                                    <button type="button" class="qty-btn" onclick="tambahKuantitas()">+</button>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button onclick="eksekusiPembelian()" class="btn btn-buy-now px-4 py-2 shadow-sm">
                                    <i class="bi bi-wallet2 me-2"></i> Beli Sekarang
                                </button>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="mb-5">
                    <h5 class="fw-bold text-dark mb-3">Sinopsis</h5>
                    <p class="text-secondary lh-lg" style="font-size: 0.95rem; text-align: justify;">
                        <?= $produk['deskripsi'] ? nl2br($produk['deskripsi']) : '....' ?>
                    </p>
                </div>

                <div>
                    <h5 class="fw-bold text-dark mb-3">Detail Buku</h5>
                    
                    <div class="container-fluid spec-table bg-white p-0">
                        <div class="row g-0">
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Penerbit</div>
                                <div class="spec-value"><?= $produk['penerbit'] ?? 'Pustakawan Populer Gramedia' ?></div>
                            </div>
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Stok Buku</div>
                                <div class="spec-value" id="stok_maksimal"><?= $produk['stok'] ?></div>
                            </div>
                            <div class="col-4 spec-cell">
                                <div class="spec-title">Kategori</div>
                                <div class="spec-value"><?= $produk['namakategori'] ?></div>
                            </div>
                        </div>
                        <div class="row g-0">
                            <div class="col-4 spec-cell" style="border-bottom: none;">
                                <div class="spec-title">Dimensi</div>
                                <div class="spec-value"><?= $produk['dimensi'] ?? '20 Cm X 25 Cm X 3 Cm' ?></div>
                            </div>
                            <div class="col-4 spec-cell" style="border-bottom: none;">
                                <div class="spec-title">Berat</div>
                                <div class="spec-value"><?= $produk['berat'] ?? '10g' ?></div>
                            </div>
                            <div class="col-4 spec-cell" style="border-bottom: none; border-right: none;">
                                <div class="spec-title">Nomor Seri</div>
                                <div class="spec-value"><?= $produk['isbn'] ?? '978-1234-5678-90' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <h5 class="fw-bold text-dark mb-3">Cerita Serupa</h5>
            <div class="d-flex flex-column gap-3">
                <?php while ($cs = mysqli_fetch_assoc($cerita_serupa)) { ?>
                    <div class="card similar-card p-2">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <?php if ($cs['foto']) { ?>
                                    <img src="assets/uploads/produk/<?= $cs['foto'] ?>" class="similar-cover">
                                <?php } else { ?>
                                    <img src="https://via.placeholder.com/75x105?text=Cover" class="similar-cover">
                                <?php } ?>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.85rem;" title="<?= $cs['namaproduk'] ?>">
                                    <a href="produkdetail.php?id=<?= $cs['id'] ?>" class="text-decoration-none text-dark">
                                        <?= $cs['namaproduk'] ?>
                                    </a>
                                </h6>
                                <p class="text-muted mb-1" style="font-size: 0.75rem;"><?= $cs['penulis'] ?? 'Penulis Komunitas' ?></p>
                                <div class="d-flex align-items-center mb-1" style="font-size: 0.7 shadow-sm;">
                                    <span class="text-orange me-1"><i class="bi bi-star-fill"></i></span>
                                    <span class="text-muted">293 Terjual</span>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                    Rp <?= number_format($cs['harga'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>
</div>

<script>
    const stokMax = parseInt(document.getElementById('stok_maksimal').innerText) || 1;
    const inputQty = document.getElementById('jumlah_beli');

    function tambahKuantitas() {
        let currentVal = parseInt(inputQty.value);
        if (currentVal < stokMax) {
            inputQty.value = currentVal + 1;
        } else {
            alert('Maaf, jumlah pembelian tidak boleh melebihi stok yang tersedia (' + stokMax + ' buku).');
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
        // Melempar data id produk beserta qty yang dipilih ke file pemroses keranjang
        window.location.href = "keranjangtambah.php?id=<?= $produk['id'] ?>&qty=" + qtyVal;
    }
</script>

<?php include 'footer.php'; ?>