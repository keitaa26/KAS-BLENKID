<?php

session_start();
include 'koneksi.php';
require_admin();

// Hapus data
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM anggota WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    echo "<script>location='anggota.php'</script>";
}

// Tambah data
if (isset($_POST['tambah'])) {
    $nama = esc($conn, $_POST['nama']);
    $no_hp = esc($conn, $_POST['no_hp']);

    $stmt = mysqli_prepare($conn, "INSERT INTO anggota (nama, no_hp, status) VALUES (?, ?, 'aktif')");
    mysqli_stmt_bind_param($stmt, 'ss', $nama, $no_hp);
    mysqli_stmt_execute($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota</title>

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
        <a href="index.php" class="btn btn-sm btn-light">Dashboard</a>
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
        <h2>Data Anggota</h2>
        <p>Kelola data anggota organisasi</p>
    </div>

    <!-- FORM -->
    <div class="card fade-in mb-4">
        <h5 class="mb-3">Tambah Anggota</h5>

        <form method="POST">
            <div class="mb-2">
                <input type="text" name="nama" class="form-control" placeholder="Nama" required>
            </div>

            <div class="mb-2">
                <input type="text" name="no_hp" class="form-control" placeholder="No HP" required>
            </div>

            <button name="tambah" class="btn btn-primary">Tambah Anggota</button>
        </form>
    </div>

    <!-- TABLE -->
    <div class="card fade-in">

        <h5 class="mb-3">Daftar Anggota</h5>

        <div class="table-responsive">
            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $data = mysqli_query($conn, "SELECT * FROM anggota");

                while ($d = mysqli_fetch_array($data)) {

                    echo "<tr>
                        <td>{$d['nama']}</td>
                        <td>{$d['no_hp']}</td>
                        <td>
                            <span class='badge badge-success'>{$d['status']}</span>
                        </td>
                        <td class='d-flex gap-1'>

                            <a href='edit.php?id={$d['id']}' class='btn btn-warning btn-sm'>
                                Edit
                            </a>

                            <button 
                                onclick=\"confirmDelete('?hapus={$d['id']}', '{$d['nama']}')\" 
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