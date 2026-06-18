<?php

session_start();
include 'koneksi.php';
require_admin();


// Hapus data
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM pengeluaran WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    echo "<script>location='pengeluaran.php'</script>";
}

// Tambah data
if (isset($_POST['tambah'])) {
    $tanggal = esc($conn, $_POST['tanggal']);
    $keterangan = esc($conn, $_POST['keterangan']);
    $jumlah = (int) $_POST['jumlah'];

    $stmt = mysqli_prepare($conn, "INSERT INTO pengeluaran 
    (tanggal, keterangan, jumlah) 
    VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssi', $tanggal, $keterangan, $jumlah);
    mysqli_stmt_execute($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengeluaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container py-4">

    <!-- HERO -->
    <div class="hero">
        <h2>Pengeluaran</h2>
        <p>Catat dan kelola pengeluaran kas</p>
    </div>

    <!-- FORM -->
    <div class="card fade-in mb-4">
        <h5 class="mb-3">Tambah Pengeluaran</h5>

        <form method="POST">

            <div class="mb-2">
                <label class="text-muted small">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="mb-2">
                <input type="text" name="keterangan" class="form-control" placeholder="Keterangan" required>
            </div>

            <div class="mb-2">
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required>
            </div>

            <button name="tambah" class="btn btn-primary">Tambah Pengeluaran</button>

        </form>
    </div>

    <!-- TABLE -->
    <div class="card fade-in">

        <h5 class="mb-3">Riwayat Pengeluaran</h5>

        <div class="table-responsive">
            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $data = mysqli_query($conn, "SELECT * FROM pengeluaran ORDER BY tanggal DESC");

                while ($d = mysqli_fetch_array($data)) {

                    echo "<tr>
                        <td>" . date('d M Y', strtotime($d['tanggal'])) . "</td>
                        <td>{$d['keterangan']}</td>
                        <td>Rp " . number_format($d['jumlah']) . "</td>
                        <td>

                            <button 
                                onclick=\"confirmDelete('?hapus={$d['id']}', '{$d['keterangan']}')\" 
                                class='btn btn-danger btn-sm'>
                                Hapus
                            </button>

                        </td>
                    </tr>";
                }
                ?>
                </tbody>

            </table>
        </div>

    </div>

</div>

<script src="script.js"></script>

</body>
</html>