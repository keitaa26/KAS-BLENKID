<?php
session_start();
include 'koneksi.php';
require_admin();

// ==========================================
// PROSES BAYAR - Insert ke tabel pembayaran
// ==========================================
if (isset($_POST['bayar'])) {
    $anggota_id = (int) $_POST['anggota_id'];
    $iuran_id   = (int) $_POST['iuran_id'];
    $jumlah     = (int) $_POST['jumlah'];
    $tanggal    = esc($conn, $_POST['tanggal']);
    $keterangan = esc($conn, $_POST['keterangan']);

    // Cek apakah sudah pernah bayar iuran ini
    $cekStmt = mysqli_prepare($conn,
        "SELECT id FROM pembayaran WHERE id_anggota = ? AND id_iuran = ?"
    );
    mysqli_stmt_bind_param($cekStmt, 'ii', $anggota_id, $iuran_id);
    mysqli_stmt_execute($cekStmt);
    $cekResult = mysqli_stmt_get_result($cekStmt);
    $cek = mysqli_fetch_assoc($cekResult);

    if ($cek) {
        $msg = 'already';
    } else {
        $insertStmt = mysqli_prepare($conn,
            "INSERT INTO pembayaran (id_anggota, id_iuran, jumlah, tanggal_bayar, jumlah_bayar) 
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($insertStmt, 'iiisi', $anggota_id, $iuran_id, $jumlah, $tanggal, $jumlah);
        mysqli_stmt_execute($insertStmt);
        $msg = 'success';
    }

    $iuran_id_redirect = (int) $_POST['iuran_id'];
    echo "<script>location='bayar.php?iuran_id={$iuran_id_redirect}&msg={$msg}'</script>";
    exit;
}

// ==========================================
// HAPUS PEMBAYARAN
// ==========================================
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $iuran_back = (int) $_GET['iuran_id'];
    if ($id > 0) {
        $deleteStmt = mysqli_prepare($conn, "DELETE FROM pembayaran WHERE id = ?");
        mysqli_stmt_bind_param($deleteStmt, 'i', $id);
        mysqli_stmt_execute($deleteStmt);
        echo "<script>location='bayar.php?iuran_id={$iuran_back}&msg=deleted'</script>";
        exit;
    }
}

// ==========================================
// AMBIL DATA IURAN
// ==========================================
$iuran_id   = (int) ($_GET['iuran_id'] ?? 0);
$iuran_list = mysqli_query($conn, "SELECT * FROM iuran ORDER BY tanggal_event DESC");
$iuran_sel  = null;

if ($iuran_id > 0) {
    $selectIuranStmt = mysqli_prepare($conn, "SELECT * FROM iuran WHERE id = ?");
    mysqli_stmt_bind_param($selectIuranStmt, 'i', $iuran_id);
    mysqli_stmt_execute($selectIuranStmt);
    $resultIuran = mysqli_stmt_get_result($selectIuranStmt);
    $iuran_sel = mysqli_fetch_assoc($resultIuran);
}

// ==========================================
// HITUNG STATISTIK PEMBAYARAN (jika iuran dipilih)
// ==========================================
$total_anggota   = 0;
$sudah_bayar     = 0;
$belum_bayar     = 0;
$total_terkumpul = 0;

if ($iuran_sel) {
    $total_anggota   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM anggota WHERE status='aktif'"))['c'];
    $sudah_bayar     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM pembayaran WHERE id_iuran=$iuran_id"))['c'];
    $belum_bayar     = $total_anggota - $sudah_bayar;
    $total_terkumpul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE id_iuran=$iuran_id"))['total'] ?? 0;
}

// Notif pesan
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pembayaran - Kas BLENKID</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .status-sudah { background: rgba(39,174,96,0.12); color: #1e8449; border-radius: 20px; padding: 5px 14px; font-weight:600; font-size:0.82rem; }
        .status-belum { background: rgba(231,76,60,0.12); color: #c0392b; border-radius: 20px; padding: 5px 14px; font-weight:600; font-size:0.82rem; }
        .progress-custom { height: 10px; border-radius: 10px; }
        .iuran-select-card { cursor:pointer; transition: all 0.2s; border: 2px solid transparent; }
        .iuran-select-card:hover { border-color: var(--secondary); transform: translateY(-2px); }
        .iuran-select-card.active { border-color: var(--secondary); background: rgba(52,152,219,0.06); }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-credit-card"></i> Kelola Pembayaran</h2>
            <p class="text-muted">Catat pembayaran iuran anggota dan pantau status</p>
        </div>
    </div>

    <?php if ($msg == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <strong>Berhasil!</strong> Pembayaran berhasil dicatat.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php elseif ($msg == 'already'): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong> Anggota ini sudah membayar iuran tersebut.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php elseif ($msg == 'deleted'): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-trash"></i> Data pembayaran berhasil dihapus.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- PILIH IURAN -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-hand-holding-dollar"></i> Pilih Iuran</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="bayar.php" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Iuran yang ingin dicatat pembayarannya</label>
                    <select name="iuran_id" class="form-select" required onchange="this.form.submit()">
                        <option value="">-- Pilih Iuran --</option>
                        <?php
                        mysqli_data_seek($iuran_list, 0);
                        while ($il = mysqli_fetch_array($iuran_list)) {
                            $sel = ($il['id'] == $iuran_id) ? 'selected' : '';
                            $tgl = date('d M Y', strtotime($il['tanggal_event']));
                            $nom = number_format($il['nominal'], 0, ',', '.');
                            echo "<option value='{$il['id']}' {$sel}>{$il['nama_iuran']} — Rp {$nom} ({$tgl})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary-custom w-100">
                        <i class="fas fa-filter"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($iuran_sel): ?>

    <!-- STATISTIK -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card info text-center py-3">
                <h6 style="color:rgba(255,255,255,0.8); font-size:0.8rem;">TOTAL ANGGOTA</h6>
                <h3><?= $total_anggota ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success text-center py-3">
                <h6 style="color:rgba(255,255,255,0.8); font-size:0.8rem;">SUDAH BAYAR</h6>
                <h3><?= $sudah_bayar ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card danger text-center py-3">
                <h6 style="color:rgba(255,255,255,0.8); font-size:0.8rem;">BELUM BAYAR</h6>
                <h3><?= $belum_bayar ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card info text-center py-3" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                <h6 style="color:rgba(255,255,255,0.8); font-size:0.8rem;">TERKUMPUL</h6>
                <h4 style="color:white; font-size:1.2rem;">Rp <?= number_format($total_terkumpul) ?></h4>
            </div>
        </div>
    </div>

    <!-- PROGRESS BAR -->
    <?php if ($total_anggota > 0): ?>
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold">Progress Pembayaran</span>
                <span class="fw-bold"><?= $sudah_bayar ?>/<?= $total_anggota ?> Anggota</span>
            </div>
            <?php $pct = round(($sudah_bayar / $total_anggota) * 100); ?>
            <div class="progress progress-custom">
                <div class="progress-bar bg-success" style="width:<?= $pct ?>%"><?= $pct ?>%</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- FORM TAMBAH PEMBAYARAN -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Catat Pembayaran Baru</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="iuran_id" value="<?= $iuran_id ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Anggota</label>
                        <select name="anggota_id" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            <?php
                            // Tampilkan hanya anggota yang BELUM bayar iuran ini
                            $anggota_belum = mysqli_query($conn,
                                "SELECT * FROM anggota WHERE status='aktif'
                                 AND id NOT IN (SELECT id_anggota FROM pembayaran WHERE id_iuran=$iuran_id)
                                 ORDER BY nama ASC"
                            );
                            while ($ab = mysqli_fetch_array($anggota_belum)) {
                                echo "<option value='{$ab['id']}'>{$ab['nama']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control"
                               value="<?= $iuran_sel['nominal'] ?>" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Bayar</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                               placeholder="Opsional" maxlength="200">
                    </div>
                </div>
                <div class="mt-3">
                    <button name="bayar" class="btn btn-success-custom">
                        <i class="fas fa-save"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL STATUS PEMBAYARAN SEMUA ANGGOTA -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Status Pembayaran: <?= htmlspecialchars($iuran_sel['nama_iuran']) ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><i class="fas fa-user"></i> Nama Anggota</th>
                            <th class="text-end"><i class="fas fa-money-bill"></i> Jumlah Bayar</th>
                            <th><i class="fas fa-calendar"></i> Tanggal Bayar</th>
                            <th><i class="fas fa-comment"></i> Keterangan</th>
                            <th class="text-center"><i class="fas fa-circle-check"></i> Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    // JOIN semua anggota aktif dengan data pembayaran (left join agar yang belum bayar juga muncul)
                    $rows = mysqli_query($conn,
                        "SELECT a.id as anggota_id, a.nama, a.no_hp,
                                p.id as bayar_id, p.jumlah_bayar, p.tanggal_bayar
                         FROM anggota a
                         LEFT JOIN pembayaran p ON p.id_anggota = a.id AND p.id_iuran = $iuran_id
                         WHERE a.status = 'aktif'
                         ORDER BY p.tanggal_bayar DESC, a.nama ASC"
                    );
                    while ($r = mysqli_fetch_assoc($rows)) {
                        $sudah = !empty($r['bayar_id']);
                        $statusHtml = $sudah
                            ? '<span class="status-sudah"><i class="fas fa-check-circle"></i> Sudah Bayar</span>'
                            : '<span class="status-belum"><i class="fas fa-clock"></i> Belum Bayar</span>';
                        $jumlah  = $sudah ? 'Rp ' . number_format($r['jumlah_bayar']) : '<span class="text-muted">—</span>';
                        $tanggal = $sudah ? date('d M Y', strtotime($r['tanggal_bayar'])) : '<span class="text-muted">—</span>';
                        $ket     = $sudah ? htmlspecialchars($r['keterangan'] ?: '—') : '<span class="text-muted">—</span>';
                        $aksi    = $sudah
                            ? "<a href='?hapus={$r['bayar_id']}&iuran_id={$iuran_id}' class='btn btn-danger-custom btn-sm'
                                   onclick=\"return confirm('Hapus data pembayaran ini?')\">
                                   <i class='fas fa-trash'></i>
                               </a>"
                            : '<span class="text-muted">-</span>';

                        echo "<tr>
                            <td class='text-muted'>{$no}</td>
                            <td><strong>{$r['nama']}</strong></td>
                            <td class='text-end'>{$jumlah}</td>
                            <td>{$tanggal}</td>
                            <td>{$ket}</td>
                            <td class='text-center'>{$statusHtml}</td>
                            <td class='text-center'>{$aksi}</td>
                        </tr>";
                        $no++;
                    }
                    if ($no == 1) {
                        echo "<tr><td colspan='7' class='text-center text-muted py-4'>
                            <i class='fas fa-users-slash fa-2x mb-2 d-block'></i>Belum ada anggota aktif
                        </td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-hand-pointer fa-3x mb-3 d-block"></i>
            <h5>Pilih iuran di atas untuk melihat status pembayaran</h5>
            <p>Atau <a href="iuran.php">tambah iuran baru</a> terlebih dahulu</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
