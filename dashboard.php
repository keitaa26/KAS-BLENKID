<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// total pemasukan
$pemasukan = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(jumlah) as total FROM pembayaran")
)['total'] ?? 0;

// total pengeluaran
$pengeluaran = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(jumlah) as total FROM pengeluaran")
)['total'] ?? 0;

// saldo
$saldo = $pemasukan - $pengeluaran;

// 🔥 AKTIVITAS TERBARU
$pembayaran_terbaru = mysqli_query($conn, "
    SELECT p.*, a.nama, i.nama_iuran 
    FROM pembayaran p
    JOIN anggota a ON p.id_anggota = a.id
    JOIN iuran i ON p.id_iuran = i.id
    ORDER BY p.tanggal_bayar DESC
    LIMIT 5
");

$pengeluaran_terbaru = mysqli_query($conn, "
    SELECT * FROM pengeluaran
    ORDER BY tanggal DESC
    LIMIT 3
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="container d-flex justify-content-between">

    <span class="navbar-brand text-white fw-bold">
        Admin Kas
    </span>

    <div class="d-flex gap-2">

        <a href="dashboard.php" class="btn btn-sm btn-light">Dashboard</a>
        <a href="anggota.php" class="btn btn-sm btn-light">Anggota</a>
        <a href="iuran.php" class="btn btn-sm btn-light">Iuran</a>
        <a href="pengeluaran.php" class="btn btn-sm btn-light">Pengeluaran</a>
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>

    </div>
  </div>
</nav>

<!-- CONTENT -->
<div class="container py-4">

    <!-- HERO -->
    <div class="hero">
        <h2>Dashboard Admin</h2>
        <p>Ringkasan keuangan kas</p>
    </div>

    <!-- CARD -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card fade-in">
                <p class="text-muted mb-1">Pemasukan</p>
                <h4>Rp <?= number_format($pemasukan) ?></h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card fade-in">
                <p class="text-muted mb-1">Pengeluaran</p>
                <h4>Rp <?= number_format($pengeluaran) ?></h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card fade-in">
                <p class="text-muted mb-1">Saldo</p>
                <h4 class="<?= $saldo < 0 ? 'text-danger' : 'text-success' ?>">
                    Rp <?= number_format($saldo) ?>
                </h4>
            </div>
        </div>

    </div>

    <!-- 🔥 AKTIVITAS TERAKHIR -->
    <div class="card fade-in p-4">

        <h5 class="mb-3">Aktivitas Terakhir</h5>

        <div class="row">

            <!-- PEMBAYARAN -->
            <div class="col-md-6 mb-3">
                <h6 class="text-muted">Pembayaran Terbaru</h6>

                <?php while($p = mysqli_fetch_array($pembayaran_terbaru)) { ?>
                    <div class="d-flex justify-content-between border-bottom py-2">

                        <div>
                            <b><?= $p['nama'] ?></b><br>
                            <small class="text-muted"><?= $p['nama_iuran'] ?></small>
                        </div>

                        <div class="text-end">
                            <b class="text-success">
                                Rp <?= number_format($p['jumlah']) ?>
                            </b><br>
                            <small class="text-muted">
                                <?= date('d M', strtotime($p['tanggal_bayar'])) ?>
                            </small>
                        </div>

                    </div>
                <?php } ?>

            </div>

            <!-- PENGELUARAN -->
            <div class="col-md-6 mb-3">
                <h6 class="text-muted">Pengeluaran Terbaru</h6>

                <?php while($k = mysqli_fetch_array($pengeluaran_terbaru)) { ?>
                    <div class="d-flex justify-content-between border-bottom py-2">

                        <div>
                            <b><?= $k['keterangan'] ?></b>
                        </div>

                        <div class="text-end">
                            <b class="text-danger">
                                Rp <?= number_format($k['jumlah']) ?>
                            </b><br>
                            <small class="text-muted">
                                <?= date('d M', strtotime($k['tanggal'])) ?>
                            </small>
                        </div>

                    </div>
                <?php } ?>

            </div>

        </div>

    </div>

</div>

<script src="script.js"></script>

</body>
</html>