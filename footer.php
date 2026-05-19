<br>
<br>
<br>
<br>
<?php

$page = basename($_SERVER['PHP_SELF']);

?>

<div class="bg-white border-top fixed-bottom py-2 shadow-sm">

    <div class="d-flex justify-content-around text-center">

        <div>
            <a href="index.php"
                class="text-decoration-none <?= ($page == 'index.php') ? 'text-primary' : 'text-muted' ?>">

                <i class="bi bi-house-door-fill d-block"></i>

                <small style="font-size: 0.7rem;">
                    Home
                </small>

            </a>
        </div>

        <div>
            <a href="produk.php"
                class="text-decoration-none <?= ($page == 'produk.php' || $page == 'produkdetail.php') ? 'text-primary' : 'text-muted' ?>">

                <i class="bi bi-bag-fill d-block"></i>

                <small style="font-size: 0.7rem;">
                    Order
                </small>

            </a>
        </div>

        <div>
            <a href="keranjang.php"
                class="text-decoration-none <?= ($page == 'keranjang.php' || $page == 'checkout.php') ? 'text-primary' : 'text-muted' ?>">

                <i class="bi bi-cart-fill d-block"></i>

                <small style="font-size: 0.7rem;">
                    Cart
                </small>

            </a>
        </div>

        <div>
            <a href="akun.php"
                class="text-decoration-none <?= ($page == 'akun.php') ? 'text-primary' : 'text-muted' ?>">

                <i class="bi bi-person-fill d-block"></i>

                <small style="font-size: 0.7rem;">
                    Akun
                </small>

            </a>
        </div>

    </div>

</div>

<!-- <div class="d-none d-md-block bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted">
            <p>&copy; 2024 Foodie Palembang. All Rights Reserved.</p>
        </div>
    </div> -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>