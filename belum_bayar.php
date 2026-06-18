<?php
session_start();
include 'koneksi.php';
require_admin();

$id_iuran = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// ambil data iuran
$selectIuranStmt = mysqli_prepare($conn, "SELECT * FROM iuran WHERE id = ?");
mysqli_stmt_bind_param($selectIuranStmt, 'i', $id_iuran);
mysqli_stmt_execute($selectIuranStmt);
$iuranResult = mysqli_stmt_get_result($selectIuranStmt);
$iuran = mysqli_fetch_array($iuranResult);
?>

<!DOCTYPE html>
<html>
<head>
<title>Belum Bayar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">

<h3>Belum Bayar - <?= $iuran['nama_iuran'] ?></h3>

<div class="card p-4 shadow">

<table class="table table-bordered">
<thead class="table-dark">
<tr>
<th>Nama</th>
<th>Status</th>
</tr>
</thead>

<tbody>
<?php
$anggota = mysqli_query($conn, "SELECT * FROM anggota");

while ($a = mysqli_fetch_array($anggota)) {

    $id = $a['id'];

    $cek = mysqli_query($conn, "SELECT * FROM pembayaran 
        WHERE id_anggota=$id AND id_iuran=$id_iuran");

    if (mysqli_num_rows($cek) == 0) {
        echo "<tr>
            <td>{$a['nama']}</td>
            <td><span class='badge bg-danger'>BELUM</span></td>
        </tr>";
    }
}
?>
</tbody>

</table>

</div>

<br>
<a href="iuran.php" class="btn btn-secondary">Kembali</a>

</div>

</body>
</html>