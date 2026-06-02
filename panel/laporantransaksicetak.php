<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

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

$html = '
<h2 style="text-align:center;">Laporan Transaksi</h2>
<p>Tanggal: ' . ($tanggal_awal ?: '-') . ' s/d ' . ($tanggal_akhir ?: '-') . '</p>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Customer</th>
    <th>Teknisi</th>
    <th>Total</th>
    <th>Status</th>
</tr>
';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . $row['tanggal'] . '</td>
        <td>' . $row['customer'] . '</td>
        <td>' . ($row['teknisi'] ?: '-') . '</td>
        <td>Rp ' . number_format($row['grandtotal']) . '</td>
        <td>' . $row['status'] . '</td>
    </tr>
    ';
}

$html .= '</table>';

// DOMPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_transaksi.pdf", ["Attachment" => false]);
