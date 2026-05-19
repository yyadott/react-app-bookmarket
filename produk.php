<?php include 'header.php'; ?>

<?php

// AMBIL KATEGORI
$kategori = mysqli_query($koneksi, "
    SELECT *
    FROM kategori
    ORDER BY namakategori ASC
");

// FILTER
$where = "";

// FILTER KATEGORI
if (isset($_GET['kategori'])) {

    $kategori_id = intval($_GET['kategori']);

    $where = "WHERE p.kategori_id='$kategori_id'";
}

// SEARCH
if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $koneksi,
        $_GET['search']
    );

    $where .= $where
        ? " AND p.namaproduk LIKE '%$search%'"
        : "WHERE p.namaproduk LIKE '%$search%'";
}

// PRODUK
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

<div class="container py-4">

    <!-- HEADER -->
    <div class="mb-4">

        <h3 class="fw-bold mb-1">
            Semua Produk
        </h3>

        <p class="text-muted mb-0">
            Temukan menu catering favorit Anda
        </p>

    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-4">

        <div class="input-group shadow-sm rounded-4 overflow-hidden">

            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-muted"></i>
            </span>

            <input type="text"
                name="search"
                class="form-control border-0 py-3"
                placeholder="Cari produk..."
                value="<?= $_GET['search'] ?? '' ?>">

            <?php if (isset($_GET['kategori'])) { ?>

                <input type="hidden"
                    name="kategori"
                    value="<?= $_GET['kategori'] ?>">

            <?php } ?>

            <button class="btn btn-primary px-4">
                Cari
            </button>

        </div>

    </form>

    <!-- KATEGORI -->
    <div class="d-flex overflow-auto pb-3 gap-2 no-scrollbar mb-4">

        <a href="produk.php"
            class="btn <?= !isset($_GET['kategori']) ? 'btn-primary text-white' : 'btn-outline-secondary' ?> rounded-pill px-4 text-nowrap">

            Semua

        </a>

        <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>

            <a href="produk.php?kategori=<?= $k['id'] ?>"
                class="btn <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'btn-primary text-white' : 'btn-outline-secondary' ?> rounded-pill px-4 text-nowrap">

                <?= $k['namakategori'] ?>

            </a>

        <?php } ?>

    </div>

    <!-- PRODUK -->
    <div class="row g-3">

        <?php if (mysqli_num_rows($produk) > 0) { ?>

            <?php while ($p = mysqli_fetch_assoc($produk)) { ?>

                <div class="col-6 col-md-4 col-lg-3">

                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">

                        <div class="position-relative">

                            <?php if ($p['foto']) { ?>

                                <img src="assets/uploads/produk/<?= $p['foto'] ?>"
                                    class="card-img-top"
                                    style="height:220px; object-fit:cover;">

                            <?php } else { ?>

                                <img src="https://via.placeholder.com/300x300"
                                    class="card-img-top">

                            <?php } ?>

                            <span class="position-absolute top-0 start-0 m-2 badge bg-primary rounded-pill px-3 py-2">

                                <?= $p['namakategori'] ?>

                            </span>

                        </div>

                        <div class="card-body d-flex flex-column">

                            <h6 class="fw-bold mb-1">

                                <?= $p['namaproduk'] ?>

                            </h6>

                            <p class="text-muted small mb-3 flex-grow-1">

                                <?= substr($p['deskripsi'], 0, 70) ?>...

                            </p>

                            <div class="d-flex justify-content-between align-items-center">

                                <div>

                                    <small class="text-muted">
                                        Harga
                                    </small>

                                    <h6 class="fw-bold text-primary mb-0">

                                        Rp <?= number_format($p['harga']) ?>

                                    </h6>

                                </div>

                                <a href="produkdetail.php?id=<?= $p['id'] ?>"
                                    class="btn btn-primary btn-sm rounded-pill px-3">

                                    Detail

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="col-12">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-body text-center py-5">

                        <i class="bi bi-bag-x display-3 text-muted"></i>

                        <h5 class="fw-bold mt-3">
                            Produk Tidak Ditemukan
                        </h5>

                        <p class="text-muted mb-4">
                            Produk yang Anda cari belum tersedia
                        </p>

                        <a href="produk.php"
                            class="btn btn-primary rounded-pill px-4">

                            Lihat Semua Produk

                        </a>

                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

<?php include 'footer.php'; ?>