<?php
session_start();
include 'koneksi.php';
require_admin();

// Hapus iuran
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM iuran WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    echo "<script>location='iuran.php'</script>";
}

// Tambah iuran
if (isset($_POST['tambah'])) {
    $nama = esc($conn, $_POST['nama']);
    $nominal = (int) $_POST['nominal'];
    $tipe = esc($conn, $_POST['tipe']);
    $tahun = esc($conn, $_POST['tahun']);
    $tanggal = esc($conn, $_POST['tanggal']);
    $deadline = esc($conn, $_POST['deadline']);

    $stmt = mysqli_prepare($conn, "INSERT INTO iuran 
    (nama_iuran, nominal, tipe, tahun, tanggal_event, aktif, deadline) 
    VALUES (?, ?, ?, ?, ?, 1, ?)");
    mysqli_stmt_bind_param($stmt, 'sissss', $nama, $nominal, $tipe, $tahun, $tanggal, $deadline);
    mysqli_stmt_execute($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Iuran</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container py-4">

<!-- HERO -->
<div class="hero">
    <h2>Data Iuran</h2>
    <p>Kelola iuran tahunan dan event</p>
</div>

<!-- FORM -->
<div class="card fade-in mb-4">
    <h5 class="mb-3">Tambah Iuran</h5>

    <form method="POST">

        <div class="mb-2">
            <input name="nama" class="form-control" placeholder="Nama Iuran" required>
        </div>

        <div class="mb-2">
            <input name="nominal" type="number" class="form-control" placeholder="Nominal" required>
        </div>

        <div class="mb-2">
            <select name="tipe" class="form-control">
                <option value="tahunan">Tahunan</option>
                <option value="event">Event</option>
            </select>
        </div>

        <div class="mb-2">
            <input name="tahun" class="form-control" placeholder="Tahun (opsional)">
        </div>

        <div class="mb-2">
            <label class="text-muted small">Tanggal Event</label>
            <input type="date" name="tanggal" class="form-control">
        </div>

        <div class="mb-2">
            <label class="text-muted small">Deadline</label>
            <input type="date" name="deadline" class="form-control" required>
        </div>

        <button name="tambah" class="btn btn-primary">Tambah Iuran</button>

    </form>
</div>

<!-- TABLE -->
<div class="card fade-in">

    <h5 class="mb-3">Daftar Iuran</h5>

    <div class="table-responsive">
        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Tipe</th>
                    <th>Tahun</th>
                    <th>Event</th>
                    <th>Deadline</th>
                    <th width="260">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $data = mysqli_query($conn, "SELECT * FROM iuran");

            while ($d = mysqli_fetch_array($data)) {

                echo "<tr>
                    <td>{$d['nama_iuran']}</td>
                    <td>Rp " . number_format($d['nominal']) . "</td>
                    <td>{$d['tipe']}</td>
                    <td>{$d['tahun']}</td>
                    <td>" . ($d['tanggal_event'] ? date('d M Y', strtotime($d['tanggal_event'])) : '-') . "</td>
                    <td>" . date('d M Y', strtotime($d['deadline'])) . "</td>

                    <td class='d-flex flex-wrap gap-1'>

                        <a href='bayar.php?id={$d['id']}' class='btn btn-primary btn-sm'>
                            Bayar
                        </a>

                        <a href='belum_bayar.php?id={$d['id']}' class='btn btn-warning btn-sm'>
                            Belum
                        </a>

                        <button 
                            onclick=\"confirmDelete('?hapus={$d['id']}', '{$d['nama_iuran']}')\" 
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