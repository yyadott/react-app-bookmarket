<?php

if (isset($_POST['simpan'])) {

    $customer_id = $_POST['customer_id'];
    $tanggal     = date('Y-m-d');
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $alamat      = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $provinsi    = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $kota        = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $kodepos     = mysqli_real_escape_string($koneksi, $_POST['kodepos']);
    $kurir       = mysqli_real_escape_string($koneksi, $_POST['kurir']);
    $ongkir      = intval($_POST['ongkir']);
    $metodebayar = mysqli_real_escape_string($koneksi, $_POST['metode']);
    $status      = "Diterima";

    $atasnama = mysqli_real_escape_string($koneksi, $_POST['atasnama']);
    $bank     = mysqli_real_escape_string($koneksi, $_POST['bank']);

    $produk_id = $_POST['produk_id'];
    $qty       = $_POST['qty'];

    $queryuser = $koneksi->query("SELECT nama, email, nohp FROM users WHERE id='$customer_id'");
    $rowuser   = $queryuser->fetch_assoc();

    $nama  = $rowuser['nama']  ?? '';
    $email = $rowuser['email'] ?? '';
    $nohp  = $rowuser['nohp']  ?? '';

    if (empty($produk_id[0])) {

        echo "<script>alert('Minimal pilih 1 produk');</script>";
    } else {

        $subtotalbelanja = 0;

        // HITUNG TOTAL
        foreach ($produk_id as $i => $idproduk) {

            $p = mysqli_fetch_assoc(mysqli_query(
                $koneksi,
                "SELECT harga FROM produk WHERE id='$idproduk'"
            ));

            $subtotalbelanja += $p['harga'] * $qty[$i];
        }

        $grandtotal = $subtotalbelanja + $ongkir;

        // INSERT TRANSAKSI
        mysqli_query($koneksi, "
            INSERT INTO transaksi
            (customer_id, nama, email, nohp, tanggal, alamat, provinsi, kota,
             kodepos, kurir, ongkir, deskripsi, grandtotal, metodebayar, status)
            VALUES
            ('$customer_id','$nama','$email','$nohp','$tanggal','$alamat',
             '$provinsi','$kota','$kodepos','$kurir','$ongkir',
             '$deskripsi','$grandtotal','$metodebayar','$status')
        ");

        $transaksi_id = mysqli_insert_id($koneksi);

        // INSERT DETAIL
        foreach ($produk_id as $i => $idproduk) {

            $p = mysqli_fetch_assoc(mysqli_query(
                $koneksi,
                "SELECT harga FROM produk WHERE id='$idproduk'"
            ));

            $subtotal = $p['harga'] * $qty[$i];

            mysqli_query($koneksi, "
                INSERT INTO transaksidetail
                (transaksi_id, produk_id, jumlah, subtotal)
                VALUES
                ('$transaksi_id','$idproduk','$qty[$i]','$subtotal')
            ");
        }

        // UPLOAD BUKTI
        $folder = "../assets/uploads/bukti/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $bukti = "";

        if (!empty($_FILES['buktibayar']['name'])) {

            $bukti = time() . "_" . $_FILES['buktibayar']['name'];

            move_uploaded_file(
                $_FILES['buktibayar']['tmp_name'],
                $folder . $bukti
            );
        }

        if (!empty($bukti)) {

            // INSERT PEMBAYARAN
            mysqli_query($koneksi, "
            INSERT INTO pembayaran
            (transaksi_id, atasnama, bank, buktibayar, jumlah, tanggal)
            VALUES
            ('$transaksi_id','$atasnama','$bank','$bukti','$grandtotal', NOW())
            ");
        }

        echo "<script>alert('Transaksi berhasil');</script>";
        echo "<script>location='index.php?page=transaksi';</script>";
    }
}

$origin = 1391;
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <h4>Tambah Transaksi</h4>

            <form method="POST" enctype="multipart/form-data">

                <!-- CUSTOMER -->
                <div class="form-group">
                    <label>Customer</label>
                    <select name="customer_id" class="form-control select2" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $c = mysqli_query($koneksi, "SELECT * FROM users WHERE role='User'");
                        while ($u = mysqli_fetch_assoc($c)) { ?>
                            <option value="<?= $u['id'] ?>"><?= $u['nama'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- ALAMAT -->
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3"></textarea>
                </div>

                <!-- PROVINSI -->
                <div class="form-group">
                    <label>Provinsi</label>
                    <select name="provinsi" id="provinsi" class="form-control" required>
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                </div>

                <!-- KOTA -->
                <div class="form-group">
                    <label>Kota / Kabupaten</label>
                    <select name="kota" id="kota" class="form-control" required>
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <input type="hidden" name="destination_id" id="destination_id">

                <!-- KODE POS -->
                <div class="form-group">
                    <label>Kode Pos</label>
                    <input type="text" name="kodepos" id="kodepos" class="form-control">
                </div>

                <!-- KURIR -->
                <div class="form-group">
                    <label>Kurir</label>
                    <select name="kurir" id="kurir" class="form-control" required>
                        <option value="">-- Pilih Kurir --</option>
                        <option value="jne">JNE</option>
                        <option value="jnt">J&T</option>
                        <option value="sicepat">SiCepat</option>
                        <option value="tiki">TIKI</option>
                        <option value="anteraja">AnterAja</option>
                        <option value="pos">POS Indonesia</option>
                    </select>
                </div>

                <!-- ONGKIR -->
                <div class="form-group">
                    <label>Ongkos Kirim</label>
                    <input type="text" id="ongkir_text" class="form-control" readonly placeholder="Pilih kota & kurir dahulu">
                    <input type="hidden" name="ongkir" id="ongkir" value="0">
                </div>

                <!-- DESKRIPSI -->
                <div class="form-group">
                    <label>Catatan / Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"></textarea>
                </div>

                <!-- PRODUK -->
                <label>Produk</label>
                <table class="table table-bordered" id="tbl">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th width="100">Qty</th>
                            <th>Subtotal</th>
                            <th width="70">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="produk_id[]" class="form-control produk" required>
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $p = mysqli_query($koneksi, "SELECT * FROM produk");
                                    while ($r = mysqli_fetch_assoc($p)) { ?>
                                        <option value="<?= $r['id'] ?>" data-harga="<?= $r['harga'] ?>">
                                            <?= $r['namaproduk'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="harga">0</td>
                            <td>
                                <input type="number" name="qty[]" class="form-control qty" value="1" min="1">
                            </td>
                            <td class="subtotal">0</td>
                            <td>
                                <button type="button" class="btn btn-success btn-sm add">+</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- GRAND TOTAL -->
                <div class="form-group">
                    <label>Subtotal Produk</label>
                    <input type="text" id="subtotal_text" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Grand Total (+ Ongkir)</label>
                    <input type="text" id="grandtotal" class="form-control" readonly>
                </div>

                <h5>Pembayaran</h5>

                <!-- metode -->
                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="metode" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer">Transfer Bank</option>
                    </select>
                </div>

                <div class="form-pembayaram">
                    <div class="form-group">
                        <label>Atas Nama</label>
                        <input type="text" name="atasnama" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Bank</label>
                        <input type="text" name="bank" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Bukti Bayar</label>
                        <input type="file" name="buktibayar" class="form-control">
                    </div>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan
                </button>

            </form>

        </div>
    </div>
</div>

<script>
    // sembunyikan form pembayaran jika metode tunai
    document.querySelector(".form-pembayaram").style.display = "none";
    // jika metode nya Trnsfer tampilkan form pembayaran
    document.querySelector("select[name=metode]").addEventListener("change", function() {
        if (this.value === "Transfer") {
            document.querySelector(".form-pembayaram").style.display = "block";
        } else {
            document.querySelector(".form-pembayaram").style.display = "none";
        }
    });

    const ORIGIN = <?= $origin ?>;

    // ── HITUNG SUBTOTAL & GRAND TOTAL ─────────────────────────
    function hitung() {

        let subtotal = 0;

        document.querySelectorAll("#tbl tbody tr").forEach(row => {

            let produk = row.querySelector(".produk");
            let qty = row.querySelector(".qty").value;
            let harga = produk.options[produk.selectedIndex]?.dataset?.harga || 0;
            let sub = harga * qty;

            row.querySelector(".harga").innerHTML = parseInt(harga).toLocaleString('id-ID');
            row.querySelector(".subtotal").innerHTML = parseInt(sub).toLocaleString('id-ID');

            subtotal += parseInt(sub);
        });

        let ongkir = parseInt(document.getElementById('ongkir').value) || 0;

        document.getElementById('subtotal_text').value =
            'Rp ' + subtotal.toLocaleString('id-ID');

        document.getElementById('grandtotal').value =
            'Rp ' + (subtotal + ongkir).toLocaleString('id-ID');
    }

    // ── LOAD PROVINSI ──────────────────────────────────────────
    fetch('../api/provinsi.php')
        .then(res => res.json())
        .then(result => {

            let sel = document.getElementById('provinsi');

            sel.innerHTML = '<option value="">-- Pilih Provinsi --</option>';

            result.data.forEach(item => {
                sel.innerHTML +=
                    `<option value="${item.name}" data-provinsi="${item.id}">${item.name}</option>`;
            });
        });

    // ── LOAD KOTA ──────────────────────────────────────────────
    document.getElementById('provinsi').addEventListener('change', function() {

        let provinceId = this.options[this.selectedIndex].getAttribute('data-provinsi');

        fetch('../api/kota.php?id=' + provinceId)
            .then(res => res.json())
            .then(result => {

                let sel = document.getElementById('kota');

                sel.innerHTML = '<option value="">-- Pilih Kota --</option>';

                result.data.forEach(item => {
                    sel.innerHTML +=
                        `<option value="${item.name}" data-id="${item.id}" data-kodepos="${item.zip_code}">
                            ${item.name}
                        </option>`;
                });
            });
    });

    // ── PILIH KOTA ─────────────────────────────────────────────
    document.getElementById('kota').addEventListener('change', function() {

        let selected = this.options[this.selectedIndex];
        let destinationId = selected.getAttribute('data-id');
        let kodepos = selected.getAttribute('data-kodepos');

        document.getElementById('destination_id').value = destinationId;
        // Uncomment baris ini jika ingin kode pos terisi otomatis:
        // document.getElementById('kodepos').value = kodepos;

        hitungOngkir();
    });

    // ── PILIH KURIR ────────────────────────────────────────────
    document.getElementById('kurir').addEventListener('change', hitungOngkir);

    // ── HITUNG ONGKIR VIA RAJAONGKIR ──────────────────────────
    function hitungOngkir() {

        let destination = document.getElementById('destination_id').value;
        let courier = document.getElementById('kurir').value;

        if (!destination || !courier) return;

        // Hitung total berat: asumsikan 1000 gram per baris produk × qty
        let totalBerat = 0;

        document.querySelectorAll("#tbl tbody tr").forEach(row => {
            let qty = parseInt(row.querySelector(".qty").value) || 1;
            totalBerat += 1000 * qty;
        });

        let formData = new FormData();
        formData.append('origin', ORIGIN);
        formData.append('destination', destination);
        formData.append('weight', totalBerat);
        formData.append('courier', courier);
        formData.append('price', 'lowest');

        document.getElementById('ongkir_text').value = 'Menghitung...';

        fetch('../api/ongkir.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {

                let ongkir = 0;

                if (result.data && result.data.length > 0) {
                    ongkir = result.data[0].cost;
                }

                document.getElementById('ongkir').value = ongkir;
                document.getElementById('ongkir_text').value = 'Rp ' + ongkir.toLocaleString('id-ID');

                hitung(); // update grand total setelah ongkir berubah
            })
            .catch(() => {
                document.getElementById('ongkir_text').value = 'Gagal mengambil ongkir';
            });
    }

    // ── TAMBAH / HAPUS BARIS PRODUK ───────────────────────────
    document.addEventListener("change", function(e) {
        if (e.target.classList.contains("produk") ||
            e.target.classList.contains("qty")) {
            hitung();
            hitungOngkir(); // update berat & ongkir jika qty berubah
        }
    });

    document.addEventListener("click", function(e) {

        if (e.target.classList.contains("add")) {

            let row = document.querySelector("#tbl tbody tr").cloneNode(true);

            row.querySelector(".produk").selectedIndex = 0;
            row.querySelector(".qty").value = 1;
            row.querySelector(".harga").innerHTML = 0;
            row.querySelector(".subtotal").innerHTML = 0;

            let btn = row.querySelector("button");
            btn.classList.replace("add", "remove");
            btn.classList.replace("btn-success", "btn-danger");
            btn.innerHTML = "-";

            document.querySelector("#tbl tbody").appendChild(row);
        }

        if (e.target.classList.contains("remove")) {
            e.target.closest("tr").remove();
            hitung();
            hitungOngkir();
        }
    });
</script>