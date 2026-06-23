<?php include 'header.php'; ?>

<?php
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// LOGIKA HAPUS ITEM ATAU ALL CLEAR VIA GET REFRESH
if (isset($_GET['hapus'])) {
    $action_hapus = $_GET['hapus'];

    if ($action_hapus === 'all_clear') {
        $_SESSION['keranjang'] = [];
        echo "<script>alert('Seluruh produk di keranjang berhasil dikosongkan'); location='keranjang.php';</script>";
        exit;
    } else {
        $id_hapus = intval($action_hapus);
        unset($_SESSION['keranjang'][$id_hapus]);
        echo "<script>alert('Produk dihapus dari keranjang'); location='keranjang.php';</script>";
        exit;
    }
}

$keranjang = $_SESSION['keranjang'];
$sudah_login = isset($_SESSION['user']);
?>

<style>
    body { background-color: #f8fafc; }
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
    .cart-table-card {
        background: #ffffff;
        border-radius: 12px;
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
        transition: all 0.3s ease;
    }
    .cart-item-row:last-child { border-bottom: none; }
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
    .btn-utility-grey:hover { background-color: #e2e8f0; }
    .summary-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        padding: 2rem;
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
    .btn-checkout-primary:hover { background-color: #4c51bf; }
    .btn-need-login-cart {
        background-color: #e53e3e;
        color: #ffffff;
        font-weight: 700;
        padding: 0.75rem;
        border-radius: 8px;
        border: none;
        transition: background-color 0.2s;
    }
    
    /* Style Kustom tombol X silang merah untuk hapus item */
    .btn-remove-item {
        background: #fff5f5;
        color: #e53e3e;
        border: 1px solid #fed7d7;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .btn-remove-item:hover {
        background: #e53e3e;
        color: #ffffff;
        border-color: #e53e3e;
        transform: scale(1.08);
    }
</style>

<div class="w-100 bg-checkout-blue py-5 mb-5 text-center text-white">
    <div class="container px-4">
        <div class="inner-checkout-card mx-auto" style="max-width: 960px;">
            <h1 class="fw-bold mb-0 display-6" style="letter-spacing: -0.5px;">Keranjang Belanja</h1>
        </div>
    </div>
</div>

<div class="container pb-5">
    <?php if (count($keranjang) > 0) { ?>
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
                    foreach ($keranjang as $id => $qty) {
                        $id = intval($id);
                        $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "
                            SELECT p.*, k.namakategori 
                            FROM produk p 
                            LEFT JOIN kategori k ON p.kategori_id = k.id 
                            WHERE p.id='$id'
                        "));

                        if (!$produk) continue;

                        $subtotal = $produk['harga'] * $qty;
                        $grandtotal += $subtotal;
                    ?>
                        <div class="row g-0 align-items-center cart-item-row" id="row_<?= $produk['id'] ?>">
                            <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                                <div class="d-flex align-items-center gap-3">
                                    <?php if ($produk['foto']) { ?>
                                        <img src="assets/uploads/produk/<?= $produk['foto'] ?>" class="book-cover-thumb">
                                    <?php } else { ?>
                                        <img src="https://via.placeholder.com/150x200?text=No+Cover" class="book-cover-thumb">
                                    <?php } ?>
                                    <div>
                                        <h6 class="cart-book-title mb-0"><?= htmlspecialchars($produk['namaproduk']) ?></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-2 text-sm-center">
                                <span class="text-secondary small fw-medium">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span>
                            </div>
                            
                            <div class="col-5 col-sm-2 text-sm-center">
                                <div class="d-inline-flex align-items-center rounded overflow-hidden">
                                    <button type="button" class="btn-qty-adjust rounded-start" onclick="ubahJumlahBeli(<?= $produk['id'] ?>, -1)">-</button>
                                    <input type="number" id="qty_<?= $produk['id'] ?>" data-stok="<?= intval($produk['stok']) ?>" class="qty-display-input text-center" value="<?= $qty ?>" readonly>
                                    <button type="button" class="btn-qty-adjust rounded-end" onclick="ubahJumlahBeli(<?= $produk['id'] ?>, 1)">+</button>
                                </div>
                            </div>
                            
                            <div class="col-3 col-sm-2 text-end position-relative">
                                <span class="fw-bold text-dark d-block mb-1" id="subtotal_<?= $produk['id'] ?>" style="font-size: 0.95rem;">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn-remove-item" onclick="hapusItemKeranjang(<?= $produk['id'] ?>)" title="Hapus Buku"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                        <a href="produk.php" class="btn btn-utility-grey text-decoration-none">Lanjutkan Belanja</a>
                        <button type="button" class="btn btn-utility-grey" onclick="if(confirm('Apakah Anda yakin ingin mengosongkan seluruh isi keranjang belanja?')) { window.location.href='keranjang.php?hapus=all_clear'; }" style="background-color:#fff0f0; color:#e53e3e; border: none; border-radius: 4px; padding: 0.5rem 1.2rem; font-size: 0.8rem; font-weight: 600;">Kosongkan Keranjang</button>
                    </div>

                </div>
            </div>

            <div class="col-lg-5">
                <div class="summary-card">
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">Ringkasan Pemesanan</h5>
                    <p class="text-muted small mb-4">Pastikan rincian pesanan buku Anda sudah sesuai sebelum masuk halaman checkout.</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2 small text-secondary">
                        <span>Subtotal Belanja</span>
                        <span class="fw-semibold text-dark text-grandtotal">Rp <?= number_format($grandtotal, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4 small text-secondary border-bottom pb-3">
                        <span>Biaya Administrasi</span>
                        <span class="fw-semibold text-dark">Rp 0</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">Total Harga</span>
                        <h4 class="fw-black text-primary mb-0 text-grandtotal" style="font-weight: 800; font-size: 1.4rem;">Rp <?= number_format($grandtotal, 0, ',', '.') ?></h4>
                    </div>
                    <p class="text-muted mb-4" style="font-size: 0.75rem;">Harga keseluruhan sudah termasuk PPN</p>

                    <?php if ($sudah_login) { ?>
                        <a href="checkout.php" class="btn btn-checkout-primary w-100 text-center py-2 text-decoration-none">
                            Lanjutkan ke Checkout <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    <?php } else { ?>
                        <button type="button" onclick="peringatanLogin()" class="btn btn-need-login-cart w-100 text-center py-2 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login untuk Checkout
                        </button>
                    <?php } ?>

                </div>
            </div>

        </div>
    <?php } else { ?>
        <div class="card border-0 shadow-sm rounded-4 max-width mx-auto" style="max-width: 600px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x display-3 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Keranjang Belanja Anda Kosong</h5>
                <p class="text-muted small mb-4">Anda belum memasukkan buku apa pun ke daftar belanja.</p>
                <a href="produk.php" class="btn btn-checkout-primary px-4 rounded-pill text-decoration-none">Mulai Belanja Buku!</a>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    function ubahJumlahBeli(productId, amount) {
        const qtyInput = document.getElementById('qty_' + productId);
        if (!qtyInput) return;

        const maxStok = parseInt(qtyInput.getAttribute('data-stok')) || 0;
        let currentQty = parseInt(qtyInput.value);
        let newQty = currentQty + amount;

        if (newQty < 1 && amount < 0) {
            hapusItemKeranjang(productId);
            return;
        }

        if (newQty > maxStok && amount > 0) {
            alert('Maaf, jumlah pembelian sudah mencapai batas maksimal stok yang tersedia (' + maxStok + ' buku).');
            return;
        }

        fetch('api/update_keranjang.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId, qty: newQty, action: 'update' })
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                qtyInput.value = result.qty;
                document.getElementById('subtotal_' + productId).innerText = result.subtotal;
                document.querySelectorAll('.text-grandtotal').forEach(el => { el.innerText = result.grandtotal; });
            } else if (result.status === 'deleted') {
                window.location.reload();
            } else {
                alert(result.message);
            }
        })
        .catch(err => {
            console.error("Gagal memperbarui kuantitas:", err);
        });
    }

    function hapusItemKeranjang(productId) {
        if (!confirm("Apakah Anda yakin ingin menghapus produk buku ini dari daftar keranjang?")) return;

        fetch('api/update_keranjang.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId, action: 'delete' })
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === 'deleted') {
                const barisProduk = document.getElementById('row_' + productId);
                if (barisProduk) {
                    barisProduk.style.opacity = '0';
                    setTimeout(() => {
                        barisProduk.remove();
                        if (result.sisa_item === 0) {
                            window.location.reload();
                        }
                    }, 300);
                }
                
                document.querySelectorAll('.text-grandtotal').forEach(el => {
                    el.innerText = result.grandtotal;
                });
            } else {
                alert(result.message);
            }
        })
        .catch(err => {
            console.error("Gagal menghapus produk:", err);
            alert("Terjadi gangguan jaringan, produk gagal dihapus.");
        });
    }

    function peringatanLogin() {
        alert('Silakan login terlebih dahulu untuk melanjutkan aktivitas belanja di Book Market.');
        window.location.href = "login.php";
    }
</script>

<?php include 'footer.php'; ?>