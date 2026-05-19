<?php
$id = intval($_GET['id']);

$transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id='$id'"));
if (!$transaksi) {
    echo "<script>alert('Data tidak ditemukan'); location='index.php?page=transaksi';</script>";
    exit;
}

if (isset($_POST['update_status'])) {
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    if ($status === 'Diterima' && $transaksi['status'] !== 'Diterima') {
        $q_items = mysqli_query($koneksi, "SELECT produk_id, jumlah FROM transaksidetail WHERE transaksi_id='$id'");
        while ($item = mysqli_fetch_assoc($q_items)) {
            $p_id = $item['produk_id'];
            $qty = intval($item['jumlah']);

            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $qty WHERE id='$p_id'");
        }
    }

    mysqli_query($koneksi, "UPDATE transaksi SET status='$status' WHERE id='$id'");

    echo "<script>alert('Status berhasil diupdate'); location='index.php?page=transaksidetail&id=$id';</script>";
    exit;
}

$detail = mysqli_query($koneksi, "
    SELECT td.*, p.namaproduk, p.deskripsi, p.harga, p.foto, k.namakategori 
    FROM transaksidetail td 
    JOIN produk p ON td.produk_id = p.id 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    WHERE td.transaksi_id='$id'
");

$bayar = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE transaksi_id='$id'"));

$badges = ['Menunggu Konfirmasi' => 'warning', 'Belum Bayar' => 'danger', 'Sudah Bayar' => 'primary', 'Diterima' => 'success', 'Selesai' => 'info', 'Ditolak' => 'dark'];
$badge = $badges[$transaksi['status']] ?? 'secondary';
?>

<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <h4 class="mb-4 fw-bold">Detail Transaksi #<?= $id ?></h4>

            <table class="table table-bordered align-middle mb-4">
                <tr>
                    <th width="220">Nama Customer</th>
                    <td><?= $transaksi['nama'] ?></td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td><?= $transaksi['nohp'] ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= $transaksi['email'] ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>
                        <?= $transaksi['alamat'] ?>
                        <?= !empty($transaksi['kota']) ? "<br>Kota : {$transaksi['kota']}" : "" ?>
                        <?= !empty($transaksi['kodepos']) ? "<br>Kode Pos : {$transaksi['kodepos']}" : "" ?>
                    </td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td><?= date('d M Y H:i', strtotime($transaksi['tanggal'])) ?></td>
                </tr>
                <tr>
                    <th>Metode Bayar</th>
                    <td><span class="badge bg-dark p-2 text-white"><?= $transaksi['metodebayar'] ?></span></td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td><?= $transaksi['deskripsi'] ?: '-' ?></td>
                </tr>
                <tr>
                    <th>Subtotal</th>
                    <td><b class="text-primary">Rp <?= number_format($transaksi['grandtotal'] - $transaksi['ongkir']) ?></b></td>
                </tr>
                <tr>
                    <th>Ongkir</th>
                    <td><b class="text-primary">Rp <?= number_format($transaksi['ongkir']) ?></b></td>
                </tr>
                <tr>
                    <th>Grand Total</th>
                    <td><b class="text-primary fs-5">Rp <?= number_format($transaksi['grandtotal']) ?></b></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><span class="badge bg-<?= $badge ?> p-2 text-white"><?= $transaksi['status'] ?></span></td>
                </tr>
            </table>

            <form method="POST" class="row g-2 mb-5 align-items-end">
                <div class="col-md-4 col-sm-8">
                    <label class="form-label fw-semibold">Update Status Transaksi</label>
                    <select name="status" class="form-select form-control" required>
                        <?php
                        $statusList = ["Menunggu Konfirmasi", "Belum Bayar", "Sudah Bayar", "Diterima", "Selesai", "Ditolak"];
                        foreach ($statusList as $s): ?>
                            <option value="<?= $s ?>" <?= $transaksi['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 col-sm-4">
                    <button type="submit" name="update_status" class="btn btn-primary w-100">Update Status</button>
                </div>
            </form>

            <h5 class="mb-3 fw-bold">Detail Produk</h5>
            <div class="table-responsive mb-5">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Foto</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th width="100">Qty</th>
                            <th width="180">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($d = mysqli_fetch_assoc($detail)):
                            $qty = $d['jumlah'] ?: 1;
                            $subtotal = $d['subtotal'] ?: ($d['harga'] * $qty);
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?php if ($d['foto']): ?>
                                        <img src="../assets/uploads/produk/<?= $d['foto'] ?>" width="90" class="rounded border">
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= $d['namaproduk'] ?></div>
                                    <small class="text-muted"><?= substr($d['deskripsi'], 0, 70) ?>...</small>
                                </td>
                                <td><?= $d['namakategori'] ?: '-' ?></td>
                                <td>Rp <?= number_format($d['harga']) ?></td>
                                <td><?= $qty ?></td>
                                <td><b class="text-primary">Rp <?= number_format($subtotal) ?></b></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <h5 class="mb-3 fw-bold">Data Pembayaran</h5>
            <?php if ($transaksi['metodebayar'] == 'Tunai'): ?>
                <div class="alert alert-success border-0">Pembayaran dilakukan secara <b>Tunai / Cash on Delivery</b></div>
            <?php else: ?>
                <?php if ($bayar): ?>
                    <table class="table table-bordered align-middle mb-4">
                        <tr>
                            <th width="220">Atas Nama</th>
                            <td><?= $bayar['atasnama'] ?></td>
                        </tr>
                        <tr>
                            <th>Bank</th>
                            <td><?= $bayar['bank'] ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td><b class="text-primary">Rp <?= number_format($bayar['jumlah']) ?></b></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><?= date('d M Y H:i', strtotime($bayar['tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <th>Bukti Bayar</th>
                            <td>
                                <?php if ($bayar['buktibayar']): ?>
                                    <img src="../assets/uploads/bukti/<?= $bayar['buktibayar'] ?>" width="200" class="img-thumbnail">
                                <?php else: ?>
                                    <span class="text-muted small">Tidak ada file</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning border-0">Belum ada konfirmasi pembayaran transfer dari customer.</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <a href="index.php?page=transaksi" class="btn btn-secondary px-4">← Kembali</a>
                <a href="cetaknota.php?id=<?= $id ?>" target="_blank" class="btn btn-success px-4"><i class="fa fa-print me-2"></i>Cetak Nota</a>
            </div>

        </div>
    </div>
</div>