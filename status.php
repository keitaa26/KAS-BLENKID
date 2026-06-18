<?php
include 'koneksi.php';

$hasil = null;

// nomor admin
$wa_admin = "6285878974683";

if (isset($_POST['cari'])) {

    $keyword = trim($_POST['keyword']);
    $keyword = esc($conn, $keyword);

    // normalisasi no HP
    $kw_62 = preg_replace('/^0/', '62', $keyword);
    $kw_0  = preg_replace('/^62/', '0', $keyword);
    $keyword_like = '%' . $keyword . '%';
    $kw_62_like = '%' . $kw_62 . '%';
    $kw_0_like = '%' . $kw_0 . '%';

    $stmt = mysqli_prepare($conn, "
        SELECT * FROM anggota 
        WHERE 
            LOWER(TRIM(nama)) LIKE LOWER(?)
            OR REPLACE(no_hp,' ','') LIKE ?
            OR REPLACE(no_hp,' ','') LIKE ?
            OR REPLACE(no_hp,' ','') LIKE ?
        ORDER BY nama ASC
    ");
    mysqli_stmt_bind_param($stmt, 'ssss', $keyword_like, $keyword_like, $kw_62_like, $kw_0_like);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Status Pembayaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar_public.php'; ?>

<div class="container py-4">

    <!-- HERO -->
    <div class="hero">
        <h2>Cek Status Pembayaran</h2>
        <p>Masukkan nama atau nomor HP untuk melihat status iuran</p>
    </div>

    <!-- FORM -->
    <form method="POST" class="mb-4">
        <input type="text" name="keyword" class="form-control mb-2" placeholder="Masukkan nama / no HP" required>
        <button name="cari" class="btn btn-primary w-100">Cek Status</button>
    </form>

    <!-- JIKA BELUM CARI -->
    <?php if ($hasil === null) { ?>
        <div class="card text-center p-3">
            <p class="text-muted mb-0">Silakan masukkan nama atau nomor HP</p>
        </div>
    <?php } ?>

    <!-- TIDAK DITEMUKAN -->
    <?php if ($hasil && mysqli_num_rows($hasil) == 0) { ?>
        <div class="card text-center p-3">
            <p class="text-muted mb-0">Data tidak ditemukan</p>
        </div>
    <?php } ?>

    <!-- HASIL -->
    <?php if ($hasil && mysqli_num_rows($hasil) > 0) { ?>

        <?php while ($a = mysqli_fetch_array($hasil)) { ?>

            <div class="card mb-4 fade-in">

                <h5 class="mb-3"><?= htmlspecialchars($a['nama']) ?></h5>

                <div class="table-responsive">
                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Iuran</th>
                                <th>Sudah</th>
                                <th>Sisa</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        $iuran = mysqli_query($conn, "SELECT * FROM iuran WHERE aktif=1");

                        while ($i = mysqli_fetch_array($iuran)) {

                            $total = mysqli_fetch_assoc(mysqli_query($conn, "
                                SELECT SUM(jumlah) as total 
                                FROM pembayaran 
                                WHERE id_anggota={$a['id']} AND id_iuran={$i['id']}
                            "))['total'] ?? 0;

                            $sisa = $i['nominal'] - $total;

                            echo "<tr>
                                <td>{$i['nama_iuran']}</td>
                                <td>Rp " . number_format($total) . "</td>
                                <td>Rp " . number_format($sisa > 0 ? $sisa : 0) . "</td>
                                <td>";

                            if ($total == 0) {
                                echo "<span class='badge badge-danger'>Belum</span>";
                            } elseif ($sisa > 0) {
                                echo "<span class='badge badge-warning'>Kurang</span>";
                            } else {
                                echo "<span class='badge badge-success'>Lunas</span>";
                            }

                            echo "</td></tr>";
                        }
                        ?>
                        </tbody>

                    </table>
                </div>

                <!-- WA -->
                <?php
                $pesan = "Halo admin, saya {$a['nama']} ingin membayar iuran.";
                ?>

                <a href="https://wa.me/<?= $wa_admin ?>?text=<?= urlencode($pesan) ?>" 
                   class="btn btn-success mt-3">
                   Hubungi Admin
                </a>

            </div>

        <?php } ?>

    <?php } ?>

</div>

<script src="script.js"></script> 

</body>
</html>