<?php
$id = intval($_GET['id']);

// Ambil data transaksi dasar
$transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id=$id"));
if (!$transaksi) {
    echo "<script>alert('Data tidak ditemukan'); location='index.php?page=transaksi';</script>";
    exit;
}

$metodeBayar    = $transaksi['metodebayar'];
$statusSekarang = $transaksi['status'];

// ── AREA PERBAIKAN LOGIKA STATUS INTERAKTIF ─────────────────────────────────
if ($metodeBayar == 'Tunai') {
    $alurStatus  = [
        'Menunggu Konfirmasi' => ['Diproses', 'Dibatalkan'],
        'Diproses'            => ['Dikirim'],
        'Dikirim'             => ['Diterima'], 
        'Diterima'            => ['Selesai'],
        'Selesai'             => [],
        'Dibatalkan'          => [],
    ];
    $timelineAll = ['Menunggu Konfirmasi', 'Diproses', 'Dikirim', 'Diterima', 'Selesai'];
} else {
    // Jalur Transfer Bank (Sinkron dengan 6 Langkah di Sisi User)
    $alurStatus  = [
        'Menunggu Konfirmasi' => ['Sudah Bayar', 'Ditolak'], 
        'Sudah Bayar'         => ['Diproses'],              
        'Diproses'            => ['Dikirim'],               
        'Dikirim'             => ['Diterima'],               
        'Diterima'            => ['Selesai'],                
        'Selesai'             => [],
        'Ditolak'             => [],
        'Dibatalkan'          => [],
    ];
    $timelineAll = ['Menunggu Konfirmasi', 'Sudah Bayar', 'Diproses', 'Dikirim', 'Diterima', 'Selesai'];
}

$statusBerikutnya = $alurStatus[$statusSekarang] ?? [];
$statusFinal      = in_array($statusSekarang, ['Selesai', 'Ditolak', 'Dibatalkan']);

// ── PROSES EKSEKUSI UPDATE STATUS & MANAJEMEN STOK ──────────────────────────
if (isset($_POST['update_status'])) {
    $statusBaru = $_POST['status'];
    
    if (!in_array($statusBaru, $statusBerikutnya)) {
        echo "<script>alert('Perubahan status tidak valid!'); location='index.php?page=transaksidetail&id=$id';</script>"; 
        exit;
    }
    
    // 1. Logika Potong Stok Gudang (Saat masuk antrean "Diproses")
    if ($statusBaru === 'Diproses') {
        $q = mysqli_query($koneksi, "
            SELECT td.produk_id, td.jumlah, p.namaproduk 
            FROM transaksidetail td 
            JOIN produk p ON td.produk_id = p.id 
            WHERE td.transaksi_id = $id
        ");
        
        while ($item = mysqli_fetch_assoc($q)) {
            // Jalankan kueri pemotongan stok bersyarat
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - {$item['jumlah']} WHERE id={$item['produk_id']} AND stok >= {$item['jumlah']}");
            
            // Validasi: Jika baris database tidak terpengaruh, berarti stok kosong/kurang
            if (mysqli_affected_rows($koneksi) === 0) {
                echo "<script>alert('Gagal! Stok untuk produk [{$item['namaproduk']}] tidak mencukupi.'); history.back();</script>";
                exit;
            }
        }
    }
    
    // 2. Logika Pengembalian Stok Gudang (Jika pesanan yang sudah diproses dibatalkan/ditolak)
    if (($statusBaru === 'Dibatalkan' || $statusBaru === 'Ditolak') && $statusSekarang === 'Diproses') {
        $q = mysqli_query($koneksi, "SELECT produk_id, jumlah FROM transaksidetail WHERE transaksi_id = $id");
        while ($item = mysqli_fetch_assoc($q)) {
            mysqli_query($koneksi, "UPDATE produk SET stok = stok + {$item['jumlah']} WHERE id={$item['produk_id']}");
        }
    }
    
    // 3. Simpan perubahan ke database
    $statusEsc = mysqli_real_escape_string($koneksi, $statusBaru);
    mysqli_query($koneksi, "UPDATE transaksi SET status='$statusEsc' WHERE id=$id");
    
    echo "<script>alert('Status transaksi berhasil diperbarui menjadi: $statusBaru'); location='index.php?page=transaksidetail&id=$id';</script>"; 
    exit;
}

// Data relasional untuk tabel detail dan pembayaran
$detail = mysqli_query($koneksi, "
    SELECT td.*, p.namaproduk, p.deskripsi, p.harga, p.foto, k.namakategori
    FROM transaksidetail td
    JOIN produk p ON td.produk_id = p.id
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE td.transaksi_id=$id
");
$bayar = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE transaksi_id=$id"));

// Ambil array warna untuk status box utama
$statusStyle = [
    'Menunggu Konfirmasi' => ['bg' => '#FFC107', 'text' => '#000000'],
    'Sudah Bayar'         => ['bg' => '#0D6EFD', 'text' => '#FFFFFF'],
    'Diproses'            => ['bg' => '#0DCAF0', 'text' => '#000000'],
    'Dikirim'             => ['bg' => '#6F42C1', 'text' => '#FFFFFF'],
    'Diterima'            => ['bg' => '#20C997', 'text' => '#FFFFFF'],
    'Selesai'             => ['bg' => '#198754', 'text' => '#FFFFFF'],
    'Ditolak'             => ['bg' => '#DC3545', 'text' => '#FFFFFF'],
    'Dibatalkan'          => ['bg' => '#6C757D', 'text' => '#FFFFFF'],
];
$st = $statusStyle[$statusSekarang] ?? ['bg' => '#6C757D', 'text' => '#FFFFFF'];

$timelineColor = [
    'Menunggu Konfirmasi' => '#FFC107',
    'Sudah Bayar'         => '#0D6EFD',
    'Diproses'            => '#0DCAF0',
    'Dikirim'             => '#6F42C1',
    'Diterima'            => '#20C997',
    'Selesai'             => '#198754',
];

$labelStatus = [
    'Sudah Bayar' => 'Konfirmasi Pembayaran Diterima',
    'Diproses'    => 'Proses & Kemas Pesanan',
    'Dikirim'     => 'Kirim Pesanan (Input Resi)',
    'Diterima'    => 'Konfirmasi Telah Diterima',
    'Selesai'     => 'Selesaikan Transaksi',
    'Dibatalkan'  => 'Batalkan Transaksi',
    'Ditolak'     => 'Tolak Transaksi',
];
?>

<style>
.badge-status { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; white-space: nowrap; }
.badge-metode { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; }
.timeline-circle { width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold; border: 2px solid transparent; }
.timeline-line { flex-grow: 1; height: 3px; margin-bottom: 22px; min-width: 16px; }
</style>

<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Detail Transaksi #<?= $id ?></h4>
                <span class="badge-status" style="background:<?= $st['bg'] ?>; color:<?= $st['text'] ?>; font-size:14px; padding:7px 16px">
                    <?= $statusSekarang ?>
                </span>
            </div>

            <?php if (!$statusFinal): ?>
            <div class="mb-4 p-3 bg-light rounded-3">
                <div class="d-flex align-items-center mb-2 gap-2">
                    <span style="font-size:13px; color:#555">Metode Pembayaran:</span>
                    <?php if ($metodeBayar == 'Tunai'): ?>
                        <span class="badge-metode" style="background:#D1FAE5; color:#065F46; font-size:12px; padding:3px 10px">COD / Tunai</span>
                    <?php else: ?>
                        <span class="badge-metode" style="background:#DBEAFE; color:#1E3A8A; font-size:12px; padding:3px 10px">Transfer Bank</span>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center" style="gap:0; overflow-x:auto; padding-bottom:4px">
                    <?php foreach ($timelineAll as $i => $s):
                        $idxSekarang = array_search($statusSekarang, $timelineAll);
                        $idxS        = array_search($s, $timelineAll);
                        $isDone      = $idxS <= $idxSekarang;
                        $isActive    = $s === $statusSekarang;
                        $color       = $timelineColor[$s] ?? '#dee2e6';
                        $textColor   = in_array($s, ['Menunggu Konfirmasi', 'Diproses']) ? '#000' : '#fff';

                        if ($isActive) {
                            $circleStyle = "background:$color; color:$textColor; border-color:$color; box-shadow: 0 0 0 3px {$color}55";
                        } elseif ($isDone) {
                            $circleStyle = "background:#198754; color:#fff; border-color:#198754";
                        } else {
                            $circleStyle = "background:#fff; color:#999; border-color:#dee2e6";
                        }
                        $lineColor = ($idxS < $idxSekarang) ? '#198754' : '#dee2e6';
                    ?>
                        <div class="text-center flex-shrink-0" style="min-width:70px">
                            <div class="timeline-circle mx-auto" style="<?= $circleStyle ?>">
                                <?= ($isDone && !$isActive) ? '✓' : ($i + 1) ?>
                            </div>
                            <div style="font-size:10px; margin-top:4px; font-weight:<?= $isActive ? '700' : '400' ?>; color:<?= $isActive ? $color : ($isDone ? '#198754' : '#999') ?>">
                                <?= $s ?>
                            </div>
                        </div>
                        <?php if ($i < count($timelineAll) - 1): ?>
                            <div class="timeline-line" style="background:<?= $lineColor ?>"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
                <?php
                $alertCfg = [
                    'Ditolak'    => ['bg' => '#FEE2E2', 'border' => '#DC3545', 'text' => '#7F1D1D'],
                    'Dibatalkan' => ['bg' => '#F3F4F6', 'border' => '#6C757D', 'text' => '#374151'],
                    'Selesai'    => ['bg' => '#D1FAE5', 'border' => '#198754', 'text' => '#065F46'],
                ];
                $ac = $alertCfg[$statusSekarang] ?? $alertCfg['Dibatalkan'];
                ?>
                <div class="mb-4 p-3 rounded-3 border-start border-4"
                    style="background:<?= $ac['bg'] ?>; border-color:<?= $ac['border'] ?> !important; color:<?= $ac['text'] ?>">
                   <b>Transaksi <?= $statusSekarang ?></b> — tidak dapat diproses lebih lanjut.
                </div>
            <?php endif; ?>

            <?php if ($metodeBayar !== 'Tunai' && $statusSekarang === 'Menunggu Konfirmasi' && !$bayar): ?>
                <div class="p-3 rounded-3 border-start border-4 mb-4"
                    style="background:#FFFBEB; border-color:#F59E0B !important; color:#78350F">
                    <b>Menunggu pembayaran customer.</b><br>
                    <small>Belum ada bukti transfer. Kamu dapat <b>menolak</b> transaksi ini jika customer tidak kunjung membayar.</small>
                </div>
            <?php endif; ?>

            <?php if ($metodeBayar !== 'Tunai' && $statusSekarang === 'Sudah Bayar'): ?>
                <div class="p-3 rounded-3 border-start border-4 mb-4"
                    style="background:#EFF6FF; border-color:#0D6EFD !important; color:#1E3A8A">
                    <b>Customer sudah melakukan transfer.</b><br>
                    <small>Verifikasi bukti transfer di bawah, lalu klik <b>Proses Pesanan</b>.</small><br>
                    <small style="color:#DC3545; font-weight:600">⚠ Pesanan ini <u>tidak bisa ditolak</u> — customer sudah mentransfer uang.</small>
                </div>
            <?php endif; ?>

            <table class="table table-bordered align-middle mb-4">
                <tr><th width="200" class="table-light">Nama Customer</th><td><?= htmlspecialchars($transaksi['nama']) ?></td></tr>
                <tr><th class="table-light">No HP</th><td><?= htmlspecialchars($transaksi['nohp']) ?></td></tr>
                <tr><th class="table-light">Email</th><td><?= htmlspecialchars($transaksi['email']) ?></td></tr>
                <tr>
                    <th class="table-light">Alamat</th>
                    <td>
                        <?= htmlspecialchars($transaksi['alamat']) ?>
                        <?= !empty($transaksi['kota'])    ? "<br><span class='text-muted'>Kota: </span>"     . htmlspecialchars($transaksi['kota'])    : "" ?>
                        <?= !empty($transaksi['kodepos']) ? "<br><span class='text-muted'>Kode Pos: </span>" . htmlspecialchars($transaksi['kodepos']) : "" ?>
                    </td>
                </tr>
                <tr><th class="table-light">Tanggal Order</th><td><?= date('d M Y H:i', strtotime($transaksi['tanggal'])) ?></td></tr>
                <tr>
                    <th class="table-light">Metode Bayar</th>
                    <td>
                        <?php if ($metodeBayar == 'Tunai'): ?>
                            <span class="badge-metode" style="background:#D1FAE5; color:#065F46">COD / Tunai</span>
                        <?php else: ?>
                            <span class="badge-metode" style="background:#DBEAFE; color:#1E3A8A">Transfer Bank</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr><th class="table-light">Catatan</th><td><?= $transaksi['deskripsi'] ? htmlspecialchars($transaksi['deskripsi']) : '<span class="text-muted">-</span>' ?></td></tr>
                <tr><th class="table-light">Subtotal Produk</th><td><b class="text-primary">Rp <?= number_format($transaksi['grandtotal'] - $transaksi['ongkir']) ?></b></td></tr>
                <tr><th class="table-light">Ongkos Kirim</th><td><b class="text-primary">Rp <?= number_format($transaksi['ongkir']) ?></b></td></tr>
                <tr>
                    <th class="table-light">Grand Total</th>
                    <td><b style="font-size:18px; color:#0D6EFD">Rp <?= number_format($transaksi['grandtotal']) ?></b></td>
                </tr>
                <tr>
                    <th class="table-light">Status</th>
                    <td>
                        <span class="badge-status" style="background:<?= $st['bg'] ?>; color:<?= $st['text'] ?>">
                            <?= $statusSekarang ?>
                        </span>
                    </td>
                </tr>
            </table>

            <?php if (!empty($statusBerikutnya)): ?>
            <div class="rounded-3 p-3 mb-5" style="background:#F8F9FA; border:1px solid #E9ECEF">
                <form method="POST" class="row g-2 align-items-end">
                    <div class="col-md-5 col-sm-8">
                        <label class="form-label fw-semibold">Update Status Transaksi</label>
                        <select name="status" class="form-select" required>
                            <?php foreach ($statusBerikutnya as $s): ?>
                                <option value="<?= $s ?>"><?= $labelStatus[$s] ?? $s ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Status saat ini:
                            <span class="badge-status" style="background:<?= $st['bg'] ?>; color:<?= $st['text'] ?>; font-size:11px; padding:2px 8px">
                                <?= $statusSekarang ?>
                            </span>
                        </small>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <?php
                        $opsiNegatif = array_intersect($statusBerikutnya, ['Ditolak', 'Dibatalkan']);
                        $opsiPositif = array_diff($statusBerikutnya, ['Ditolak', 'Dibatalkan']);
                        if (count($opsiNegatif) > 0 && count($opsiPositif) == 0) {
                            $btnStyle = 'background:#DC3545; color:#fff; border:none';
                        } elseif (count($opsiNegatif) > 0) {
                            $btnStyle = 'background:#FFC107; color:#000; border:none';
                        } else {
                            $btnStyle = 'background:#0D6EFD; color:#fff; border:none';
                        }
                        ?>
                        <button type="submit" name="update_status"
                            class="btn w-100 fw-semibold"
                            style="<?= $btnStyle ?>"
                            onclick="return confirm('Yakin ingin mengubah status transaksi ini?')">
                            <i class="fa fa-check me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
                <div class="p-3 rounded-3 mb-4" style="background:#F8F9FA; border:1px solid #dee2e6; color:#6C757D">
                    <i class="fa fa-lock me-2"></i>
                    Status <b><?= $statusSekarang ?></b> — tidak ada perubahan lebih lanjut.
                </div>
            <?php endif; ?>

            <h5 class="mb-3 fw-bold">Detail Produk</h5>
            <div class="table-responsive mb-5">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th width="90">Foto</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th width="60">Qty</th>
                            <th width="140">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($d = mysqli_fetch_assoc($detail)):
                            $qty      = intval($d['jumlah']) ?: 1;
                            $subtotal = $d['subtotal'] ?: ($d['harga'] * $qty);
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?php if ($d['foto']): ?>
                                        <img src="../assets/uploads/produk/<?= $d['foto'] ?>" width="75" class="rounded border">
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($d['namaproduk']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars(substr($d['deskripsi'], 0, 70)) ?>...</small>
                                </td>
                                <td><?= htmlspecialchars($d['namakategori'] ?? '-') ?></td>
                                <td>Rp <?= number_format($d['harga']) ?></td>
                                <td><?= $qty ?></td>
                                <td><b style="color:#0D6EFD">Rp <?= number_format($subtotal) ?></b></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <h5 class="mb-3 fw-bold">Data Pembayaran</h5>
            <?php if ($metodeBayar == 'Tunai'): ?>
                <div class="p-3 rounded-3" style="background:#D1FAE5; color:#065F46">
                    <b>COD / Tunai</b> — dibayar langsung ke kurir saat barang tiba.
                </div>
            <?php else: ?>
                <?php if ($bayar): ?>
                    <table class="table table-bordered align-middle mb-3">
                        <tr><th class="table-light" width="200">Atas Nama</th><td><?= htmlspecialchars($bayar['atasnama']) ?></td></tr>
                        <tr><th class="table-light">Bank</th><td><?= htmlspecialchars($bayar['bank']) ?></td></tr>
                        <tr><th class="table-light">Jumlah Transfer</th><td><b style="color:#0D6EFD">Rp <?= number_format($bayar['jumlah']) ?></b></td></tr>
                        <tr><th class="table-light">Tanggal Transfer</th><td><?= date('d M Y H:i', strtotime($bayar['tanggal'])) ?></td></tr>
                        <tr>
                            <th class="table-light">Bukti Transfer</th>
                            <td>
                                <?php if ($bayar['buktibayar']): ?>
                                    <a href="../assets/uploads/bukti/<?= $bayar['buktibayar'] ?>" target="_blank">
                                        <img src="../assets/uploads/bukti/<?= $bayar['buktibayar'] ?>" width="200" class="img-thumbnail">
                                    </a>
                                    <br><small class="text-muted">Klik gambar untuk perbesar</small>
                                <?php else: ?>
                                    <span class="text-muted small">Belum ada file bukti</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    <?php $selisih = $bayar['jumlah'] - $transaksi['grandtotal']; ?>
                    <?php if ($selisih < 0): ?>
                        <div class="p-3 rounded-3" style="background:#FEE2E2; color:#7F1D1D; border-left:4px solid #DC3545">
                            <b>Transfer kurang!</b> Selisih Rp <?= number_format(abs($selisih)) ?>. Hubungi customer sebelum memproses.
                        </div>
                    <?php elseif ($selisih > 0): ?>
                        <div class="p-3 rounded-3" style="background:#FFFBEB; color:#78350F; border-left:4px solid #F59E0B">
                            <b>Transfer lebih!</b> Kelebihan Rp <?= number_format($selisih) ?>. Kembalikan ke customer setelah selesai.
                        </div>
                    <?php else: ?>
                        <div class="p-3 rounded-3" style="background:#D1FAE5; color:#065F46; border-left:4px solid #198754">
                            <b>Jumlah transfer sesuai</b> dengan total tagihan.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="p-3 rounded-3" style="background:#FFFBEB; color:#78350F; border-left:4px solid #F59E0B">
                        Belum ada bukti pembayaran dari customer.
                        <?php if ($statusSekarang === 'Menunggu Konfirmasi'): ?>
                            <br><small>Kamu bisa menolak transaksi ini jika customer tidak kunjung membayar.</small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="d-flex gap-2 mt-4">
                <a href="index.php?page=transaksi" class="btn btn-secondary px-4">
                    <i class="fa fa-arrow-left me-2"></i>Kembali
                </a>
                <a href="cetaknota.php?id=<?= $id ?>" target="_blank" class="btn btn-success px-4">
                    <i class="fa fa-print me-2"></i>Cetak Nota
                </a>
            </div>

        </div>
    </div>
</div>