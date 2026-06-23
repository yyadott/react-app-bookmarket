<?php
include 'header.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); location='login.php';</script>";
    exit;
}

$id      = intval($_GET['id']);
$user_id = intval($_SESSION['user']['id']);

$transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id=$id AND customer_id=$user_id"));
if (!$transaksi) {
    echo "<script>alert('Pesanan tidak ditemukan'); location='riwayat.php';</script>";
    exit;
}

$status      = $transaksi['status'];
$metodeBayar = $transaksi['metodebayar'];

$pembayaran = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE transaksi_id=$id"));

/*
 * ATURAN PEMBATALAN OLEH CUSTOMER
 * COD      : bisa batalkan hanya di "Menunggu Konfirmasi"
 * Transfer : bisa batalkan hanya di "Menunggu Konfirmasi" + belum upload bukti
 */
$bisaBatalkan = false;
$alasanTidakBisa = '';

if ($metodeBayar == 'Tunai') {
    if ($status === 'Menunggu Konfirmasi') {
        $bisaBatalkan = true;
    } elseif ($status === 'Diproses') {
        $alasanTidakBisa = 'Pesanan sudah diproses & dikemas oleh toko. Hubungi toko untuk membatalkan.';
    }
} else {
    if ($status === 'Menunggu Konfirmasi' && !$pembayaran) {
        $bisaBatalkan = true;
    } elseif ($status === 'Menunggu Konfirmasi' && $pembayaran) {
        $alasanTidakBisa = 'Kamu sudah mengupload bukti bayar. Hubungi admin untuk pembatalan & refund.';
    } elseif ($status === 'Sudah Bayar' || $status === 'Diproses') {
        $alasanTidakBisa = 'Pembayaran sudah dikonfirmasi. Hubungi admin jika ingin membatalkan & mendapat refund.';
    }
}

// ── PROSES PEMBATALAN ──────────────────────────────────────────────────────
if (isset($_POST['batalkan']) && $bisaBatalkan) {
    mysqli_query($koneksi, "UPDATE transaksi SET status='Dibatalkan' WHERE id=$id AND customer_id=$user_id");
    echo "<script>alert('Pesanan berhasil dibatalkan.'); location='riwayatdetail.php?id=$id';</script>";
    exit;
}

// ── UPLOAD BUKTI TRANSFER + VALIDASI EKSTENSI ──────────────────────────────
if (isset($_POST['upload_bukti']) && $metodeBayar !== 'Tunai' && $status === 'Menunggu Konfirmasi' && !$pembayaran) {
    $atasnama = mysqli_real_escape_string($koneksi, $_POST['atasnama'] ?? '');
    $bank     = mysqli_real_escape_string($koneksi, $_POST['bank'] ?? '');
    $jumlah   = $transaksi['grandtotal'];
    $folder   = "assets/uploads/bukti/";
    $bukti    = "";

    if (!is_dir($folder)) mkdir($folder, 0777, true);

    if (!empty($_FILES['buktibayar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['buktibayar']['name'], PATHINFO_EXTENSION));
        
        // Validasi ekstensi ketat di sisi server (Keamanan backend)
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Format file salah! Hanya diperbolehkan format JPG, JPEG, atau PNG.'); history.back();</script>";
            exit;
        }

        $bukti = time() . "_" . $id . "." . $ext;
        move_uploaded_file($_FILES['buktibayar']['tmp_name'], $folder . $bukti);
    }

    if (empty($bukti)) {
        echo "<script>alert('Pilih file bukti transfer terlebih dahulu!'); history.back();</script>"; 
        exit;
    }

    mysqli_query($koneksi, "INSERT INTO pembayaran (transaksi_id, atasnama, bank, buktibayar, jumlah, tanggal)
        VALUES ('$id', '$atasnama', '$bank', '$bukti', '$jumlah', NOW())");
    mysqli_query($koneksi, "UPDATE transaksi SET status='Sudah Bayar' WHERE id=$id AND customer_id=$user_id");

    echo "<script>alert('Bukti transfer berhasil diupload! Menunggu verifikasi admin.'); location='riwayatdetail.php?id=$id';</script>";
    exit;
}

// ── KONFIRMASI DITERIMA OLEH CUSTOMER ──────────────────────────────────────
if (isset($_POST['konfirmasi_terima']) && $status === 'Dikirim') {
    mysqli_query($koneksi, "UPDATE transaksi SET status='Diterima' WHERE id=$id AND customer_id=$user_id");
    echo "<script>alert('Terima kasih! Pesanan dikonfirmasi diterima.'); location='riwayatdetail.php?id=$id';</script>";
    exit;
}

// Reload data transaksi setelah ada aksi POST
$transaksi  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id=$id AND customer_id=$user_id"));
$status     = $transaksi['status'];
$pembayaran = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE transaksi_id=$id"));

// Warna status box utama
$statusStyle = [
    'Menunggu Konfirmasi' => ['bg' => '#FFC107', 'text' => '#000'],
    'Sudah Bayar'         => ['bg' => '#0D6EFD', 'text' => '#fff'],
    'Diproses'            => ['bg' => '#0DCAF0', 'text' => '#000'],
    'Dikirim'             => ['bg' => '#6F42C1', 'text' => '#fff'],
    'Diterima'            => ['bg' => '#20C997', 'text' => '#fff'],
    'Selesai'             => ['bg' => '#198754', 'text' => '#fff'],
    'Ditolak'             => ['bg' => '#DC3545', 'text' => '#fff'],
    'Dibatalkan'          => ['bg' => '#6C757D', 'text' => '#fff'],
];
$st = $statusStyle[$status] ?? ['bg' => '#6C757D', 'text' => '#fff'];

$timelineColor = [
    'Menunggu Konfirmasi' => '#FFC107',
    'Sudah Bayar'         => '#0D6EFD',
    'Diproses'            => '#0DCAF0',
    'Dikirim'             => '#6F42C1',
    'Diterima'            => '#20C997',
    'Selesai'             => '#198754',
];

// SINKRONISASI TOTAL DAN STRUKTUR JALUR TIMELINE DENGAN ADMIN
if ($metodeBayar == 'Tunai') {
    $timelineAll = ['Menunggu Konfirmasi', 'Diproses', 'Dikirim', 'Diterima', 'Selesai'];
} else {
    $timelineAll = ['Menunggu Konfirmasi', 'Sudah Bayar', 'Diproses', 'Dikirim', 'Diterima', 'Selesai'];
}

$statusFinal = in_array($status, ['Selesai', 'Ditolak', 'Dibatalkan']);
?>

<style>
.badge-status  { display:inline-block; padding:5px 14px; border-radius:20px; font-size:13px; font-weight:700; white-space:nowrap; }
.badge-metode  { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.timeline-circle { width:34px; height:34px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; border:2px solid transparent; }
.timeline-line { flex-grow:1; height:3px; margin-bottom:22px; min-width:16px; }
.action-card   { border-radius:16px; padding:20px 24px; margin-bottom:20px; border-left:5px solid; }
</style>

<div class="container py-4 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Pesanan</h4>
            <small class="text-muted">#TRX<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?> · <?= date('d M Y H:i', strtotime($transaksi['tanggal'])) ?></small>
        </div>
        <span class="badge-status" style="background:<?= $st['bg'] ?>; color:<?= $st['text'] ?>; font-size:14px; padding:8px 18px">
            <?= $status ?>
        </span>
    </div>

    <?php if (!$statusFinal): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3 gap-2">
                <span class="text-muted small">Metode:</span>
                <?php if ($metodeBayar == 'Tunai'): ?>
                    <span class="badge-metode" style="background:#D1FAE5; color:#065F46">COD / Tunai</span>
                <?php else: ?>
                    <span class="badge-metode" style="background:#DBEAFE; color:#1E3A8A">Transfer Bank</span>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center" style="overflow-x:auto; padding-bottom:4px">
                <?php foreach ($timelineAll as $i => $s):
                    $idxNow   = array_search($status, $timelineAll);
                    $idxS     = array_search($s, $timelineAll);
                    $isDone   = $idxS <= $idxNow;
                    $isActive = $s === $status;
                    $color    = $timelineColor[$s] ?? '#dee2e6';
                    $tcolor   = in_array($s, ['Menunggu Konfirmasi', 'Diproses']) ? '#000' : '#fff';
                    
                    if ($isActive) {
                        $cs = "background:$color; color:$tcolor; border-color:$color; box-shadow:0 0 0 3px {$color}55";
                    } elseif ($isDone) {
                        $cs = "background:#198754; color:#fff; border-color:#198754"; // Hijau jika sudah terlewati
                    } else {
                        $cs = "background:#fff; color:#999; border-color:#dee2e6";
                    }
                    $lc = ($idxS < $idxNow) ? '#198754' : '#dee2e6';
                ?>
                    <div class="text-center flex-shrink-0" style="min-width:68px">
                        <div class="timeline-circle mx-auto" style="<?= $cs ?>">
                            <?= ($isDone && !$isActive) ? '✓' : ($i+1) ?>
                        </div>
                        <div style="font-size:10px; margin-top:4px; font-weight:<?= $isActive?'700':'400' ?>; color:<?= $isActive?$color:($isDone?'#198754':'#999') ?>">
                            <?= $s ?>
                        </div>
                    </div>
                    <?php if ($i < count($timelineAll)-1): ?>
                        <div class="timeline-line" style="background:<?= $lc ?>"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <?php
        $alertCfg = [
            'Selesai'    => ['bg'=>'#D1FAE5','border'=>'#198754','text'=>'#065F46'],
            'Ditolak'    => ['bg'=>'#FEE2E2','border'=>'#DC3545','text'=>'#7F1D1D'],
            'Dibatalkan' => ['bg'=>'#F3F4F6','border'=>'#6C757D','text'=>'#374151'],
        ];
        $ac = $alertCfg[$status] ?? $alertCfg['Dibatalkan'];
        ?>
        <div class="p-3 rounded-4 mb-4 border-start border-4"
            style="background:<?= $ac['bg'] ?>; border-color:<?= $ac['border'] ?> !important; color:<?= $ac['text'] ?>">
            <b>Pesanan <?= $status ?></b>
            <?php if ($status === 'Selesai'): ?>— Terima kasih sudah berbelanja! 
            <?php elseif ($status === 'Dibatalkan'): ?>— Pesanan ini telah dibatalkan.
            <?php elseif ($status === 'Ditolak'): ?>— Pesanan ditolak oleh toko. Hubungi admin untuk info lebih lanjut.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($status === 'Dikirim'): ?>
        <div class="action-card" style="background:#EFF6FF; border-color:#0D6EFD; color:#1E3A8A">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <b>Pesananmu sedang dalam perjalanan!</b><br>
                    <small>Setelah barang tiba, klik tombol <b>Konfirmasi Diterima</b>.</small>
                </div>
                <form method="POST">
                    <button type="submit" name="konfirmasi_terima" class="btn fw-bold px-4"
                        style="background:#0D6EFD; color:#fff; border:none"
                        onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima?')">
                        Konfirmasi Diterima
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($metodeBayar !== 'Tunai' && $status === 'Menunggu Konfirmasi' && !$pembayaran): ?>
        <div class="action-card" style="background:#FFFBEB; border-color:#F59E0B; color:#78350F">
            <b>Selesaikan Pembayaran Transfer</b><br>
            <small class="d-block mb-3">Transfer ke rekening toko di bawah, lalu upload bukti di sini.</small>

            <div class="p-3 rounded-3 mb-3" style="background:#FEF3C7; border:1px solid #F59E0B">
                <b>Rekening Toko:</b><br>
                BCA · <b>1234567890</b> · a.n. <b>Toko Online</b><br>
                Mandiri · <b>0987654321</b> · a.n. <b>Toko Online</b><br>
                <b>Total Transfer: <span style="color:#DC2626">Rp <?= number_format($transaksi['grandtotal']) ?></span></b>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Atas Nama Pengirim</label>
                        <input type="text" name="atasnama" class="form-control form-control-sm" required placeholder="Nama rekening kamu">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Bank Asal</label>
                        <select name="bank" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Bank --</option>
                            <option>BCA</option><option>Mandiri</option><option>BRI</option>
                            <option>BNI</option><option>CIMB</option><option>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Bukti Transfer (foto/screenshot)</label>
                        <input type="file" name="buktibayar" class="form-control form-control-sm" accept="image/*" required>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" name="upload_bukti" class="btn btn-sm fw-bold px-4"
                            style="background:#F59E0B; color:#000; border:none"
                            onclick="return confirm('Upload bukti transfer sekarang?')">
                            Upload Bukti Transfer
                        </button>
                        <small class="text-muted ms-2">Status akan otomatis berubah ke <b>Sudah Bayar</b> setelah upload.</small>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($bisaBatalkan): ?>
        <div class="action-card" style="background:#FEF2F2; border-color:#DC3545; color:#7F1D1D">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <b>Batalkan Pesanan</b><br>
                    <small>
                        <?php if ($metodeBayar == 'Tunai'): ?>
                            Pesanan bisa dibatalkan selama belum diproses oleh toko.
                        <?php else: ?>
                            Pesanan bisa dibatalkan selama kamu belum mengupload bukti transfer.
                        <?php endif; ?>
                    </small>
                </div>
                <form method="POST">
                    <button type="submit" name="batalkan" class="btn btn-sm fw-bold px-4"
                        style="background:#DC3545; color:#fff; border:none"
                        onclick="return confirm('Yakin ingin membatalkan pesanan ini? Tindakan ini tidak bisa dibatalkan.')">
                        Batalkan Pesanan
                    </button>
                </form>
            </div>
        </div>
    <?php elseif (!empty($alasanTidakBisa)): ?>
        <div class="action-card" style="background:#F3F4F6; border-color:#9CA3AF; color:#374151">
            <b>Ingin membatalkan?</b><br>
            <small><?= $alasanTidakBisa ?></small>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Informasi Pesanan</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted">ID Pesanan</small>
                    <div class="fw-bold">#TRX<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Tanggal Order</small>
                    <div class="fw-bold"><?= date('d M Y H:i', strtotime($transaksi['tanggal'])) ?></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Nama Penerima</small>
                    <div class="fw-bold"><?= htmlspecialchars($transaksi['nama']) ?></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">No HP</small>
                    <div class="fw-bold"><?= htmlspecialchars($transaksi['nohp']) ?></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Email</small>
                    <div class="fw-bold"><?= htmlspecialchars($transaksi['email']) ?></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Metode Pembayaran</small>
                    <div>
                        <?php if ($metodeBayar == 'Tunai'): ?>
                            <span class="badge-metode" style="background:#D1FAE5; color:#065F46">💵 COD / Tunai</span>
                        <?php else: ?>
                            <span class="badge-metode" style="background:#DBEAFE; color:#1E3A8A">🏦 Transfer Bank</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Kurir</small>
                    <div class="fw-bold"><?= htmlspecialchars(strtoupper($transaksi['kurir'] ?? '-')) ?></div>
                </div>
                <div class="col-12">
                    <small class="text-muted">Alamat Pengiriman</small>
                    <div class="fw-bold">
                        <?= htmlspecialchars($transaksi['alamat']) ?>
                        <?= !empty($transaksi['kota']) ? ', ' . htmlspecialchars($transaksi['kota']) : '' ?>
                        <?= !empty($transaksi['kodepos']) ? ' ' . htmlspecialchars($transaksi['kodepos']) : '' ?>
                    </div>
                </div>
                <?php if (!empty($transaksi['deskripsi'])): ?>
                <div class="col-12">
                    <small class="text-muted">Catatan</small>
                    <div><?= nl2br(htmlspecialchars($transaksi['deskripsi'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Produk Pesanan</h5>
            <?php
            $detail = mysqli_query($koneksi, "
                SELECT td.jumlah, td.subtotal AS sub_item, p.namaproduk, p.deskripsi, p.harga, p.foto, k.namakategori
                FROM transaksidetail td
                LEFT JOIN produk p ON td.produk_id = p.id
                LEFT JOIN kategori k ON p.kategori_id = k.id
                WHERE td.transaksi_id=$id
            ");
            while ($d = mysqli_fetch_assoc($detail)): ?>
            <div class="border rounded-4 p-3 mb-3">
                <div class="row align-items-center">
                    <div class="col-4 col-md-2">
                        <img src="<?= $d['foto'] ? 'assets/uploads/produk/' . $d['foto'] : 'https://via.placeholder.com/150' ?>"
                            class="img-fluid rounded-3" style="height:80px; width:100%; object-fit:cover">
                    </div>
                    <div class="col-8 col-md-7">
                        <span class="badge bg-light text-dark mb-1" style="font-size:11px"><?= htmlspecialchars($d['namakategori'] ?? '-') ?></span>
                        <div class="fw-bold"><?= htmlspecialchars($d['namaproduk']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars(substr($d['deskripsi'], 0, 80)) ?>...</small>
                        <div class="mt-1"><span class="badge bg-secondary fw-normal">Qty: <?= $d['jumlah'] ?> pcs</span></div>
                    </div>
                    <div class="col-12 col-md-3 text-md-end mt-2 mt-md-0">
                        <div class="fw-bold text-primary">Rp <?= number_format($d['sub_item']) ?></div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

            <hr>
            <div class="d-flex justify-content-between mb-2 text-muted">
                <span>Subtotal Produk</span>
                <span>Rp <?= number_format($transaksi['grandtotal'] - $transaksi['ongkir']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 text-muted">
                <span>Ongkos Kirim (<?= strtoupper($transaksi['kurir'] ?? '') ?>)</span>
                <span>Rp <?= number_format($transaksi['ongkir']) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Grand Total</h5>
                <h4 class="fw-bold mb-0" style="color:#0D6EFD">Rp <?= number_format($transaksi['grandtotal']) ?></h4>
            </div>
        </div>
    </div>

    <?php if ($metodeBayar == 'Tunai'): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Data Pembayaran</h5>
                <div class="p-3 rounded-3" style="background:#D1FAE5; color:#065F46">
                    <b>COD / Tunai</b> — Bayar langsung ke kurir saat barang tiba.
                    <br><small>Siapkan uang pas sebesar <b>Rp <?= number_format($transaksi['grandtotal']) ?></b></small>
                </div>
            </div>
        </div>
    <?php elseif ($pembayaran): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Data Pembayaran</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">Atas Nama</small>
                        <div class="fw-bold"><?= htmlspecialchars($pembayaran['atasnama']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Bank</small>
                        <div class="fw-bold"><?= htmlspecialchars($pembayaran['bank']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Jumlah Transfer</small>
                        <div class="fw-bold" style="color:#0D6EFD">Rp <?= number_format($pembayaran['jumlah']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Tanggal Transfer</small>
                        <div class="fw-bold"><?= date('d M Y H:i', strtotime($pembayaran['tanggal'])) ?></div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block mb-2">Bukti Transfer</small>
                        <?php if ($pembayaran['buktibayar']): ?>
                            <a href="assets/uploads/bukti/<?= $pembayaran['buktibayar'] ?>" target="_blank">
                                <img src="assets/uploads/bukti/<?= $pembayaran['buktibayar'] ?>"
                                    class="img-thumbnail rounded-3" style="max-width:280px">
                            </a>
                            <br><small class="text-muted">Klik gambar untuk perbesar</small>
                        <?php else: ?>
                            <span class="text-muted small">Tidak ada file bukti</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($status === 'Sudah Bayar'): ?>
                    <div class="mt-3 p-3 rounded-3" style="background:#FFFBEB; color:#78350F; border-left:4px solid #F59E0B">
                        <b>Menunggu verifikasi admin.</b> Admin akan memproses pesananmu setelah memverifikasi bukti transfer.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex gap-2 flex-wrap">
        <a href="riwayat.php" class="btn btn-outline-secondary rounded-pill px-4">← Kembali</a>
        <a href="cetaknota.php?id=<?= $id ?>" target="_blank" class="btn btn-success rounded-pill px-4">
            <i class="fa fa-print me-2"></i>Cetak Nota
        </a>
    </div>

</div>

<?php include 'footer.php'; ?>