<?php
include 'koneksi.php';

// ambil iuran aktif
$iuran = mysqli_query($conn, "SELECT * FROM iuran WHERE aktif=1");

// total pemasukan
$pemasukan = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(jumlah) as total FROM pembayaran")
)['total'] ?? 0;

// total pengeluaran
$pengeluaran = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(jumlah) as total FROM pengeluaran")
)['total'] ?? 0;

$saldo = $pemasukan - $pengeluaran;

// nomor admin
$wa_admin = "6285878974683";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transparansi Kas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar_public.php'; ?>

<div class="container py-4">

    <!-- HERO -->
    <div class="hero">
        <h2>Kas IPP BLENKID</h2>
        <p>Transparansi iuran & keuangan pemuda</p>

        <a href="status.php" class="btn btn-primary mt-2">
            🔍 Cek Status Saya
        </a>
    </div>

    <!-- RINGKASAN -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card">
                <p class="text-muted mb-1">Pemasukan</p>
                <h4>Rp <?= number_format($pemasukan) ?></h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <p class="text-muted mb-1">Pengeluaran</p>
                <h4>Rp <?= number_format($pengeluaran) ?></h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <p class="text-muted mb-1">Saldo</p>
                <h4>Rp <?= number_format($saldo) ?></h4>
            </div>
        </div>

    </div>

    <!-- DATA IURAN -->
    <h5 class="mb-3">Data Iuran</h5>

    <?php while ($i = mysqli_fetch_array($iuran)) { ?>

        <div class="card mb-4">

            <div class="mb-3">
                <h5 class="mb-1"><?= $i['nama_iuran'] ?></h5>
                <small class="text-muted">
                    Nominal: Rp <?= number_format($i['nominal']) ?> |
                    Deadline: <?= date('d M Y', strtotime($i['deadline'])) ?>
                </small>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Sudah</th>
                            <th>Sisa</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $anggota = mysqli_query($conn, "SELECT * FROM anggota");

                    while ($a = mysqli_fetch_array($anggota)) {

                        $total = mysqli_fetch_assoc(mysqli_query($conn, "
                            SELECT SUM(jumlah) as total 
                            FROM pembayaran 
                            WHERE id_anggota=$a[id] AND id_iuran=$i[id]
                        "))['total'] ?? 0;

                        $sisa = $i['nominal'] - $total;

                        echo "<tr>
                            <td>$a[nama]</td>
                            <td>Rp " . number_format($total) . "</td>
                            <td>Rp " . number_format($sisa > 0 ? $sisa : 0) . "</td>
                            <td>";

                        if ($total == 0) {
                            echo "<span class='badge badge-danger'>❌ Belum</span>";
                        } elseif ($sisa > 0) {
                            echo "<span class='badge badge-warning'>⚠️ Kurang</span>";
                        } else {
                            echo "<span class='badge badge-success'>✅ Lunas</span>";
                        }

                        echo "</td></tr>";
                    }
                    ?>
                    </tbody>

                </table>
            </div>

        </div>

    <?php } ?>

    <!-- WA ADMIN -->
    <div class="text-center mt-4">
        <a href="https://wa.me/<?= $wa_admin ?>?text=Halo admin, saya ingin menanyakan iuran." 
           class="btn btn-success">
           📱 Hubungi Admin
        </a>
    </div>

</div>

<script src="script.js"></script>

</body>
</html>