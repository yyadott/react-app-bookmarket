<?php include 'header.php'; ?>

<?php

$kategori = mysqli_query($koneksi, "
    SELECT * FROM kategori
");

$where = "";

if (isset($_GET['kategori'])) {

    $kategori_id = intval($_GET['kategori']);

    $where = "WHERE kategori_id='$kategori_id'";
}

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $koneksi,
        $_GET['search']
    );

    $where .= $where
        ? " AND namaproduk LIKE '%$search%'"
        : "WHERE namaproduk LIKE '%$search%'";
}

$produk = mysqli_query($koneksi, "
    SELECT
        p.*,
        k.namakategori
    FROM produk p
    LEFT JOIN kategori k
    ON p.kategori_id = k.id
    $where
    ORDER BY p.id DESC
");

?>

<div class="container py-3">

    <!-- BANNER -->
    <div class="card border-0 text-white mb-4 overflow-hidden bg-warning"
        style="border-radius: 20px;">

        <div class="card-body d-flex align-items-center justify-content-between p-4">

            <div>
                <h2 class="fw-bold mb-1 text-dark">
                    Book Market
                </h2>

                <p class="mb-3 text-dark">
                    <!-- untuk book market -->
                    Selamat datang di Book Market, tempat terbaik untuk menemukan buku-buku berkualitas dengan harga terjangkau. Temukan berbagai genre dan penawaran menarik hanya di sini!
                </p>

                <a href="produk.php"
                    class="btn btn-light fw-bold rounded-pill px-4">
                    Pesan Sekarang
                </a>
            </div>

            <i class="bi bi-truck display-1 opacity-25 d-none d-sm-block"></i>

        </div>

    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-4">

        <div class="input-group shadow-sm rounded">

            <input type="hidden" name="page" value="home">

            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>

            <input type="text"
                name="search"
                class="form-control border-start-0 py-2"
                placeholder="Cari produk di sini..."
                value="<?= $_GET['search'] ?? '' ?>">

            <button class="btn btn-primary">
                Cari
            </button>

        </div>

    </form>

    <!-- KATEGORI -->
    <h5 class="fw-bold mb-3">
        Category
    </h5>

    <div class="d-flex overflow-auto pb-3 gap-2 no-scrollbar mb-4">

        <a href="index.php?page=home"
            class="btn <?= !isset($_GET['kategori']) ? 'btn-primary text-white' : 'btn-outline-secondary' ?> rounded-3 px-4">

            All
        </a>

        <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>

            <a href="index.php?page=home&kategori=<?= $k['id'] ?>"
                class="btn <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'btn-primary text-white' : 'btn-outline-secondary' ?> rounded-3 px-4 text-nowrap">

                <?= $k['namakategori'] ?>

            </a>

        <?php } ?>

    </div>

    <!-- PRODUK -->
    <div class="row g-3" id="produk">

        <?php if (mysqli_num_rows($produk) > 0) { ?>

            <?php while ($p = mysqli_fetch_assoc($produk)) { ?>

                <div class="col-6 col-md-4 col-lg-3">

                    <div class="card h-100 border-0 bg-transparent shadow-sm">

                        <div class="position-relative">

                            <?php if ($p['foto']) { ?>

                                <img src="assets/uploads/produk/<?= $p['foto'] ?>"
                                    class="card-img-top rounded-4"
                                    style="height:220px; object-fit:cover;">

                            <?php } else { ?>

                                <img src="https://via.placeholder.com/300x300"
                                    class="card-img-top rounded-4">

                            <?php } ?>

                            <span class="position-absolute top-0 start-0 m-2 badge bg-primary">

                                <?= $p['namakategori'] ?>

                            </span>

                        </div>

                        <div class="card-body px-1">

                            <h6 class="fw-bold mb-1">

                                <?= $p['namaproduk'] ?>

                            </h6>

                            <p class="text-muted small mb-2">

                                <?= substr($p['deskripsi'], 0, 60) ?>...

                            </p>

                            <div class="d-flex justify-content-between align-items-center">

                                <p class="fw-bold text-primary mb-0">

                                    Rp <?= number_format($p['harga']) ?>

                                </p>

                                <a href="produkdetail.php?id=<?= $p['id'] ?>"
                                    class="btn btn-primary btn-sm rounded-pill">

                                    Detail
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="col-12">

                <div class="alert alert-warning">

                    Produk tidak ditemukan

                </div>

            </div>

        <?php } ?>

    </div>

</div>

<?php include 'footer.php'; ?>