<?php
$user_id = intval($_SESSION['user']['id']);
$role    = $_SESSION['user']['role'];

$bulan = date('m');
$tahun = date('Y');

$total_customer = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT COUNT(*) as total FROM users WHERE role='Customer'"
))['total'];

$total_transaksi = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT COUNT(*) as total FROM transaksi"
))['total'];

$total_pendapatan = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT SUM(grandtotal) as total 
     FROM transaksi 
     WHERE status IN ('Sudah Bayar','Diterima')
     AND MONTH(tanggal)='$bulan'
     AND YEAR(tanggal)='$tahun'"
))['total'] ?? 0;
?>

<style>
    .card {
        border-radius: 12px;
        overflow: hidden;
    }

    .card-body {
        padding: 20px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
    }

    .card h2 {
        font-size: 24px;
        font-weight: bold;
    }

    /* Gradient */
    .gradient-1 {
        background: linear-gradient(45deg, #1e3c72, #2a5298);
    }

    .gradient-2 {
        background: linear-gradient(45deg, #11998e, #38ef7d);
    }

    .gradient-3 {
        background: linear-gradient(45deg, #f7971e, #ffd200);
    }

    .gradient-4 {
        background: linear-gradient(45deg, #8e2de2, #4a00e0);
    }

    .gradient-5 {
        background: linear-gradient(45deg, #ff416c, #ff4b2b);
    }

    @media (max-width: 768px) {
        .card h2 {
            font-size: 20px;
        }
    }
</style>

<div class="container-fluid mt-3">
    <div class="row">

        <!-- CUSTOMER -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card gradient-1 text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Customer</h5>
                    <h2><?= $total_customer ?></h2>
                    <small>Semua pelanggan</small>
                    <span class="float-right display-5 opacity-5">
                        <i class="fa fa-users"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- TRANSAKSI -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card gradient-2 text-white">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= ($role == 'Customer') ? 'Transaksi Saya' : 'Total Transaksi' ?>
                    </h5>
                    <h2><?= $total_transaksi ?></h2>
                    <small>Semua transaksi</small>
                    <span class="float-right display-5 opacity-5">
                        <i class="fa fa-receipt"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- PENDAPATAN -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card gradient-3 text-white">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan Bulan Ini</h5>
                    <h2>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h2>
                    <small><?= date('F Y') ?></small>
                    <span class="float-right display-5 opacity-5">
                        <i class="fa fa-money-bill"></i>
                    </span>
                </div>
            </div>
        </div>


    </div>
</div>