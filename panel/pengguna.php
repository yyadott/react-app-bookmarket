<div class="row page-titles mx-0">
    <div class="col">
        <h4>Data Pengguna</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Daftar Pengguna</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "
                            SELECT *
                            FROM users
                            WHERE role='User'
                            ORDER BY id DESC
                        ");

                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['jeniskelamin']) ?></td>
                                <td><?= htmlspecialchars($row['nohp']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>