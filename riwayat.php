<?php include 'header.php'; ?>

<?php

if (!isset($_SESSION['user'])) {

    echo "
        <script>
            alert('Silakan login terlebih dahulu');
            location='login.php';
        </script>
    ";

    exit;
}

$user_id = $_SESSION['user']['id'];

// DATA TRANSAKSI
$transaksi = mysqli_query($koneksi, "
    SELECT *
    FROM transaksi
    WHERE customer_id='$user_id'
    ORDER BY id DESC
");

?>

<div class="container py-4 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4 class="fw-bold mb-0">
            Riwayat Pesanan
        </h4>

        <span class="badge bg-primary px-3 py-2">
            <?= mysqli_num_rows($transaksi) ?> Pesanan
        </span>

    </div>

    <?php if (mysqli_num_rows($transaksi) > 0) { ?>

        <div class="row g-3">

            <?php while ($t = mysqli_fetch_assoc($transaksi)) { ?>

                <?php

                // STATUS COLOR
                $badge = "secondary";

                if ($t['status'] == "Belum Bayar") {
                    $badge = "warning";
                } elseif ($t['status'] == "Diterima") {
                    $badge = "primary";
                } elseif ($t['status'] == "Selesai") {
                    $badge = "success";
                } elseif ($t['status'] == "Ditolak") {
                    $badge = "danger";
                }

                // DETAIL PRODUK
                $detail = mysqli_query($koneksi, "
                    SELECT
                        td.*,
                        p.namaproduk,
                        p.foto
                    FROM transaksidetail td
                    LEFT JOIN produk p
                    ON td.produk_id = p.id
                    WHERE td.transaksi_id='" . $t['id'] . "'
                    LIMIT 1
                ");

                $produk = mysqli_fetch_assoc($detail);

                ?>

                <div class="col-12">

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                        <div class="card-body p-3">

                            <div class="row align-items-center">

                                <!-- FOTO -->
                                <div class="col-4 col-md-2">

                                    <?php if ($produk && $produk['foto']) { ?>

                                        <img src="assets/uploads/produk/<?= $produk['foto'] ?>"
                                            class="img-fluid rounded-4"
                                            style="height:100px; width:100%; object-fit:cover;">

                                    <?php } else { ?>

                                        <img src="https://via.placeholder.com/300x300"
                                            class="img-fluid rounded-4">

                                    <?php } ?>

                                </div>

                                <!-- DETAIL -->
                                <div class="col-8 col-md-7">

                                    <div class="d-flex align-items-center gap-2 mb-2">

                                        <span class="badge bg-<?= $badge ?>">

                                            <?= $t['status'] ?>

                                        </span>

                                        <small class="text-muted">

                                            #TRX<?= $t['id'] ?>

                                        </small>

                                    </div>

                                    <h6 class="fw-bold mb-1">

                                        <?= $produk['namaproduk'] ?? 'Produk Tidak Ditemukan' ?>

                                    </h6>

                                    <small class="text-muted d-block mb-2">

                                        <?= date('d M Y', strtotime($t['tanggal'])) ?>

                                    </small>

                                    <h5 class="fw-bold text-primary mb-0">

                                        Rp <?= number_format($t['grandtotal']) ?>

                                    </h5>

                                </div>

                                <!-- AKSI -->
                                <div class="col-12 col-md-3 text-md-end mt-3 mt-md-0">

                                    <div class="mb-2">

                                        <small class="text-muted d-block">
                                            Pembayaran
                                        </small>

                                        <span class="fw-semibold">

                                            <?= $t['metodebayar'] ?>

                                        </span>

                                    </div>

                                    <a href="riwayatdetail.php?id=<?= $t['id'] ?>"
                                        class="btn btn-primary btn-sm rounded-pill px-3">

                                        Detail Pesanan
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body text-center py-5">

                <i class="bi bi-receipt display-3 text-muted"></i>

                <h5 class="fw-bold mt-3">
                    Belum Ada Pesanan
                </h5>

                <p class="text-muted mb-4">
                    Kamu belum pernah melakukan pemesanan
                </p>

                <a href="produk.php"
                    class="btn btn-primary rounded-pill px-4">

                    Mulai Belanja
                </a>

            </div>

        </div>

    <?php } ?>

</div>

<?php include 'footer.php'; ?>