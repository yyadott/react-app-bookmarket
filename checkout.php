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

$subtotalbelanja = 0;
$totalberat = 0;
$produk_keranjang = [];

foreach ($keranjang as $idproduk => $qty) {
    $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$idproduk'"));
    $produk['qty'] = $qty;
    $produk['subtotal'] = $produk['harga'] * $qty;

    $subtotalbelanja += $produk['subtotal'];
    // Ambil berat asli dari database jika tersedia, default 1000g jika kosong
    $berat_produk = isset($produk['berat']) ? $produk['berat'] : 1000;
    $totalberat += ($berat_produk * $qty); 
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

    // JIKA TRANSFER EFFECTIVE PROCESS
    if ($metodebayar == "Transfer") {
        $atasnama = mysqli_real_escape_string($koneksi, $_POST['atasnama']);
        $bank     = mysqli_real_escape_string($koneksi, $_POST['bank']);
        $folder   = "assets/uploads/bukti/";
        $bukti    = "";

        if (!is_dir($folder)) mkdir($folder, 0777, true);

        if (isset($_FILES['buktibayar']) && $_FILES['buktibayar']['error'] === UPLOAD_ERR_OK) {
            $fileExtension = pathinfo($_FILES['buktibayar']['name'], PATHINFO_EXTENSION);
            $bukti = time() . "_" . bin2hex(random_bytes(4)) . "." . $fileExtension; // Rename aman
            move_uploaded_file($_FILES['buktibayar']['tmp_name'], $folder . $bukti);
        }

        mysqli_query($koneksi, "INSERT INTO pembayaran (transaksi_id, atasnama, bank, buktibayar, jumlah, tanggal) 
            VALUES ('$transaksi_id', '$atasnama', '$bank', '$bukti', '$grandtotal', NOW())");
    }

    unset($_SESSION['keranjang']);
    echo "<script>alert('Checkout berhasil');location='riwayatdetail.php?id=$transaksi_id';</script>";
    exit;
}
?>

<div class="container py-4 mb-5">
    <h4 class="fw-bold mb-4">Checkout</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Data Penerima</h5>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text" name="nohp" class="form-control" value="<?= htmlspecialchars($user['nohp']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control bg-light" rows="3" readonly required><?= htmlspecialchars($user['alamat']) ?></textarea>
                            <small class="text-muted">Alamat dikunci otomatis sesuai profile pendaftaran Anda.</small>
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
                            <input type="text" name="kodepos" id="kodepos" class="form-control bg-light" readonly required>
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
                            <input type="text" id="ongkir_text" class="form-control bg-light fw-bold text-dark" value="Rp 0" readonly>
                            <input type="hidden" name="ongkir" id="ongkir" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="deskripsi" class="form-control" placeholder="Tambahkan catatan khusus pengiriman jika ada..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metodebayar" id="metodebayar" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                        
                        <div id="transferArea" class="p-3 border rounded-3 bg-light" style="display:none;">
                            <h6 class="fw-bold text-primary mb-3">Informasi Rekening Toko</h6>
                            <div class="p-2 border border-dashed rounded bg-white mb-3">
                                <p class="mb-1 small text-secondary">Silakan transfer tepat senilai Grand Total ke:</p>
                                <p class="mb-0 fw-bold text-dark">BANK BCA: 123-4567-890 <br>A/N: BOOK MARKET OFFICIAL</p>
                            </div>
                            <h6 class="fw-bold mb-3">Konfirmasi Bukti Transfer</h6>
                            <div class="mb-2">
                                <label class="form-label small text-secondary">Atas Nama Pemilik Rekening Anda</label>
                                <input type="text" name="atasnama" id="atasnama" class="form-control bg-white">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-secondary">Nama Bank Pengirim</label>
                                <input type="text" name="bank" id="bank" class="form-control bg-white" placeholder="Contoh: Mandiri, BRI, BCA">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small text-secondary">Unggah Bukti Transaksi</label>
                                <input type="file" name="buktibayar" id="buktibayar" class="form-control bg-white" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Ringkasan Pesanan</h5>
                        <?php foreach ($produk_keranjang as $p) { ?>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($p['namaproduk']) ?></h6>
                                    <small class="text-muted"><?= $p['qty'] ?> x Rp <?= number_format($p['harga']) ?></small>
                                </div>
                                <div class="fw-bold text-primary">Rp <?= number_format($p['subtotal']) ?></div>
                            </div>
                        <?php } ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>Rp <?= number_format($subtotalbelanja) ?></span></div>
                        <div class="d-flex justify-content-between mb-3"><span>Ongkir</span><span id="ongkir_label" class="fw-bold">Rp 0</span></div>
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
    const totalBerat = <?= $totalberat ?>; // Berat dinamis dari php keranjang

    // LOAD PROVINSI
    fetch('api/provinsi.php')
        .then(res => res.json())
        .then(result => {
            let provSelect = document.getElementById('provinsi');
            result.data.forEach(item => {
                let opt = document.createElement('option');
                opt.value = item.name;
                opt.textContent = item.name;
                opt.setAttribute('data-provinsi', item.id);
                provSelect.appendChild(opt);
            });
        });

    // EVENT PROVINSI BERUBAH
    document.getElementById('provinsi').addEventListener('change', function() {
        let provinceId = this.options[this.selectedIndex].getAttribute('data-provinsi');
        let kotaSelect = document.getElementById('kota');
        let kodeposInput = document.getElementById('kodepos');
        
        kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        kodeposInput.value = '';
        document.getElementById('destination_id').value = '';
        resetOngkir();

        if(!provinceId) return;

        fetch('api/kota.php?id=' + provinceId)
            .then(res => res.json())
            .then(result => {
                result.data.forEach(item => {
                    let opt = document.createElement('option');
                    opt.value = item.name;
                    opt.textContent = item.name;
                    opt.setAttribute('data-id', item.id);
                    opt.setAttribute('data-kodepos', item.zip_code || '');
                    kotaSelect.appendChild(opt);
                });
            });
    });

    // EVENT KOTA BERUBAH
    document.getElementById('kota').addEventListener('change', function() {
        let selected = this.options[this.selectedIndex];
        let destId = selected.getAttribute('data-id');
        let zipCode = selected.getAttribute('data-kodepos');
        
        document.getElementById('destination_id').value = destId || '';
        document.getElementById('kodepos').value = zipCode || '';
        
        hitungOngkir();
    });

    // EVENT KURIR BERUBAH
    document.getElementById('kurir').addEventListener('change', hitungOngkir);

    // LOGIKA HITUNG ONGKIR
    function hitungOngkir() {
        let destination = document.getElementById('destination_id').value;
        let courier = document.getElementById('kurir').value;
        if (!destination || !courier) {
            resetOngkir();
            return;
        }

        let formData = new FormData();
        formData.append('origin', '<?= $origin ?>');
        formData.append('destination', destination);
        formData.append('weight', totalBerat); // Menggunakan total berat asli belanjaan
        formData.append('courier', courier);
        formData.append('price', 'lowest');

        fetch('api/ongkir.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                let ongkir = (result.data && result.data.length > 0) ? parseInt(result.data[0].cost) : 0;
                let grandtotal = subtotalBelanja + ongkir;

                document.getElementById('ongkir').value = ongkir;
                document.getElementById('ongkir_text').value = 'Rp ' + ongkir.toLocaleString('id-ID');
                document.getElementById('ongkir_label').innerHTML = 'Rp ' + ongkir.toLocaleString('id-ID');
                document.getElementById('grandtotal').innerHTML = 'Rp ' + grandtotal.toLocaleString('id-ID');
            })
            .catch(err => {
                console.error("Gagal mengambil data ongkir:", err);
                resetOngkir();
            });
    }

    function resetOngkir() {
        document.getElementById('ongkir').value = 0;
        document.getElementById('ongkir_text').value = 'Rp 0';
        document.getElementById('ongkir_label').innerHTML = 'Rp 0';
        document.getElementById('grandtotal').innerHTML = 'Rp ' + subtotalBelanja.toLocaleString('id-ID');
    }

    // CONTROL AREA TRANSFER & VALIDASI MANDATORI
    document.getElementById('metodebayar').addEventListener('change', function() {
        let isTransfer = (this.value === 'Transfer');
        document.getElementById('transferArea').style.display = isTransfer ? 'block' : 'none';
        
        // Buat input transfer required jika opsi transfer dipilih
        document.getElementById('atasnama').required = isTransfer;
        document.getElementById('bank').required = isTransfer;
        document.getElementById('buktibayar').required = isTransfer;
    });
</script>

<?php include 'footer.php'; ?>