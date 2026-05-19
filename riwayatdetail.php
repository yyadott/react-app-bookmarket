<?php
include 'header.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); location='login.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$user_id = intval($_SESSION['user']['id']);

// 1. Ambil Data Transaksi Utama
$transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id='$id' AND customer_id='$user_id'"));
if (!$transaksi) {
    echo "<script>alert('Pesanan tidak ditemukan'); location='riwayat.php';</script>";
    exit;
}

// 2. Ambil Query Detail Produk (Termasuk Kolom Jumlah & Subtotal)
$detail = mysqli_query($koneksi, "
    SELECT td.jumlah, td.subtotal AS sub_item, p.namaproduk, p.deskripsi, p.foto, k.namakategori 
    FROM transaksidetail td
    LEFT JOIN produk p ON td.produk_id = p.id
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE td.transaksi_id='$id'
");

// 3. Ambil Data Pembayaran
$pembayaran = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE transaksi_id='$id'"));

// Penentuan Badge Status (Menggunakan Array Match agar Lebih Ringkas dari If-Else)
$status_badges = ['Belum Bayar' => 'warning', 'Diterima' => 'primary', 'Selesai' => 'success', 'Ditolak' => 'danger'];
$badge = $status_badges[$transaksi['status']] ?? 'secondary';
?>

<div class="container py-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Detail Pesanan</h4>
        <span class="badge bg-<?= $badge ?> px-3 py-2"><?= $transaksi['status'] ?></span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Informasi Pesanan</h5>
            <div class="row g-3">
                <div class="col-md-6"><small class="text-muted">ID Transaksi</small>
                    <h6 class="fw-bold">#TRX<?= $transaksi['id'] ?></h6>
                </div>
                <div class="col-md-6"><small class="text-muted">Tanggal</small>
                    <h6 class="fw-bold"><?= date('d M Y', strtotime($transaksi['tanggal'])) ?></h6>
                </div>
                <div class="col-md-6"><small class="text-muted">Nama</small>
                    <h6 class="fw-bold"><?= $transaksi['nama'] ?></h6>
                </div>
                <div class="col-md-6"><small class="text-muted">No HP</small>
                    <h6 class="fw-bold"><?= $transaksi['nohp'] ?></h6>
                </div>
                <div class="col-md-6"><small class="text-muted">Email</small>
                    <h6 class="fw-bold"><?= $transaksi['email'] ?></h6>
                </div>
                <div class="col-md-6"><small class="text-muted">Metode Pembayaran</small>
                    <h6 class="fw-bold"><?= $transaksi['metodebayar'] ?></h6>
                </div>
                <div class="col-12"><small class="text-muted">Alamat</small>
                    <h6 class="fw-bold"><?= "{$transaksi['alamat']}, {$transaksi['kota']}, {$transaksi['kodepos']}" ?></h6>
                </div>
                <?php if (!empty($transaksi['deskripsi'])): ?>
                    <div class="col-12"><small class="text-muted">Catatan</small>
                        <h6 class="fw-bold"><?= nl2br($transaksi['deskripsi']) ?></h6>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Produk Pesanan</h5>
            <div class="row g-3">
                <?php while ($d = mysqli_fetch_assoc($detail)): ?>
                    <div class="col-12">
                        <div class="border rounded-4 p-3">
                            <div class="row align-items-center">
                                <div class="col-4 col-md-2">
                                    <img src="<?= $d['foto'] ? 'assets/uploads/produk/' . $d['foto'] : 'https://via.placeholder.com/300x300' ?>" class="img-fluid rounded-4" style="height:90px; width:100%; object-fit:cover;">
                                </div>
                                <div class="col-8 col-md-7">
                                    <span class="badge bg-light text-dark mb-2"><?= $d['namakategori'] ?></span>
                                    <h6 class="fw-bold mb-1"><?= $d['namaproduk'] ?></h6>
                                    <p class="small text-muted mb-2"><?= substr($d['deskripsi'], 0, 80) ?>...</p>
                                    <span class="badge bg-secondary text-white fw-normal">Jumlah: <strong><?= $d['jumlah'] ?> pcs</strong></span>
                                </div>
                                <div class="col-12 col-md-3 text-md-end mt-2 mt-md-0">
                                    <small class="text-muted d-block d-md-none">Subtotal Item:</small>
                                    <h6 class="fw-bold text-primary mb-0">Rp <?= number_format($d['sub_item']) ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <hr class="my-4">
            <div class="d-flex justify-content-between mb-2"><span>Subtotal Produk</span><span>Rp <?= number_format($transaksi['grandtotal'] - $transaksi['ongkir']) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span>Ongkos Kirim (Ongkir)</span><span>Rp <?= number_format($transaksi['ongkir']) ?></span></div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Grand Total</h5>
                <h4 class="fw-bold text-primary mb-0">Rp <?= number_format($transaksi['grandtotal']) ?></h4>
            </div>
        </div>
    </div>

    <?php if ($pembayaran): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Data Pembayaran</h5>
                <div class="row g-3">
                    <div class="col-md-6"><small class="text-muted">Atas Nama</small>
                        <h6 class="fw-bold"><?= $pembayaran['atasnama'] ?></h6>
                    </div>
                    <div class="col-md-6"><small class="text-muted">Bank</small>
                        <h6 class="fw-bold"><?= $pembayaran['bank'] ?></h6>
                    </div>
                    <div class="col-md-6"><small class="text-muted">Jumlah</small>
                        <h6 class="fw-bold text-primary">Rp <?= number_format($pembayaran['jumlah']) ?></h6>
                    </div>
                    <div class="col-md-6"><small class="text-muted">Tanggal Bayar</small>
                        <h6 class="fw-bold"><?= date('d M Y H:i', strtotime($pembayaran['tanggal'])) ?></h6>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block mb-2">Bukti Pembayaran</small>
                        <?php if ($pembayaran['buktibayar']): ?>
                            <img src="assets/uploads/bukti/<?= $pembayaran['buktibayar'] ?>" class="img-fluid rounded-4 border" style="max-width:300px;">
                        <?php else: ?>
                            <div class="alert alert-warning m-0">Tidak ada bukti pembayaran</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex gap-2 flex-wrap">
        <a href="riwayat.php" class="btn btn-outline-primary rounded-pill px-4">← Kembali</a>
        <a href="cetaknota.php?id=<?= $transaksi['id'] ?>" target="_blank" class="btn btn-primary rounded-pill px-4"><i class="bi bi-printer me-2"></i>Cetak Nota</a>
    </div>
</div>

<?php include 'footer.php'; ?>