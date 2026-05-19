<?php
session_start();
require_once '../koneksi.php';
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silahkan login terlebih dahulu'); window.location='../login.php';</script>";
    exit;
}

function limit_text($html, $limit = 120)
{
    $text = strip_tags($html);

    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    if (strlen($text) <= $limit) {
        return $text;
    }

    return substr($text, 0, $limit) . '...';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Book Market</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Pignose Calender -->
    <link href="../assets/panel/plugins/pg-calendar/css/pignose.calendar.min.css" rel="stylesheet">
    <!-- Chartist -->
    <link rel="stylesheet" href="../assets/panel/plugins/chartist/css/chartist.min.css">
    <link rel="stylesheet" href="../assets/panel/plugins/chartist-plugin-tooltips/css/chartist-plugin-tooltip.css">
    <!-- Custom Stylesheet -->
    <link href="../assets/panel/css/style.css" rel="stylesheet">

    <link href="../assets/panel/plugins/tables/css/datatable/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- <style>
        [data-nav-headerbg="color_1"] .nav-header {
            background-color: #0069d1;
        }
    </style> -->


</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <div class="brand-logo">
                <a href="index.php?page=dashboard" class="d-flex align-items-center gap-2">

                    <!-- Logo (hanya desktop) -->
                    <!-- <img src="../assets/foto/logo.jpeg"
                        alt="logo"
                        class="img-fluid rounded-circle d-none d-md-block"
                        style="height:45px; width:45px; object-fit:cover;"> -->

                    <!-- Text -->
                    <div class="d-flex flex-column text-white d-none d-md-block">
                        <span class="fw-bold ml-2 d-none d-md-block text-dark">Book Market</span>
                    </div>

                </a>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content clearfix">

                <!-- HAMBURGER -->
                <div class="nav-control">
                    <div class="hamburger" id="menu-toggle">
                        <span class="toggle-icon">
                            <i class="fa fa-bars"></i>
                        </span>
                    </div>
                </div>

                <!-- RIGHT HEADER -->
                <div class="header-right">
                    <ul class="clearfix">

                        <li class="icons dropdown">
                            <div class="user-img c-pointer position-relative" data-toggle="dropdown">
                                <span class="activity active"></span>
                                <img src="../assets/panel/images/user/1.png"
                                    height="40"
                                    width="40"
                                    alt="">
                            </div>

                            <div class="drop-down dropdown-profile animated fadeIn dropdown-menu">
                                <div class="dropdown-content-body">
                                    <ul>
                                        <li>
                                            <a href="index.php?page=profile">
                                                <i class="icon-user"></i> <span>Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="../logout.php"
                                                onclick="return confirm('Yakin ingin logout?')">
                                                <i class="icon-key"></i> <span>Logout</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>

            </div>
        </div>

        <style>
            .nav-control {
                display: block !important;
            }

            .hamburger {
                cursor: pointer;
                padding: 10px;
            }

            @media (max-width: 768px) {
                .header .nav-control {
                    display: block !important;
                }
            }
        </style>

        <style>
            .nk-sidebar {
                background: #eaf3ff;
            }

            .nk-sidebar .metismenu {
                background: transparent;
                padding: 10px 0;
            }

            .nk-sidebar .metismenu a {
                color: #2c3e50;
                padding: 10px 15px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                transition: all 0.3s ease;
                margin: 4px 10px;
            }

            .nk-sidebar .metismenu a i {
                color: #4a90e2;
                margin-right: 10px;
                transition: all 0.3s ease;
            }

            .nk-sidebar .metismenu a:hover {
                background: #d9ebff;
                color: #1d4ed8;
            }

            .nk-sidebar .metismenu a:hover i {
                color: #1d4ed8;
                transform: scale(1.05);
            }

            .nk-sidebar .metismenu a.active {
                background: linear-gradient(90deg, #4a90e2, #6fb1ff);
                color: #ffffff !important;
                font-weight: 600;
                box-shadow: 0 4px 10px rgba(74, 144, 226, 0.25);
            }

            .nk-sidebar .metismenu a.active i {
                color: black !important;
                transform: scale(1.15);
                text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
            }

            .nk-sidebar .nav-label {
                color: #6b7c93;
                font-size: 12px;
                letter-spacing: 1px;
                padding: 15px 15px 5px;
                text-transform: uppercase;
            }

            [data-sibebarbg="color_1"] .nk-sidebar .metismenu {
                background: #eaf3ff;
            }

            [data-nav-headerbg="color_1"] .nav-header {
                background: #eaf3ff;
            }
        </style>

        <div class="nk-sidebar">
            <div class="nk-nav-scroll">
                <ul class="metismenu" id="menu">
                    <!-- <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-speedometer menu-icon"></i><span class="nav-text">Dashboard</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="../assets/panel/index.html">Home 1</a></li>
                        </ul>
                    </li> -->

                    <?php
                    $page = $_GET['page'] ?? 'dashboard';

                    function isActive($pages, $current)
                    {
                        if (is_array($pages)) {
                            return in_array($current, $pages) ? 'active' : '';
                        }
                        return ($current === $pages) ? 'active' : '';
                    }
                    ?>

                    <li class="nav-label">Dashboard</li>
                    <li>
                        <a href="index.php?page=dashboard" aria-expanded="false" class="<?= isActive(['dashboard', 'index'], $page) ?>">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Dashboard</span>
                        </a>
                    </li>


                    <li class="nav-label">Master Data</li>

                    <li>
                        <a href="index.php?page=kategori" class="<?= isActive(['kategori', 'kategoriedit'], $page) ?>">
                            <i class="icon-wrench menu-icon"></i>
                            <span class="nav-text">Kategori</span>
                        </a>
                    </li>

                    <li>
                        <a href="index.php?page=produk" class="<?= isActive(['produk', 'produkedit'], $page) ?>">
                            <i class="icon-list menu-icon"></i>
                            <span class="nav-text">Produk</span>
                        </a>
                    </li>

                    <li>
                        <a href="index.php?page=transaksi" class="<?= isActive(['transaksi', 'transaksitambah', 'transaksidetail'], $page) ?>">
                            <i class="icon-basket menu-icon"></i>
                            <span class="nav-text">Daftar Pemesanan</span>
                        </a>
                    </li>

                    <li>
                        <a href="index.php?page=pengguna" class="<?= isActive(['pengguna', 'penggunaedit'], $page) ?>">
                            <i class="icon-user menu-icon"></i>
                            <span class="nav-text">Pengguna</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">

            <?php
            if (isset($_GET['page'])) {

                switch ($_GET['page']) {
                    case 'dashboard':
                        include 'dashboard.php';
                        break;

                    case 'kategori':
                        include 'kategori.php';
                        break;

                    case 'kategoriedit':
                        include 'kategoriedit.php';
                        break;

                    case 'kategorihapus':
                        include 'kategorihapus.php';
                        break;

                    case 'pengguna':
                        include 'pengguna.php';
                        break;

                    case 'penggunaedit':
                        include 'penggunaedit.php';
                        break;

                    case 'penggunahapus':
                        include 'penggunahapus.php';
                        break;

                    case 'produk':
                        include 'produk.php';
                        break;

                    case 'produkedit':
                        include 'produkedit.php';
                        break;

                    case 'produkhapus':
                        include 'produkhapus.php';
                        break;

                    case 'transaksi':
                        include 'transaksi.php';
                        break;

                    case 'transaksitambah':
                        include 'transaksitambah.php';
                        break;

                    case 'transaksidetail':
                        include 'transaksidetail.php';
                        break;

                    case 'transaksihapus':
                        include 'transaksihapus.php';
                        break;

                    case 'laporantransaksi':
                        include 'laporantransaksi.php';
                        break;

                    case 'profile':
                        include 'profile.php';
                        break;

                    default:
                        echo "<script>window.location='index.php?page=dashboard';</script>";
                        break;
                }
            } else {
                include 'dashboard.php';
            }
            ?>

            <!-- #/ container -->
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy; Designed & Developed by <a href="index.php?page=dashboard">Book Market</a> <?= date('Y'); ?></p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <script src="../assets/panel/plugins/common/common.min.js"></script>
    <script src="../assets/panel/js/custom.min.js"></script>
    <script src="../assets/panel/js/settings.js"></script>
    <script src="../assets/panel/js/gleek.js"></script>
    <script src="../assets/panel/js/styleSwitcher.js"></script>

    <!-- Chartjs -->
    <script src="../assets/panel/plugins/chart.js/Chart.bundle.min.js"></script>
    <!-- Circle progress -->
    <script src="../assets/panel/plugins/circle-progress/circle-progress.min.js"></script>
    <!-- Datamap -->
    <script src="../assets/panel/plugins/d3v3/index.js"></script>
    <script src="../assets/panel/plugins/topojson/topojson.min.js"></script>
    <script src="../assets/panel/plugins/datamaps/datamaps.world.min.js"></script>
    <!-- Morrisjs -->
    <script src="../assets/panel/plugins/raphael/raphael.min.js"></script>
    <script src="../assets/panel/plugins/morris/morris.min.js"></script>
    <!-- Pignose Calender -->
    <script src="../assets/panel/plugins/moment/moment.min.js"></script>
    <script src="../assets/panel/plugins/pg-calendar/js/pignose.calendar.min.js"></script>
    <!-- ChartistJS -->
    <script src="../assets/panel/plugins/chartist/js/chartist.min.js"></script>
    <script src="../assets/panel/plugins/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js"></script>



    <script src="../assets/panel/js/dashboard/dashboard-1.js"></script>


    <script src="../assets/panel/plugins/tables/js/jquery.dataTables.min.js"></script>
    <script src="../assets/panel/plugins/tables/js/datatable/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/panel/plugins/tables/js/datatable-init/datatable-basic.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            $('.select2modal').select2({
                dropdownParent: $('.modal'),
                width: '100%'
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>

</body>

</html>