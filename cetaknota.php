<?php

include 'koneksi.php';

$id = intval($_GET['id']);

$transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT *
    FROM transaksi
    WHERE id='$id'
"));

if (!$transaksi) {

    echo "
        <script>
            alert('Data tidak ditemukan');
            window.close();
        </script>
    ";

    exit;
}

// DETAIL PRODUK
$detail = mysqli_query($koneksi, "
    SELECT
        td.*,
        p.namaproduk,
        p.harga
    FROM transaksidetail td
    JOIN produk p
    ON td.produk_id = p.id
    WHERE td.transaksi_id='$id'
");

// PEMBAYARAN
$pembayaran = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT *
    FROM pembayaran
    WHERE transaksi_id='$id'
"));

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Nota Transaksi #<?= $id ?>
    </title>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            font-size: 14px;
            color: #000;
        }

        .table td,
        .table th {
            padding: 8px;
        }

        @media print {

            .no-print {
                display: none;
            }
        }
    </style>

</head>

<body>

    <div class="container mt-4">

        <div class="text-center mb-4">

            <h3 class="mb-1">
                NOTA TRANSAKSI
            </h3>

            <h5>
                Book Market
            </h5>

        </div>

        <table class="table table-bordered">

            <tr>
                <th width="220">
                    ID Transaksi
                </th>

                <td>
                    #<?= $transaksi['id'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Tanggal
                </th>

                <td>
                    <?= $transaksi['tanggal'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Nama Customer
                </th>

                <td>
                    <?= $transaksi['nama'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    No HP
                </th>

                <td>
                    <?= $transaksi['nohp'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Email
                </th>

                <td>
                    <?= $transaksi['email'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Alamat
                </th>

                <td>
                    <?= $transaksi['alamat'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Kota
                </th>

                <td>
                    <?= $transaksi['kota'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Kode Pos
                </th>

                <td>
                    <?= $transaksi['kodepos'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Metode Pembayaran
                </th>

                <td>
                    <?= $transaksi['metodebayar'] ?>
                </td>
            </tr>

            <tr>
                <th>
                    Status
                </th>

                <td>
                    <?= $transaksi['status'] ?>
                </td>
            </tr>

        </table>

        <h5 class="mb-3">
            Detail Produk
        </h5>

        <table class="table table-bordered">

            <thead class="thead-light">

                <tr>

                    <th width="60">
                        No
                    </th>

                    <th>
                        Produk
                    </th>

                    <th width="120">
                        Harga
                    </th>

                    <th width="100">
                        Qty
                    </th>

                    <th width="150">
                        Subtotal
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php

                $no = 1;
                $grandtotal = 0;

                while ($d = mysqli_fetch_assoc($detail)) {

                    $qty = $d['jumlah'] ?? 1;

                    $subtotal = $d['harga'] * $qty;

                    $grandtotal += $subtotal;

                ?>

                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $d['namaproduk'] ?></td>
                        <td>Rp <?= number_format($d['harga']) ?></td>
                        <td><?= $qty ?></td>
                        <td>Rp <?= number_format($subtotal) ?></td>
                    </tr>

                <?php } ?>

            </tbody>

            <tfoot>

                <tr>
                    <th colspan="4" class="text-right">
                        Total
                    </th>
                    <th>
                        Rp <?= number_format($transaksi['grandtotal'] - $transaksi['ongkir']) ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">
                        Ongkir
                    </th>
                    <th>
                        Rp <?= number_format($transaksi['ongkir']) ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">
                        Grand Total
                    </th>
                    <th>
                        Rp <?= number_format($transaksi['grandtotal']) ?>
                    </th>
                </tr>

            </tfoot>

        </table>

        <?php if ($transaksi['metodebayar'] == 'Transfer' && $pembayaran) { ?>

            <h5 class="mb-3 mt-4">
                Data Pembayaran
            </h5>

            <table class="table table-bordered">

                <tr>

                    <th width="220">
                        Atas Nama
                    </th>

                    <td>
                        <?= $pembayaran['atasnama'] ?>
                    </td>

                </tr>

                <tr>

                    <th>
                        Bank
                    </th>

                    <td>
                        <?= $pembayaran['bank'] ?>
                    </td>

                </tr>

                <tr>

                    <th>
                        Jumlah
                    </th>

                    <td>
                        Rp <?= number_format($pembayaran['jumlah']) ?>
                    </td>

                </tr>

                <tr>

                    <th>
                        Tanggal
                    </th>

                    <td>
                        <?= $pembayaran['tanggal'] ?>
                    </td>

                </tr>

            </table>

        <?php } ?>

        <div class="text-center mt-5">

            <p>
                Terima kasih telah melakukan pemesanan
            </p>

        </div>

    </div>

    <script>
        window.print();
    </script>

</body>

</html>