
Nama: Akmaludin Ikhlasul Amal
NIM: 101230098

# Kas IPP BLENKID

Aplikasi kas sederhana untuk manajemen iuran, pembayaran, dan pengeluaran organisasi.

## Ringkasan
- Backend: PHP
- Database: MySQL / MariaDB
- Frontend: HTML, Bootstrap 5, CSS custom, JavaScript ringan
- Autentikasi: session PHP
- Struktur: halaman PHP terpisah untuk setiap fitur

## Perbaikan yang telah diterapkan
- `config.php` untuk konfigurasi pusat
- `functions.php` untuk helper sanitasi, auth, dan redirect
- Password admin sekarang mendukung hashing (`password_hash` / `password_verify`)
- Prepared statement ditambahkan untuk operasi CRUD utama
- Session guard admin distandarisasi
- Input output disanitasi dengan `esc()` dan `sc()`

## Use Case Diagram
```mermaid
usecaseDiagram
    actor Admin
    actor "Pengguna Publik" as Public

    Admin --> (Login)
    Admin --> (Lihat Dashboard)
    Admin --> (Kelola Anggota)
    Admin --> (Kelola Iuran)
    Admin --> (Catat Pembayaran)
    Admin --> (Lihat Anggota Belum Bayar)
    Admin --> (Catat Pengeluaran)
    Admin --> (Logout)

    Public --> (Lihat Transparansi Kas)
    Public --> (Cek Status Pembayaran)
    Public --> (Hubungi Admin via WhatsApp)
```

## Component Diagram
```mermaid
graph LR
    Browser -->|HTTP| LoginPage[login.php]
    Browser -->|HTTP| Dashboard[dashboard.php]
    Browser -->|HTTP| AnggotaPage[anggota.php]
    Browser -->|HTTP| IuranPage[iuran.php]
    Browser -->|HTTP| BayarPage[bayar.php]
    Browser -->|HTTP| PengeluaranPage[pengeluaran.php]
    Browser -->|HTTP| PublikPage[publik.php]
    Browser -->|HTTP| StatusPage[status.php]

    subgraph Admin Pages
        Dashboard
        AnggotaPage
        IuranPage
        BayarPage
        PengeluaranPage
    end

    subgraph Public Pages
        PublikPage
        StatusPage
    end

    LoginPage --> Koneksi[koneksi.php]
    Dashboard --> Koneksi
    AnggotaPage --> Koneksi
    IuranPage --> Koneksi
    BayarPage --> Koneksi
    PengeluaranPage --> Koneksi
    PublikPage --> Koneksi
    StatusPage --> Koneksi

    Koneksi -->|MySQL| Database[(kas_blenkid)]
    Database --> AnggotaTable[anggota]
    Database --> IuranTable[iuran]
    Database --> PembayaranTable[pembayaran]
    Database --> PengeluaranTable[pengeluaran]
    Database --> AdminTable[admin]
```

## Entity Diagram
```mermaid
classDiagram
    class Admin {
      +username
      +password
    }
    class Anggota {
      +id
      +nama
      +no_hp
      +status
    }
    class Iuran {
      +id
      +nama_iuran
      +nominal
      +tipe
      +tahun
      +tanggal_event
      +deadline
      +aktif
    }
    class Pembayaran {
      +id
      +id_anggota
      +id_iuran
      +jumlah
      +tanggal_bayar
      +jumlah_bayar
      +keterangan
    }
    class Pengeluaran {
      +id
      +tanggal
      +keterangan
      +jumlah
    }

    Anggota "1" <-- "0..*" Pembayaran : melakukan
    Iuran "1" <-- "0..*" Pembayaran : diterapkan pada
```

## Sequence Diagram: Login Admin
```mermaid
sequenceDiagram
    participant Admin
    participant Browser
    participant LoginPage
    participant Database

    Admin->>Browser: buka login.php
    Browser->>LoginPage: GET
    LoginPage-->>Browser: tampilkan form login
    Admin->>Browser: submit username/password
    Browser->>LoginPage: POST credentials
    LoginPage->>Database: SELECT * FROM admin WHERE username = ?
    Database-->>LoginPage: record admin
    LoginPage->>LoginPage: verify password
    LoginPage-->>Browser: redirect dashboard.php
```

## Sequence Diagram: Catat Pembayaran
```mermaid
sequenceDiagram
    participant Admin
    participant Browser
    participant BayarPage
    participant Database

    Admin->>Browser: buka bayar.php
    Browser->>BayarPage: GET with iuran_id
    BayarPage->>Database: SELECT iuran, anggota, pembayaran
    Database-->>BayarPage: data statistik
    BayarPage-->>Browser: render halaman pembayaran

    Admin->>Browser: pilih anggota + submit pembayaran
    Browser->>BayarPage: POST pembayaran
    BayarPage->>Database: SELECT existing pembayaran
    Database-->>BayarPage: hasil cek
    alt belum bayar
        BayarPage->>Database: INSERT INTO pembayaran
        Database-->>BayarPage: sukses
    else sudah bayar
        BayarPage-->>Browser: notifikasi sudah bayar
    end
    BayarPage-->>Browser: redirect kembali dengan status
```
