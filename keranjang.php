<?php include 'header.php'; ?>

<?php

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// HAPUS ITEM
if (isset($_GET['hapus'])) {

    $hapus = intval($_GET['hapus']);

    unset($_SESSION['keranjang'][$hapus]);

    echo "
        <script>
            alert('Produk dihapus dari keranjang');
            location='keranjang.php';
        </script>
    ";
}

// UPDATE QTY
if (isset($_POST['update'])) {

    foreach ($_POST['qty'] as $id => $qty) {

        $id  = intval($id);
        $qty = intval($qty);

        if ($qty <= 0) {

            unset($_SESSION['keranjang'][$id]);
        } else {

            $_SESSION['keranjang'][$id] = $qty;
        }
    }

    echo "
        <script>
            alert('Keranjang berhasil diupdate');
            location='keranjang.php';
        </script>
    ";
}

$keranjang = $_SESSION['keranjang'];

?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4 class="fw-bold mb-0">
            Keranjang
        </h4>

        <span class="badge bg-primary px-3 py-2">
            <?= count($keranjang) ?> Item
        </span>

    </div>

    <?php if (count($keranjang) > 0) { ?>

        <form method="POST">

            <div class="row g-3">

                <?php

                $grandtotal = 0;

                foreach ($keranjang as $id => $qty) {

                    $id = intval($id);

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
                        continue;
                    }

                    $subtotal = $produk['harga'] * $qty;

                    $grandtotal += $subtotal;

                ?>

                    <div class="col-12">

                        <div class="card border-0 shadow-sm rounded-4">

                            <div class="card-body">

                                <div class="row align-items-center">

                                    <!-- FOTO -->
                                    <div class="col-4 col-md-2">

                                        <?php if ($produk['foto']) { ?>

                                            <img src="assets/uploads/produk/<?= $produk['foto'] ?>"
                                                class="img-fluid rounded-4"
                                                style="height:100px; width:100%; object-fit:cover;">

                                        <?php } else { ?>

                                            <img src="https://via.placeholder.com/300x300"
                                                class="img-fluid rounded-4">

                                        <?php } ?>

                                    </div>

                                    <!-- DETAIL -->
                                    <div class="col-8 col-md-5">

                                        <span class="badge bg-light text-dark mb-2">

                                            <?= $produk['namakategori'] ?>

                                        </span>

                                        <h5 class="fw-bold mb-1">

                                            <?= $produk['namaproduk'] ?>

                                        </h5>

                                        <p class="text-muted small mb-2">

                                            <?= substr($produk['deskripsi'], 0, 80) ?>...

                                        </p>

                                        <h6 class="fw-bold text-primary mb-0">

                                            Rp <?= number_format($produk['harga']) ?>

                                        </h6>

                                    </div>

                                    <!-- QTY -->
                                    <div class="col-6 col-md-2 mt-3 mt-md-0">

                                        <label class="small text-muted">
                                            Jumlah
                                        </label>

                                        <input type="number"
                                            name="qty[<?= $produk['id'] ?>]"
                                            value="<?= $qty ?>"
                                            min="1"
                                            class="form-control qty-input">

                                    </div>

                                    <!-- SUBTOTAL -->
                                    <div class="col-6 col-md-2 mt-3 mt-md-0">

                                        <label class="small text-muted">
                                            Subtotal
                                        </label>

                                        <h6 class="fw-bold text-primary">

                                            Rp <?= number_format($subtotal) ?>

                                        </h6>

                                    </div>

                                    <!-- AKSI -->
                                    <div class="col-12 col-md-1 text-md-end mt-3 mt-md-0">

                                        <a href="keranjang.php?hapus=<?= $produk['id'] ?>"
                                            class="btn btn-danger btn-sm rounded-pill"
                                            onclick="return confirm('Hapus produk ini?')">

                                            <i class="bi bi-trash"></i>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

            <!-- TOTAL -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="fw-bold mb-0">
                            Grand Total
                        </h5>

                        <h4 class="fw-bold text-primary mb-0">

                            Rp <?= number_format($grandtotal) ?>

                        </h4>

                    </div>

                    <div class="d-flex gap-2">

                        <button type="submit"
                            name="update"
                            id="btnUpdate"
                            class="btn btn-outline-primary w-50 rounded-pill py-2 fw-bold d-none">

                            Update Keranjang
                        </button>

                        <a href="checkout.php"
                            class="btn btn-primary w-50 rounded-pill py-2 fw-bold">

                            Checkout
                        </a>

                    </div>

                </div>

            </div>

        </form>

    <?php } else { ?>

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body text-center py-5">

                <i class="bi bi-cart-x display-3 text-muted"></i>

                <h5 class="fw-bold mt-3">
                    Keranjang Kosong
                </h5>

                <p class="text-muted">
                    Belum ada produk di keranjang
                </p>

                <a href="index.php"
                    class="btn btn-primary rounded-pill px-4">

                    Belanja Sekarang
                </a>

            </div>

        </div>

    <?php } ?>

</div>

<script>
    const qtyInputs = document.querySelectorAll('.qty-input');
    const btnUpdate = document.getElementById('btnUpdate');

    qtyInputs.forEach(input => {

        input.addEventListener('input', function() {

            btnUpdate.classList.remove('d-none');

        });

    });
</script>

<?php include 'footer.php'; ?>