<?php
session_start();
include 'koneksi.php';

// kalau sudah login → langsung ke dashboard
if (isset($_SESSION['login'])) {
    header('Location: dashboard.php');
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (verify_admin_password($password, $user['password'])) {
            // Jika password tersimpan dalam plain text, upgrade hash otomatis.
            if (!is_hashed_password($user['password'])) {
                $newHash = hash_admin_password($password);
                $updateStmt = mysqli_prepare($conn, "UPDATE admin SET password = ? WHERE username = ?");
                mysqli_stmt_bind_param($updateStmt, 'ss', $newHash, $username);
                mysqli_stmt_execute($updateStmt);
            }

            $_SESSION['login'] = true;
            $_SESSION['username'] = $user['username'];

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Password salah!';
    } else {
        $error = 'Username tidak ditemukan!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">

    <div class="card fade-in p-4" style="width: 100%; max-width: 400px;">

        <div class="text-center mb-3">
            <h3>Login Admin</h3>
            <small class="text-muted">Kas IPP BLENKID</small>
        </div>

        <?php if ($error != "") { ?>
            <div class="alert alert-danger text-center">
                <?= $error ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <!-- PASSWORD + TOGGLE -->
            <div class="mb-3 position-relative">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>

                <button type="button" onclick="togglePassword()" 
                    class="btn btn-sm btn-light position-absolute end-0 top-50 translate-middle-y me-2">
                    👁
                </button>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100">
                Login
            </button>

        </form>

        <!-- BACK -->
        <div class="text-center mt-3">
            <a href="index.php" class="text-muted small">← Kembali ke halaman utama</a>
        </div>

    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}
</script>

<script src="script.js"></script>

</body>
</html>