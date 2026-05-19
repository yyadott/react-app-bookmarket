<?php
include 'header.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu');location='login.php';</script>";
    exit;
}

$keranjang = $_SESSION['keranjang'] ?? [];
if (count($keranjang) <= 0) {
    echo "<script>alert('Keranjang kosong');location='keranjang.php';</script>";
    exit;
}

$user_id = $_SESSION['user']['id'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'"));

$apikey = "MASUKKAN_API_KEY_RAJAONGKIR";
$origin = 1391; // ORIGIN TOKO

// Ambil data produk di keranjang terlebih dahulu untuk menghemat query database
$subtotalbelanja = 0;
$totalberat = 0;
$produk_keranjang = [];

foreach ($keranjang as $idproduk => $qty) {
    $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$idproduk'"));
    $produk['qty'] = $qty;
    $produk['subtotal'] = $produk['harga'] * $qty;

    $subtotalbelanja += $produk['subtotal'];
    $totalberat += (1000 * $qty); // contoh berat 1000 gram / produk
    $produk_keranjang[] = $produk;
}

// PROSES CHECKOUT
if (isset($_POST['checkout'])) {
    $customer_id = $user['id'];
    $tanggal     = date('Y-m-d');
    $ongkir      = intval($_POST['ongkir']);
    $destination = intval($_POST['destination_id']);
    $grandtotal  = $subtotalbelanja + $ongkir;
    $status      = ($_POST['metodebayar'] == "Tunai") ? "Sudah Bayar" : "Menunggu Konfirmasi";

    // Escape semua input POST secara dinamis
    $fields = ['nama', 'email', 'nohp', 'alamat', 'provinsi', 'kota', 'kodepos', 'deskripsi', 'metodebayar', 'kurir'];
    foreach ($fields as $field) {
        $$field = mysqli_real_escape_string($koneksi, $_POST[$field] ?? '');
    }

    // INSERT TRANSAKSI
    mysqli_query($koneksi, "INSERT INTO transaksi (customer_id, tanggal, deskripsi, nama, nohp, email, alamat, provinsi, kota, kodepos, kurir, ongkir, grandtotal, metodebayar, status) 
        VALUES ('$customer_id', '$tanggal', '$deskripsi', '$nama', '$nohp', '$email', '$alamat', '$provinsi', '$kota', '$kodepos', '$kurir', '$ongkir', '$grandtotal', '$metodebayar', '$status')");

    $transaksi_id = mysqli_insert_id($koneksi);

    // INSERT DETAIL TRANSAKSI
    foreach ($keranjang as $idproduk => $qty) {

        $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$idproduk'"));

        $subtotal = $produk['harga'] * $qty;

        mysqli_query($koneksi, "INSERT INTO transaksidetail (transaksi_id, produk_id, jumlah, subtotal) VALUES ('$transaksi_id', '$idproduk', '$qty', '$subtotal')");
    }

    // JIKA TRANSFER
    if ($metodebayar == "Transfer") {
        $atasnama = mysqli_real_escape_string($koneksi, $_POST['atasnama']);
        $bank     = mysqli_real_escape_string($koneksi, $_POST['bank']);
        $folder   = "assets/uploads/bukti/";
        $bukti    = "";

        if (!is_dir($folder)) mkdir($folder, 0777, true);

        if (!empty($_FILES['buktibayar']['name'])) {
            $bukti = time() . "_" . $_FILES['buktibayar']['name'];
            move_uploaded_file($_FILES['buktibayar']['tmp_name'], $folder . $bukti);
        }

        mysqli_query($koneksi, "INSERT INTO pembayaran (transaksi_id, atasnama, bank, buktibayar, jumlah, tanggal) 
            VALUES ('$transaksi_id', '$atasnama', '$bank', '$bukti', '$grandtotal', NOW())");
    }

    unset($_SESSION['keranjang']);
    echo "<script>alert('Checkout berhasil');location='riwayatdetail.php?id=$transaksi_id';</script>";
}
?>

<div class="container py-4 mb-5">
    <h4 class="fw-bold mb-4">Checkout</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- FORM DATA PENERIMA -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Data Penerima</h5>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= $user['nama'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text" name="nohp" class="form-control" value="<?= $user['nohp'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="4" required><?= $user['alamat'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Provinsi</label>
                            <select name="provinsi" id="provinsi" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kota / Kabupaten</label>
                            <select name="kota" id="kota" class="form-select" required>
                                <option value="">-- Pilih Kota --</option>
                            </select>
                        </div>
                        <input type="hidden" name="destination_id" id="destination_id">
                        <div class="mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="kodepos" id="kodepos" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kurir</label>
                            <select name="kurir" id="kurir" class="form-select" required>
                                <option value="">-- Pilih Kurir --</option>
                                <option value="jne">JNE</option>
                                <option value="jnt">J&T</option>
                                <option value="sicepat">SiCepat</option>
                                <option value="tiki">TIKI</option>
                                <option value="anteraja">AnterAja</option>
                                <option value="pos">POS Indonesia</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ongkir</label>
                            <input type="text" id="ongkir_text" class="form-control" readonly>
                            <input type="hidden" name="ongkir" id="ongkir">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="deskripsi" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metodebayar" id="metodebayar" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                        <!-- TRANSFER AREA -->
                        <div id="transferArea" style="display:none;">
                            <hr>
                            <h6 class="fw-bold mb-3">Upload Pembayaran</h6>
                            <div class="mb-3"><label class="form-label">Atas Nama</label><input type="text" name="atasnama" class="form-control"></div>
                            <div class="mb-3"><label class="form-label">Bank</label><input type="text" name="bank" class="form-control"></div>
                            <div class="mb-3"><label class="form-label">Bukti Bayar</label><input type="file" name="buktibayar" class="form-control"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- RINGKASAN PESANAN -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Ringkasan Pesanan</h5>
                        <?php foreach ($produk_keranjang as $p) { ?>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1"><?= $p['namaproduk'] ?></h6>
                                    <small class="text-muted"><?= $p['qty'] ?> x Rp <?= number_format($p['harga']) ?></small>
                                </div>
                                <div class="fw-bold text-primary">Rp <?= number_format($p['subtotal']) ?></div>
                            </div>
                        <?php } ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>Rp <?= number_format($subtotalbelanja) ?></span></div>
                        <div class="d-flex justify-content-between mb-3"><span>Ongkir</span><span id="ongkir_label">Rp 0</span></div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Grand Total</h5>
                            <h4 class="fw-bold text-primary mb-0" id="grandtotal">Rp <?= number_format($subtotalbelanja) ?></h4>
                        </div>
                        <button type="submit" name="checkout" class="btn btn-primary w-100 rounded-pill py-3 fw-bold mt-4">Buat Pesanan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const subtotalBelanja = <?= $subtotalbelanja ?>;

    // AMBIL PROVINSI
    fetch('api/provinsi.php')
        .then(res => res.json())
        .then(result => {
            let provSelect = document.getElementById('provinsi');
            result.data.forEach(item => {
                provSelect.innerHTML += `<option value="${item.name}" data-provinsi="${item.id}">${item.name}</option>`;
            });
        });

    // AMBIL KOTA BERDASARKAN PROVINSI
    document.getElementById('provinsi').addEventListener('change', function() {
        let provinceId = this.options[this.selectedIndex].getAttribute('data-provinsi');
        fetch('api/kota.php?id=' + provinceId)
            .then(res => res.json())
            .then(result => {
                let kotaSelect = document.getElementById('kota');
                kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                result.data.forEach(item => {
                    kotaSelect.innerHTML += `<option value="${item.name}" data-id="${item.id}" data-kodepos="${item.zip_code}">${item.name}</option>`;
                });
            });
    });

    // PILIH KOTA
    document.getElementById('kota').addEventListener('change', function() {
        let selected = this.options[this.selectedIndex];
        document.getElementById('destination_id').value = selected.getAttribute('data-id');
        hitungOngkir();
    });

    // PILIH KURIR
    document.getElementById('kurir').addEventListener('change', hitungOngkir);

    // HITUNG ONGKIR VIA API
    function hitungOngkir() {
        let destination = document.getElementById('destination_id').value;
        let courier = document.getElementById('kurir').value;
        if (!destination || !courier) return;

        let formData = new FormData();
        formData.append('origin', '<?= $origin ?>');
        formData.append('destination', destination);
        formData.append('weight', 1000);
        formData.append('courier', courier);
        formData.append('price', 'lowest');

        fetch('api/ongkir.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                let ongkir = result.data ? result.data[0].cost : 0;
                let grandtotal = subtotalBelanja + ongkir;

                document.getElementById('ongkir').value = ongkir;
                document.getElementById('ongkir_text').value = 'Rp ' + ongkir.toLocaleString('id-ID');
                document.getElementById('ongkir_label').innerHTML = 'Rp ' + ongkir.toLocaleString('id-ID');
                document.getElementById('grandtotal').innerHTML = 'Rp ' + grandtotal.toLocaleString('id-ID');
            });
    }

    // SHOW/HIDE AREA TRANSFER
    document.getElementById('metodebayar').addEventListener('change', function() {
        document.getElementById('transferArea').style.display = (this.value === 'Transfer') ? 'block' : 'none';
    });
</script>

<?php include 'footer.php'; ?>