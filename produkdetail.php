<?php include 'header.php'; ?>

<?php

$id = intval($_GET['id']);

$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT
        p.*,
        k.namakategori
    FROM produk p
    LEFT JOIN kategori k
    ON p.kategori_id = k.id
    WHERE p.id='$id'
"));

if (!$produk) {

    echo "
        <script>
            alert('Produk tidak ditemukan');
            location='index.php';
        </script>
    ";

    exit;
}

// PRODUK LAINNYA
$lainnya = mysqli_query($koneksi, "
    SELECT *
    FROM produk
    WHERE id != '$id'
    ORDER BY RAND()
    LIMIT 4
");

?>

<div class="container py-3 pb-5">

    <!-- DETAIL PRODUK -->
    <div class="row g-4">

        <!-- FOTO -->
        <div class="col-12 col-md-5">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <?php if ($produk['foto']) { ?>

                    <img src="assets/uploads/produk/<?= $produk['foto'] ?>"
                        class="img-fluid w-100"
                        style="height:350px; object-fit:cover;">

                <?php } else { ?>

                    <img src="https://via.placeholder.com/600x600"
                        class="img-fluid w-100"
                        style="height:350px; object-fit:cover;">

                <?php } ?>

            </div>

        </div>

        <!-- DETAIL -->
        <div class="col-12 col-md-7">

            <span class="badge bg-primary px-3 py-2 mb-3 rounded-pill">

                <?= $produk['namakategori'] ?>

            </span>

            <h3 class="fw-bold mb-2">

                <?= $produk['namaproduk'] ?>

            </h3>

            <h4 class="text-primary fw-bold mb-3">

                Rp <?= number_format($produk['harga']) ?>

            </h4>

            <h5 class="text-primary fw-bold mb-3">

                Stok: <?= $produk['stok'] ?>

            </h5>

            <!-- DESKRIPSI -->
            <div class="card border-0 bg-light rounded-4 mb-4">

                <div class="card-body">

                    <h6 class="fw-bold mb-2">
                        Deskripsi
                    </h6>

                    <p class="text-muted mb-0 small lh-lg">

                        <?= nl2br($produk['deskripsi']) ?>

                    </p>

                </div>

            </div>

            <!-- QTY -->
            <div class="mb-4">

                <label class="fw-bold mb-2 d-block">
                    Jumlah Pesanan
                </label>

                <div class="d-flex align-items-center">

                    <button type="button"
                        class="btn btn-outline-secondary rounded-circle"
                        style="width:40px; height:40px;"
                        onclick="kurangQty()">

                        <i class="bi bi-dash"></i>

                    </button>

                    <input type="number"
                        id="qty"
                        value="1"
                        min="1"
                        class="form-control text-center mx-2"
                        style="max-width:80px;"
                        onchange="updateTotal()">

                    <button type="button"
                        class="btn btn-outline-secondary rounded-circle"
                        style="width:40px; height:40px;"
                        onclick="tambahQty()">

                        <i class="bi bi-plus"></i>

                    </button>

                </div>

            </div>

            <!-- TOTAL -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted d-block">
                            Total Harga
                        </small>

                        <h4 class="fw-bold text-primary mb-0">

                            Rp <span id="totalharga">
                                <?= number_format($produk['harga']) ?>
                            </span>

                        </h4>
                    </div>

                    <button onclick="pesanProduk()"
                        class="btn btn-primary rounded-pill px-4 py-2 fw-bold">

                        <i class="bi bi-cart-plus me-1"></i>
                        Tambah
                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- PRODUK LAIN -->
    <div class="mt-5">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h5 class="fw-bold mb-0">
                Produk Lainnya
            </h5>

        </div>

        <div class="row g-3">

            <?php while ($p = mysqli_fetch_assoc($lainnya)) { ?>

                <div class="col-6 col-md-3">

                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">

                        <?php if ($p['foto']) { ?>

                            <img src="assets/uploads/produk/<?= $p['foto'] ?>"
                                class="card-img-top"
                                style="height:170px; object-fit:cover;">

                        <?php } else { ?>

                            <img src="https://via.placeholder.com/300x300"
                                class="card-img-top"
                                style="height:170px; object-fit:cover;">

                        <?php } ?>

                        <div class="card-body d-flex flex-column">

                            <small class="text-muted mb-1">

                                <?= $p['namakategori'] ?? 'Produk' ?>

                            </small>

                            <h6 class="fw-bold mb-2"
                                style="font-size:0.95rem;">

                                <?= $p['namaproduk'] ?>

                            </h6>

                            <p class="fw-bold text-primary mb-3">

                                Rp <?= number_format($p['harga']) ?>

                            </p>

                            <a href="produkdetail.php?id=<?= $p['id'] ?>"
                                class="btn btn-primary btn-sm rounded-pill mt-auto">

                                Detail
                            </a>

                        </div>

                    </div>

                </div>

            <?php } ?>

        </div>

    </div>

</div>

<script>
    let harga = <?= $produk['harga'] ?>;

    function updateTotal() {

        let qty = document.getElementById("qty").value;

        if (qty < 1 || qty == "") {
            qty = 1;
            document.getElementById("qty").value = 1;
        }

        let total = harga * qty;

        document.getElementById("totalharga").innerHTML =
            total.toLocaleString('id-ID');
    }

    function tambahQty() {

        let qty = document.getElementById("qty");

        qty.value = parseInt(qty.value) + 1;

        updateTotal();
    }

    function kurangQty() {

        let qty = document.getElementById("qty");

        if (parseInt(qty.value) > 1) {

            qty.value = parseInt(qty.value) - 1;

            updateTotal();
        }
    }

    function pesanProduk() {

        let qty = document.getElementById("qty").value;

        window.location =
            "keranjangtambah.php?id=<?= $produk['id'] ?>&qty=" + qty;
    }
</script>

<?php include 'footer.php'; ?>