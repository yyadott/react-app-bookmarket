<?php
@session_start();

include 'koneksi.php';

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --bs-primary: #A65D37;
            --bs-primary-rgb: 166, 93, 55;
        }

        /* BUTTON */
        .btn-primary {
            background-color: #A65D37 !important;
            border-color: #A65D37 !important;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #8d4d2d !important;
            border-color: #8d4d2d !important;
        }

        /* BG */
        .bg-primary {
            background-color: #A65D37 !important;
        }

        /* TEXT */
        .text-primary {
            color: #A65D37 !important;
        }

        /* BORDER */
        .border-primary {
            border-color: #A65D37 !important;
        }

        /* OUTLINE BUTTON */
        .btn-outline-primary {
            color: #A65D37 !important;
            border-color: #A65D37 !important;
        }

        .btn-outline-primary:hover {
            background-color: #A65D37 !important;
            color: white !important;
        }

        /* FORM */
        .form-control:focus,
        .form-select:focus {
            border-color: #A65D37 !important;
            box-shadow: 0 0 0 .25rem rgba(166, 93, 55, .25) !important;
        }

        /* LINK */
        a {
            color: #A65D37;
        }

        a:hover {
            color: #8d4d2d;
        }

        /* PAGINATION */
        .page-item.active .page-link {
            background-color: #A65D37 !important;
            border-color: #A65D37 !important;
        }

        .page-link {
            color: #A65D37;
        }

        /* CHECKBOX & RADIO */
        .form-check-input:checked {
            background-color: #A65D37 !important;
            border-color: #A65D37 !important;
        }

        .navbar-light .navbar-nav .nav-link:hover,
        .navbar-light .navbar-nav .nav-link.active {
            color: #FFFFFF;
            background: #A65D37;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-light bg-white sticky-top border-bottom">
        <div class="container d-flex justify-content-between align-items-center">

            <div>
                <small class="text-muted d-block" style="font-size: 0.7rem;">
                    Lokasi
                </small>

                <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle p-0 fw-bold" type="button">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        Palembang, IDN
                    </button>
                </div>
            </div>

            <div>

                <?php
                $totalKeranjang = 0;

                if (isset($_SESSION['keranjang'])) {

                    foreach ($_SESSION['keranjang'] as $qty) {
                        $totalKeranjang += $qty;
                    }
                }
                ?>

                <?php if (!isset($_SESSION['user'])): ?>

                    <a href="login.php"
                        class="btn btn-light rounded-circle">

                        <i class="bi bi-person"></i>

                    </a>

                <?php else: ?>

                    <!-- KERANJANG -->
                    <a href="keranjang.php"
                        class="btn btn-light rounded-circle me-2 position-relative">

                        <i class="bi bi-cart"></i>

                        <?php if ($totalKeranjang > 0): ?>

                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="font-size:10px;">

                                <?= $totalKeranjang ?>

                            </span>

                        <?php endif; ?>

                    </a>

                    <!-- AKUN -->
                    <a href="akun.php"
                        class="btn btn-light rounded-circle">

                        <i class="bi bi-person-fill"></i>

                    </a>

                <?php endif; ?>

            </div>

        </div>
    </nav>