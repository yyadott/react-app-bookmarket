<?php include 'header.php'; ?>

<?php
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// HAPUS ITEM
if (isset($_GET['hapus'])) {
    $hapus = intval($_GET['hapus']);
    unset($_SESSION['keranjang'][$hapus]);
    echo "
        <script>
            alert('Produk dihapus dari keranjang');
            location='keranjang.php';
        </script>
    ";
}

// UPDATE QTY
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id  = intval($id);
        $qty = intval($qty);

        if ($qty <= 0) {
            unset($_SESSION['keranjang'][$id]);
        } else {
            $_SESSION['keranjang'][$id] = $qty;
        }
    }
    echo "
        <script>
            alert('Keranjang berhasil diupdate');
            location='keranjang.php';
        </script>
    ";
}

$keranjang = $_SESSION['keranjang'];
?>

<style>
    body {
        background-color: #f8fafc;
    }
    /* HERO BANNER TOP (Sesuai Gambar UI Atas) */
    .bg-checkout-blue {
        background: linear-gradient(180deg, #5a67d8 0%, #4c51bf 100%);
        border-radius: 0 0 30px 30px;
    }
    .inner-checkout-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 3.5rem 2rem;
    }

    /* TABEL PRODUK BARU */
    .cart-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        padding: 1.5rem;
    }
    .table-header-text {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #a0aec0;
        letter-spacing: 0.5px;
    }
    .cart-item-row {
        border-bottom: 1px solid #edf2f7;
        padding: 1.25rem 0;
    }
    .cart-item-row:last-child {
        border-bottom: none;
    }
    .book-cover-thumb {
        width: 65px;
        height: 90px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .cart-book-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a202c;
    }

    /* ACTION BUTTONS & QTY COUNTER */
    .btn-qty-adjust {
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #718096;
        width: 32px;
        height: 32px;
        font-weight: bold;
        transition: all 0.2s;
    }
    .btn-qty-adjust:hover {
        background-color: #f7fafc;
        color: #5a67d8;
    }
    .qty-display-input {
        width: 45px;
        height: 32px;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        border-left: none;
        border-right: none;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .btn-utility-grey {
        background-color: #edf2f7;
        color: #4a5568;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        border-radius: 4px;
        padding: 0.5rem 1.2rem;
    }
    .btn-utility-grey:hover {
        background-color: #e2e8f0;
    }

    /* RINGKASAN PEMESANAN CARD (SISI KANAN UI) */
    .summary-card {
        background: #ffffff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        padding: 2rem;
    }
    .btn-promo-apply {
        background-color: #f7fafc;
        border: 1px solid #edf2f7;
        color: #2d3748;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 6px;
    }
    .btn-checkout-primary {
        background-color: #5a67d8;
        color: #ffffff;
        font-weight: 700;
        padding: 0.75rem;
        border-radius: 8px;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-checkout-primary:hover {
        background-color: #4c51bf;
    }
</style>

<div class="w-100 bg-checkout-blue py-5 mb-5 text-center text-white">
    <div class="container px-4">
        <div class="inner-checkout-card mx-auto" style="max-width: 960px;">
            <h1 class="fw-bold mb-0 display-6" style="letter-spacing: -0.5px;">Checkout</h1>
        </div>
    </div>
</div>

<div class="container pb-5">
    <?php if (count($keranjang) > 0) { ?>
        <form method="POST" id="cartForm">
            <div class="row g-4">
                
                <div class="col-lg-7">
                    <div class="cart-table-card">
                        
                        <div class="row g-0 pb-3 border-bottom d-none d-sm-flex">
                            <div class="col-sm-6 table-header-text">Produk</div>
                            <div class="col-sm-2 table-header-text text-center">Harga</div>
                            <div class="col-sm-2 table-header-text text-center">Jumlah</div>
                            <div class="col-sm-2 table-header-text text-end">Total</div>
                        </div>

                        <?php
                        $grandtotal = 0;
                        $first_product = null; // Penampung produk pertama untuk preview ringkasan kanan
                        $counter = 0;

                        foreach ($keranjang as $id => $qty) {
                            $id = intval($id);
                            $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "
                                SELECT p.*, k.namakategori 
                                FROM produk p 
                                LEFT JOIN kategori k ON p.kategori_id = k.id 
                                WHERE p.id='$id'
                            "));

                            if (!$produk) continue;

                            if ($counter === 0) {
                                $first_product = $produk;
                            }
                            $counter++;

                            $subtotal = $produk['harga'] * $qty;
                            $grandtotal += $subtotal;
                        ?>
                            <div class="row g-0 align-items-center cart-item-row">
                                <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if ($produk['foto']) { ?>
                                            <img src="assets/uploads/produk/<?= $produk['foto'] ?>" class="book-cover-thumb">
                                        <?php } else { ?>
                                            <img src="https://via.placeholder.com/150x200?text=No+Cover" class="book-cover-thumb">
                                        <?php } ?>
                                        <div>
                                            <h6 class="cart-book-title mb-0"><?= $p['namaproduk'] ?? $produk['namaproduk'] ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 col-sm-2 text-sm-center">
                                    <span class="text-secondary small fw-medium">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span>
                                </div>
                                <div class="col-5 col-sm-2 text-sm-center">
                                    <div class="d-inline-flex align-items-center rounded overflow-hidden">
                                        <button type="button" class="btn-qty-adjust rounded-start" onclick="adjustQty(<?= $produk['id'] ?>, -1)">-</button>
                                        <input type="number" name="qty[<?= $produk['id'] ?>]" id="qty_<?= $produk['id'] ?>" class="qty-display-input text-center qty-input" value="<?= $qty ?>" min="1" readonly>
                                        <button type="button" class="btn-qty-adjust rounded-end" onclick="adjustQty(<?= $produk['id'] ?>, 1)">+</button>
                                    </div>
                                </div>
                                <div class="col-3 col-sm-2 text-end position-relative">
                                    <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.95rem;">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn border-0 p-0 text-muted me-1 small" style="font-size:0.75rem;"><i class="bi bi-heart"></i></button>
                                        <button type="button" class="btn border-0 p-0 text-muted me-1 small" style="font-size:0.75rem;"><i class="bi bi-pencil"></i></button>
                                        <a href="keranjang.php?hapus=<?= $produk['id'] ?>" class="text-muted text-decoration-none small" onclick="return confirm('Hapus produk ini?')" style="font-size:0.75rem;"><i class="bi bi-x-lg text-danger"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                            <a href="produk.php" class="btn btn-utility-grey">Lanjutkan Belanja</a>
                            <div class="d-flex gap-2">
                                <button type="submit" name="update" id="btnUpdate" class="btn btn-warning btn-utility-grey text-dark fw-bold d-none">Simpan Perubahan</button>
                                <button type="button" onclick="window.location.href='keranjang.php?hapus=all_clear'" class="btn btn-utility-grey" style="background-color:#fff0f0; color:#e53e3e;">Hapus Daftar Keranjang</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="summary-card">
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">Ringkasan Pemesanan</h5>
                        <p class="text-muted small mb-4">Sebelum Anda menikmati layanan kami, pastikan semua detail di bawah ini sudah benar dan selesai!</p>
                        
                        <?php if ($first_product) { ?>
                            <div class="d-flex align-items-center gap-3 p-3 border rounded-3 mb-4 bg-light bg-opacity-50">
                                <?php if ($first_product['foto']) { ?>
                                    <img src="assets/uploads/produk/<?= $first_product['foto'] ?>" class="rounded" style="width: 55px; height: 75px; object-fit: cover;">
                                <?php } else { ?>
                                    <img src="https://via.placeholder.com/55x75?text=Cover" class="rounded">
                                <?php } ?>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem; line-height: 1.3;"><?= $p['namaproduk'] ?? $first_product['namaproduk'] ?></h6>
                                    <div class="text-orange small" style="font-size: 0.75rem;">
                                        <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star text-muted"></i>
                                        <span class="text-muted ms-1">104+ Review</span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="d-flex justify-content-between align-items-center mb-2 small text-secondary">
                            <span>Subtotal</span>
                            <span class="fw-semibold text-dark">Rp <?= number_format($grandtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4 small text-secondary border-bottom pb-3">
                            <span>Pajak</span>
                            <span class="fw-semibold text-dark">Rp 0</span>
                        </div>

                        <div class="input-group mb-4 bg-light rounded-3 p-1 border">
                            <input type="text" class="form-control bg-transparent border-0 small" placeholder="Terapkan kode promo" style="font-size: 0.85rem;">
                            <button class="btn btn-promo-apply px-3 py-1" type="button">Gunakan</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">Total Harga Booking</span>
                            <h4 class="fw-black text-dark" style="font-weight: 800; font-size: 1.4rem;">Rp <?= number_format($grandtotal, 0, ',', '.') ?></h4>
                        </div>
                        <p class="text-muted" style="font-size: 0.75rem; margin-bottom: 2rem;">Harga keseluruhan dan termasuk diskon</p>

                        <a href="checkout.php" class="btn btn-checkout-primary w-100 text-center py-2 shadow-sm">
                            Checkout
                        </a>

                    </div>
                </div>

            </div>
        </form>
    <?php } else { ?>
        <div class="card border-0 shadow-sm rounded-4 max-width mx-auto" style="max-width: 600px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x display-3 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Keranjang Belanja Anda Kosong</h5>
                <p class="text-muted small mb-4">Anda belum memasukkan buku apa pun ke daftar belanja.</p>
                <a href="produk.php" class="btn btn-checkout-primary px-4 rounded-pill">Belanja Sekarang</a>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    function adjustQty(productId, amount) {
        const qtyInput = document.getElementById('qty_' + productId);
        const btnUpdate = document.getElementById('btnUpdate');
        let currentQty = parseInt(qtyInput.value);
        
        let newQty = currentQty + amount;
        if (newQty >= 1) {
            qtyInput.value = newQty;
            // Tampilkan tombol "Simpan Perubahan" otomatis ketika kuantitas bergeser
            btnUpdate.classList.remove('d-none');
        }
    }
</script>

<?php include 'footer.php'; ?>