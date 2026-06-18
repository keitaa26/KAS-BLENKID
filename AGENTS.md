# Development Rules — Kas IPP BLENKID

Setiap agen AI WAJIB mematuhi aturan berikut secara ketat:

## 1. Wajib Plan Mode (No Direct Build)
- Sebelum mengerjakan APAPUN, agen HARUS menyusun rencana terlebih dahulu.
- Rencana harus mencakup: file yang akan diubah, tujuan perubahan, potensi risiko.
- Presentasikan rencana ke user. TUNGGU persetujuan user sebelum melanjutkan.

## 2. Build Hanya Setelah Plan Disetujui
- DILARANG langsung mengedit file tanpa persetujuan.
- Jika user menyetujui plan, baru lakukan implementasi.

## 3. Test Code Setelah Build Selesai
- Setiap selesai build, lakukan testing:
  - Cek sintaks PHP: `php -l <file>`
  - Pastikan tidak ada `die()` atau `var_dump()` tertinggal dari debugging
  - Pastikan semua fungsionalitas berjalan (tidak ada fitur yang rusak)
  - Cek tidak ada SQL injection (harus pakai prepared statement)
  - Cek tidak ada XSS (setiap output html harus pakai `htmlspecialchars()`)
- JIKA TEST GAGAL: perbaiki bug, jangan lanjut ke step berikutnya.

## 4. Git Commit & Push (Hanya Jika Diminta)
- DILARANG commit tanpa perintah eksplisit dari user.
- Jika user menyuruh commit, lakukan:
  ```
  git add <file-file yang relevan>
  git commit -m "pesan commit yang deskriptif"
  git push
  ```
- Pastikan hanya file yang relevan yang di-stage. Jangan commit file sampah atau konfigurasi lokal.

## 5. Test di GitHub (CI/CD)
- Setelah push, pantau GitHub Actions / deployment.
- Jika ada error di CI: laporkan ke user, jangan diabaikan.

## 6. Security Checklist (Wajib)
Setiap build HARUS memenuhi:
- ✅ Prepared statement / parameterized query (AMAN dari SQL injection)
- ✅ `htmlspecialchars()` untuk semua output HTML
- ✅ Tidak ada plain password (minimal `password_hash()` / `password_verify()`)
- ✅ Validasi input server-side (tipe data, panjang, required)
- ✅ Tidak ada debug code di production

## 7. Bahasa
- Kode: Indonesia (untuk konsistensi dengan kode existing)
- Komentar: Indonesia
- Commit message: Indonesia
- Diskusi dengan user: Ikuti bahasa yang digunakan user

## 8. Tidak Boleh Overtthinking / Over-engineering
- Jangan menambahkan fitur di luar rencana yang sudah disetujui
- Jangan refactor tanpa izin
- Jangan ganti struktur database tanpa izin

---

*Aturan ini berlaku untuk seluruh pengembangan aplikasi Kas IPP BLENKID.*
