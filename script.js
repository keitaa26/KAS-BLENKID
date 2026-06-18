/* ===== KONFIRMASI HAPUS ===== */
function confirmDelete(url, name) {
    if (confirm("Yakin hapus '" + name + "'?")) {
        window.location.href = url;
    }
}

/* ===== AUTO HIDE ALERT ===== */
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    });
});

/* ===== INPUT EFFECT ===== */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', () => input.classList.add('shadow-sm'));
        input.addEventListener('blur', () => input.classList.remove('shadow-sm'));
    });
});

/* ===== FIX: JANGAN BLOK SUBMIT ===== */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            // HANYA UBAH TEKS, JANGAN DISABLE
            const btn = form.querySelector('button[type="submit"]');

            if (btn) {
                btn.innerText = "Loading...";
            }

            // ❌ TIDAK ADA preventDefault
            // ❌ TIDAK ADA disable
        });
    });
});