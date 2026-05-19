<?php
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <h4 class="mb-4">Laporan Transaksi</h4>

            <!-- FILTER -->
            <form method="GET" class="mb-4">
                <input type="hidden" name="page" value="laporantransaksi">

                <div class="row">
                    <div class="col-md-3">
                        <label>Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control"
                            value="<?= $tanggal_awal ?>">
                    </div>

                    <div class="col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control"
                            value="<?= $tanggal_akhir ?>">
                    </div>

                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            Filter
                        </button>

                        <!-- CETAK -->
                        <a href="laporantransaksicetak.php?tanggal_awal=<?= $tanggal_awal ?>&tanggal_akhir=<?= $tanggal_akhir ?>"
                            target="_blank"
                            class="btn btn-danger ml-1">
                            Cetak PDF
                        </a>
                    </div>
                </div>
            </form>

            <!-- TABEL -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Teknisi</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;

                        $where = "";

                        if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                            $where = "WHERE t.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                        }

                        $query = mysqli_query($koneksi, "
                            SELECT 
                                t.*,
                                c.nama AS customer,
                                te.nama AS teknisi
                            FROM transaksi t
                            LEFT JOIN users c ON t.customer_id = c.id
                            LEFT JOIN users te ON t.teknisi_id = te.id
                            $where
                            ORDER BY t.id DESC
                        ");

                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>

                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['tanggal'] ?></td>
                                <td><?= $row['customer'] ?></td>
                                <td><?= $row['teknisi'] ?: '-' ?></td>
                                <td>Rp <?= number_format($row['grandtotal']) ?></td>
                                <td><?= $row['status'] ?></td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>