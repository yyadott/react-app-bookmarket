<div class="row page-titles mx-0">
    <div class="col">
        <h4>Data Transaksi</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Daftar Transaksi</h5>

                <!-- <a href="index.php?page=transaksitambah" class="btn btn-primary btn-sm">
                    + Tambah Transaksi
                </a> -->
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Kategori</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th width="170">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        $user = $_SESSION['user'];

                        if ($user['role'] == 'Admin') {

                            $query = mysqli_query($koneksi, "
                                SELECT 
                                    t.*,
                                    u.nama AS customer,
                                    GROUP_CONCAT(DISTINCT p.namakategori SEPARATOR ', ') AS kategori
                                FROM transaksi t
                                LEFT JOIN users u ON t.customer_id = u.id
                                LEFT JOIN transaksidetail td ON t.id = td.transaksi_id
                                LEFT JOIN produk l ON td.produk_id = l.id
                                LEFT JOIN kategori p ON l.kategori_id = p.id
                                GROUP BY t.id
                                ORDER BY t.id DESC
                            ");
                        } else {
                            $query = mysqli_query($koneksi, "
                                SELECT 
                                    t.*,
                                    u.nama AS customer,
                                    GROUP_CONCAT(DISTINCT p.namakategori SEPARATOR ', ') AS kategori
                                FROM transaksi t
                                LEFT JOIN users u ON t.customer_id = u.id
                                LEFT JOIN transaksidetail td ON t.id = td.transaksi_id
                                LEFT JOIN produk l ON td.produk_id = l.id
                                LEFT JOIN kategori p ON l.kategori_id = p.id
                                WHERE p.id = {$user['id']}
                                GROUP BY t.id
                                ORDER BY t.id DESC
                            ");
                        }

                        while ($row = mysqli_fetch_assoc($query)) {

                            // WARNA STATUS
                            $badge = "secondary";
                            if ($row['status'] == "Menunggu Konfirmasi") $badge = "warning";
                            elseif ($row['status'] == "Belum Bayar") $badge = "danger";
                            elseif ($row['status'] == "Sudah Bayar") $badge = "primary";
                            elseif ($row['status'] == "Diterima") $badge = "success";
                            elseif ($row['status'] == "Ditolak") $badge = "dark";
                        ?>

                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['tanggal'] ?></td>
                                <td><?= $row['customer'] ?></td>
                                <td><?= $row['kategori'] ?: '-' ?></td>
                                <td>Rp <?= number_format($row['grandtotal'] ?? 0) ?></td>
                                <td>
                                    <span class="badge badge-<?= $badge ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td>

                                    <a href="index.php?page=transaksidetail&id=<?= $row['id'] ?>"
                                        class="btn btn-info btn-sm">
                                        Detail
                                    </a>

                                    <a href="index.php?page=transaksihapus&id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus transaksi ini?')">
                                        Hapus
                                    </a>

                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>