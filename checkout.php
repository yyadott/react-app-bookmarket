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

$apikey = "7ff8406f12c653758df1a5fa6d6bf474";
$origin = 105; // ORIGIN TOKO (ID KOTA CIMAHI)

// Ambil data produk di keranjang
$subtotalbelanja = 0;
$totalberat = 0;
$produk_keranjang = [];

foreach ($keranjang as $idproduk => $qty) {
    $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$idproduk'"));
    
    $qty = intval($qty); 
    $produk['qty'] = $qty;
    $produk['subtotal'] = $produk['harga'] * $qty;

    $subtotalbelanja += $produk['subtotal'];
    
    $berat_database = (isset($produk['berat']) && $produk['berat'] !== '') ? $produk['berat'] : 1000;
    $berat_item = intval($berat_database); 
    $totalberat += ($berat_item * $qty); 
    
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
    <h4 class="fw-bold mb-4">Checkout Pembayaran</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Alamat Pengiriman</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Handphone</label>
                                <input type="text" name="nohp" class="form-control" value="<?= htmlspecialchars($user['nohp']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap Rumah</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, Kecamatan" required><?= htmlspecialchars($user['alamat']) ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi</label>
                                <select name="provinsi" id="provinsi" class="form-select" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota / Kabupaten</label>
                                <select name="kota" id="kota" class="form-select" required>
                                    <option value="">-- Pilih Kota --</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="destination_id" id="destination_id" value="<?= isset($user['destination_id']) ? $user['destination_id'] : '' ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="kodepos" id="kodepos" class="form-control" value="<?= isset($user['kodepos']) ? htmlspecialchars($user['kodepos']) : '' ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jasa Kurir Pengiriman</label>
                                <select name="kurir" id="kurir" class="form-select" required>
                                    <option value="">-- Pilih Kurir --</option>
                                    <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                                    <option value="jnt">J&T Express</option>
                                    <option value="sicepat">SiCepat Express</option>
                                    <option value="tiki">TIKI</option>
                                    <option value="pos">POS Indonesia</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Biaya Ongkos Kirim (Ongkir)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" id="ongkir_text" class="form-control bg-light fw-bold text-primary" value="0" readonly>
                            </div>
                            <input type="hidden" name="ongkir" id="ongkir" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Tambahan untuk Kurir</label>
                            <textarea name="deskripsi" class="form-control" placeholder="Contoh: Titipkan di pos satpam jika rumah pagar terkunci." rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metodebayar" id="metodebayar" class="form-select" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Tunai">Tunai / COD</option>
                                <option value="Transfer">Transfer Bank (Verifikasi Manual)</option>
                            </select>
                        </div>

                        <div id="transferArea" style="display:none;" class="p-3 bg-light rounded-4 border border-warning-subtle mt-3">
                            <h6 class="fw-bold text-warning mb-3"><i class="bi bi-bank"></i> Rekening Pembayaran Toko</h6>
                            <div class="p-3 bg-white rounded-3 shadow-sm mb-3">
                                <p class="mb-1 text-muted small">Silakan transfer tepat sesuai Grand Total ke rekening berikut:</p>
                                <h5 class="fw-bold text-dark mb-1">Bank BCA</h5>
                                <h4 class="fw-bold text-primary mb-1" style="letter-spacing: 1px;">2350-0810-41</h4>
                                <p class="mb-0 fw-semibold text-muted">a.n. Coolcat Book Market Inc</p>
                            </div>
                            <hr>
                            <h6 class="fw-bold mb-3">Konfirmasi Pembayaran Anda</h6>
                            <div class="mb-3">
                                <label class="form-label">Atas Nama Rekening Anda</label>
                                <input type="text" name="atasnama" class="form-control" placeholder="Contoh: Taryadi">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Bank Anda</label>
                                <input type="text" name="bank" class="form-control" placeholder="Contoh: Bank Mandiri / BRI">
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Upload Bukti Foto Transfer</label>
                                <input type="file" name="buktibayar" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Ringkasan Belanja</h5>
                        <?php foreach ($produk_keranjang as $p) { ?>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1" style="font-size:0.95rem;"><?= htmlspecialchars($p['namaproduk']) ?></h6>
                                    <small class="text-muted">
                                        <?= $p['qty'] ?> barang (<?= number_format(($p['berat'] !== '') ? intval($p['berat']) : 1000) ?>g) x Rp <?= number_format(intval($p['harga'])) ?>
                                    </small>
                                </div>
                                <div class="fw-bold text-dark text-end" style="font-size:0.95rem;">Rp <?= number_format($p['subtotal']) ?></div>
                            </div>
                        <?php } ?>
                        <hr class="text-muted">
                        <div class="d-flex justify-content-between mb-2 text-muted"><span>Subtotal Belanja</span><span>Rp <?= number_format($subtotalbelanja) ?></span></div>
                        <div class="d-flex justify-content-between mb-3 text-muted"><span>Total Berat</span><span><?= number_format($totalberat) ?> gram</span></div>
                        <div class="d-flex justify-content-between mb-3 text-muted"><span>Estimasi Ongkir</span><span id="ongkir_label" class="fw-bold text-dark">Rp 0</span></div>
                        <hr class="text-muted">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Total Pembayaran</h5>
                            <h4 class="fw-bold text-primary mb-0" id="grandtotal">Rp <?= number_format($subtotalbelanja) ?></h4>
                        </div>
                        <button type="submit" name="checkout" class="btn btn-primary w-100 rounded-pill py-3 fw-bold mt-4 shadow-sm">Selesaikan Pesanan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const subtotalBelanja = <?= intval($subtotalbelanja) ?>;
    const totalBerat = <?= intval($totalberat) ?>;
    
    const userProvinsi = "<?= isset($user['provinsi']) ? $user['provinsi'] : '' ?>";
    const userKota = "<?= isset($user['kota']) ? $user['kota'] : '' ?>";

    // 1. AMBIL DATA PROVINSI
    fetch('api/provinsi.php')
        .then(res => res.json())
        .then(result => {
            let provSelect = document.getElementById('provinsi');
            provSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            
            if (result.status === 'success' && result.data) {
                result.data.forEach(item => {
                    let selected = (item.name.toLowerCase() === userProvinsi.toLowerCase()) ? 'selected' : '';
                    provSelect.innerHTML += `<option value="${item.name}" data-provinsi="${item.id}" ${selected}>${item.name}</option>`;
                });
            }
            
            if(userProvinsi !== "" && provSelect.value !== "") {
                let event = new Event('change');
                provSelect.dispatchEvent(event);
            }
        })
        .catch(err => console.error("Gagal memuat API Provinsi:", err));

    // 2. AMBIL DATA KOTA BERDASARKAN PROVINSI
    document.getElementById('provinsi').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let provinceId = selectedOption.getAttribute('data-provinsi');
        
        let kotaSelect = document.getElementById('kota');
        kotaSelect.innerHTML = '<option value="">-- Mohon Tunggu... --</option>';
        
        if(!provinceId) {
            kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            return;
        }

        fetch('api/kota.php?id=' + provinceId)
            .then(res => res.json())
            .then(result => {
                kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                
                if (result.status === 'success' && result.data) {
                    result.data.forEach(item => {
                        let selected = (item.name.toLowerCase() === userKota.toLowerCase()) ? 'selected' : '';
                        kotaSelect.innerHTML += `<option value="${item.name}" data-id="${item.id}" data-kodepos="${item.zip_code}" ${selected}>${item.name}</option>`;
                    });
                }

                // Setup otomatis jika data kota berasal dari pembacaan awal profil database
                setTimeout(() => {
                    if (kotaSelect.selectedIndex >= 0) {
                        let autoSelectedKota = kotaSelect.options[kotaSelect.selectedIndex];
                        if (autoSelectedKota && autoSelectedKota.value !== "") {
                            document.getElementById('destination_id').value = autoSelectedKota.getAttribute('data-id');
                            
                            let zip = autoSelectedKota.getAttribute('data-kodepos');
                            document.getElementById('kodepos').value = (zip && zip !== "null") ? zip : '';
                            
                            hitungOngkir();
                        } 
                    }
                }, 300);
            })
            .catch(err => console.error("Gagal memuat API Kota:", err));
    });

    // 3. EVENT KETIKA USER MENGUBAH PILIHAN KOTA KABUPATEN MANUAL
    document.getElementById('kota').addEventListener('change', function() {
        let selected = this.options[this.selectedIndex];
        
        if(selected && selected.value !== "") {
            document.getElementById('destination_id').value = selected.getAttribute('data-id');
            
            // Pengisian Kode Pos otomatis secara langsung ke elemen input
            let kodePosBawaan = selected.getAttribute('data-kodepos');
            document.getElementById('kodepos').value = (kodePosBawaan && kodePosBawaan !== "null") ? kodePosBawaan : '';
            
            hitungOngkir();
        } else {
            document.getElementById('destination_id').value = '';
            document.getElementById('kodepos').value = ''; 
        }
    });

    // 4. EVENT KETIKA USER MENGUBAH PILIHAN JASA EKSPEDISI KURIR
    document.getElementById('kurir').addEventListener('change', hitungOngkir);

    // 5. CORE LOGIC HITUNG ONGKIR VIA API
    function hitungOngkir() {
        let destination = document.getElementById('destination_id').value;
        let courier = document.getElementById('kurir').value;
        
        if (!destination || !courier || destination === "" || destination === "null") {
            document.getElementById('ongkir').value = 0;
            document.getElementById('ongkir_text').value = '0';
            document.getElementById('ongkir_label').innerHTML = 'Rp 0';
            document.getElementById('grandtotal').innerHTML = 'Rp ' + subtotalBelanja.toLocaleString('id-ID');
            return;
        }

        document.getElementById('ongkir_text').value = 'Menghitung...';

        let dataPayload = {
            origin: parseInt("<?= $origin ?>"),
            destination: parseInt(destination),
            weight: parseInt(totalBerat),
            courier: courier
        };

        fetch('api/ongkir.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dataPayload)
            })
            .then(res => res.json())
            .then(result => {
                let ongkir = (result.status === 'success' && result.data && result.data[0]) ? parseInt(result.data[0].cost) : 0;
                let grandtotal = subtotalBelanja + ongkir;

                document.getElementById('ongkir').value = ongkir;
                document.getElementById('ongkir_text').value = ongkir.toLocaleString('id-ID');
                document.getElementById('ongkir_label').innerHTML = 'Rp ' + ongkir.toLocaleString('id-ID');
                document.getElementById('grandtotal').innerHTML = 'Rp ' + grandtotal.toLocaleString('id-ID');
            })
            .catch(error => {
                console.error("Gagal memuat ongkir:", error);
                document.getElementById('ongkir_text').value = 'Gagal memuat ongkir';
            });
    }

    // 6. ANIMASI TOGGLE METODE TRANSFER
    document.getElementById('metodebayar').addEventListener('change', function() {
        document.getElementById('transferArea').style.display = (this.value === 'Transfer') ? 'block' : 'none';
    });
</script>
<?php include 'footer.php'; ?>