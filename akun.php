<?php include 'header.php'; ?>

<div class="container py-4 mb-5">

    <h4 class="fw-bold mb-4">
        Akun Saya
    </h4>

    <?php if (isset($_SESSION['user'])) { ?>

        <?php
        $user = $_SESSION['user'];
        ?>

        <!-- PROFILE CARD -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">

            <div class="card-body p-4">

                <div class="d-flex align-items-center">

                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width:70px; height:70px; font-size:28px;">

                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>

                    </div>

                    <div class="ms-3">

                        <h5 class="fw-bold mb-1">
                            <?= $user['nama'] ?>
                        </h5>

                        <p class="text-muted mb-1">
                            <?= $user['email'] ?>
                        </p>

                        <small class="text-muted">
                            <?= $user['nohp'] ?>
                        </small>

                    </div>

                </div>

            </div>

        </div>

        <!-- MENU -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <a href="profile.php"
                class="text-decoration-none text-dark border-bottom">

                <div class="d-flex justify-content-between align-items-center p-3">

                    <div class="d-flex align-items-center">

                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width:45px; height:45px;">

                            <i class="bi bi-person text-primary"></i>

                        </div>

                        <div>

                            <h6 class="fw-bold mb-0">
                                Profile
                            </h6>

                            <small class="text-muted">
                                Lihat dan edit profile
                            </small>

                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </a>

            <a href="riwayat.php"
                class="text-decoration-none text-dark border-bottom">

                <div class="d-flex justify-content-between align-items-center p-3">

                    <div class="d-flex align-items-center">

                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width:45px; height:45px;">

                            <i class="bi bi-receipt text-primary"></i>

                        </div>

                        <div>

                            <h6 class="fw-bold mb-0">
                                Riwayat Pesanan
                            </h6>

                            <small class="text-muted">
                                Lihat transaksi dan status pesanan
                            </small>

                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </a>

            <a href="logout.php"
                onclick="return confirm('Yakin ingin logout?')"
                class="text-decoration-none text-danger">

                <div class="d-flex justify-content-between align-items-center p-3">

                    <div class="d-flex align-items-center">

                        <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width:45px; height:45px;">

                            <i class="bi bi-box-arrow-right text-danger"></i>

                        </div>

                        <div>

                            <h6 class="fw-bold mb-0 text-danger">
                                Logout
                            </h6>

                            <small class="text-muted">
                                Keluar dari akun
                            </small>

                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </a>

        </div>

    <?php } else { ?>

        <!-- BELUM LOGIN -->
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body text-center py-5">

                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width:90px; height:90px;">

                    <i class="bi bi-person display-5 text-primary"></i>

                </div>

                <h5 class="fw-bold mb-2">
                    Belum Login
                </h5>

                <p class="text-muted mb-4">
                    Silahkan login terlebih dahulu untuk melihat akun dan riwayat pesanan
                </p>

                <div class="d-grid gap-2">

                    <a href="login.php"
                        class="btn btn-primary rounded-pill py-2 fw-bold">

                        Login
                    </a>

                    <a href="register.php"
                        class="btn btn-outline-primary rounded-pill py-2 fw-bold">

                        Daftar Akun
                    </a>

                </div>

            </div>

        </div>

    <?php } ?>

</div>

<?php include 'footer.php'; ?>