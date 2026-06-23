<?php
$user = $_SESSION['user'];
$no   = 1;

if ($user['role'] == 'Admin') {
    $query = mysqli_query($koneksi, "
        SELECT t.*, u.nama AS customer,
            GROUP_CONCAT(DISTINCT k.namakategori SEPARATOR ', ') AS kategori
        FROM transaksi t
        LEFT JOIN users u ON t.customer_id = u.id
        LEFT JOIN transaksidetail td ON t.id = td.transaksi_id
        LEFT JOIN produk l ON td.produk_id = l.id
        LEFT JOIN kategori k ON l.kategori_id = k.id
        GROUP BY t.id
        ORDER BY t.id DESC
    ");
} else {
    $seller_id = intval($user['id']);
    $query = mysqli_query($koneksi, "
        SELECT t.*, u.nama AS customer,
            GROUP_CONCAT(DISTINCT k.namakategori SEPARATOR ', ') AS kategori
        FROM transaksi t
        LEFT JOIN users u ON t.customer_id = u.id
        LEFT JOIN transaksidetail td ON t.id = td.transaksi_id
        LEFT JOIN produk l ON td.produk_id = l.id
        LEFT JOIN kategori k ON l.kategori_id = k.id
        WHERE l.seller_id = $seller_id
        GROUP BY t.id
        ORDER BY t.id DESC
    ");
}
?>

<div class="row page-titles mx-0">
    <div class="col"><h4>Data Transaksi</h4></div>
</div>

<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Daftar Transaksi</h5>
            </div>

            <div class="mb-3 d-flex flex-wrap gap-1">
                <?php
                $statusFilters = [
                    'Semua'               => 'secondary',
                    'Menunggu Konfirmasi' => 'warning',
                    'Sudah Bayar'         => 'primary',
                    'Diproses'            => 'info',
                    'Dikirim'             => 'info',
                    'Diterima'            => 'success',
                    'Selesai'             => 'success',
                    'Ditolak'             => 'danger',
                    'Dibatalkan'          => 'dark',
                ];
                foreach ($statusFilters as $label => $color): ?>
                    <button
                        class="btn btn-outline-<?= $color ?> btn-sm filter-btn <?= $label === 'Semua' ? 'active' : '' ?>"
                        data-status="<?= $label ?>" style="margin-right: 4px; margin-bottom: 6px; border-radius: 20px; padding: 4px 14px;">
                        <?= $label ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="datatable">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Metode</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php while ($row = mysqli_fetch_assoc($query)):
        $isCOD = ($row['metodebayar'] == 'Tunai');
        
        // Logika Tombol Tolak
        $bisaDitolak = false;
        if ($isCOD) {
            $bisaDitolak = in_array($row['status'], ['Menunggu Konfirmasi', 'Diproses']);
        } else {
            $bisaDitolak = ($row['status'] === 'Menunggu Konfirmasi');
        }

        // PERBAIKAN SINTAKS SWITCH CASE YANG BENAR & AMAN:
        switch ($row['status']) {
            case 'Menunggu Konfirmasi':
                $warna = 'background:#ffc107; color:#000;';
                break;
            case 'Sudah Bayar':
                $warna = 'background:#0d6efd; color:#fff;';
                break;
            case 'Diproses':
                $warna = 'background:#0dcaf0; color:#000;';
                break;
            case 'Dikirim':
                $warna = 'background:#6f42c1; color:#fff;';
                break;
            case 'Diterima':
                $warna = 'background:#20c997; color:#fff;';
                break;
            case 'Selesai':
                $warna = 'background:#198754; color:#fff;';
                break;
            case 'Ditolak':
                $warna = 'background:#dc3545; color:#fff;';
                break;
            case 'Dibatalkan':
                $warna = 'background:#6c757d; color:#fff;';
                break;
            default:
                $warna = 'background:#adb5bd; color:#fff;';
                break;
        }
    ?>
        <tr data-status="<?= $row['status'] ?>">
            <td><?= $no++ ?></td>
            <td><?= date('d M Y H:i', strtotime($row['tanggal'])) ?></td>
            <td><?= htmlspecialchars($row['customer'] ?? '-') ?></td>
            <td>
                <span class="badge" style="background:<?= $isCOD ? '#20c997' : '#6f42c1' ?>; color:white; padding:6px 12px; border-radius: 20px;">
                    <?= $isCOD ? 'COD' : 'Transfer' ?>
                </span>
            </td>
            <td><b>Rp <?= number_format($row['grandtotal'] ?? 0) ?></b></td>
            <td>
                <span class="badge" style="<?= $warna ?> padding:6px 14px; border-radius: 20px; font-weight: 700;">
                    <?= $row['status'] ?>
                </span>
            </td>
            <td>
                <a href="index.php?page=transaksidetail&id=<?= $row['id'] ?>"
                    class="btn btn-info btn-sm rounded-pill px-3">
                    <i class="fa fa-eye"></i> Detail
                </a>

                <?php if ($bisaDitolak): ?>
                    <a href="index.php?page=transaksitolak&id=<?= $row['id'] ?>"
                        class="btn btn-danger btn-sm rounded-pill px-3"
                        onclick="return confirm('Yakin ingin menolak transaksi #<?= $row['id'] ?> atas nama <?= addslashes(htmlspecialchars($row['customer'])) ?>?')">
                        <i class="fa fa-times"></i> Tolak
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const status = this.dataset.status;
        document.querySelectorAll('#datatable tbody tr').forEach(tr => {
            tr.style.display = (status === 'Semua' || tr.dataset.status === status) ? '' : 'none';
        });
    });
});
</script>