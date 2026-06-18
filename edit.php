<?php
include 'koneksi.php';
require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// ambil data
$stmt = mysqli_prepare($conn, "SELECT * FROM anggota WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_array($result);

// proses update
if (isset($_POST['update'])) {
    $nama = esc($conn, $_POST['nama']);
    $no_hp = esc($conn, $_POST['no_hp']);
    $status = esc($conn, $_POST['status']);

    $updateStmt = mysqli_prepare($conn, "UPDATE anggota SET nama = ?, no_hp = ?, status = ? WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, 'sssi', $nama, $no_hp, $status, $id);
    mysqli_stmt_execute($updateStmt);

    echo "<script>location='anggota.php'</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Anggota</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">IPP BLENKID</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="anggota.php">Anggota</a></li>
        <li class="nav-item"><a class="nav-link" href="iuran.php">Iuran</a></li>
        <li class="nav-item"><a class="nav-link" href="pengeluaran.php">Pengeluaran</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- CONTENT -->
<div class="container mt-4">

    <h3 class="mb-4">Edit Anggota</h3>

    <div class="card shadow p-4">

        <form method="POST">
            <label>Nama</label>
            <input type="text" name="nama" value="<?= sc($d['nama']) ?>" class="form-control mb-3" required>

            <label>No HP</label>
            <input type="text" name="no_hp" value="<?= sc($d['no_hp']) ?>" class="form-control mb-3" required>

            <label>Status</label>
            <select name="status" class="form-control mb-3">
                <option value="aktif" <?= $d['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="nonaktif" <?= $d['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>

            <button name="update" class="btn btn-success">Update</button>
            <a href="anggota.php" class="btn btn-secondary">Kembali</a>
        </form>

    </div>

</div>

</body>
</html>