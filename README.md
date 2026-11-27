# TP9DPBO2526C… — MVP (Model-View-Presenter) Rekrutmen/HR

Aplikasi PHP native dengan arsitektur **MVP – Strict Passive View**.
Dua modul: **Kandidat** (starter, CRUD dilengkapi) dan **Lowongan** (modul baru), keduanya CRUD penuh.

## Cara Menjalankan
1. Salin folder `project/` ke `C:\xampp\htdocs\MVP_Kandidat`.
2. Nyalakan Apache & MySQL, import `mvp_db.sql` lewat phpMyAdmin (membuat DB `mvp_kandidat_db`, tabel `kandidat` & `lowongan`).
3. Buka `http://localhost/MVP_Kandidat`.

## Desain Program

### Struktur
```
project/
├── index.php                      # composition root + router
├── models/
│   ├── DB.php                     # koneksi PDO + executeQuery/getAllResult
│   ├── Kandidat.php, Lowongan.php # entity
│   ├── KontrakModel.php           # interface model kandidat
│   ├── KontrakModelLowongan.php   # interface model lowongan
│   ├── TabelKandidat.php          # implementasi model kandidat
│   └── TabelLowongan.php          # implementasi model lowongan
├── presenters/
│   ├── KontrakPresenter.php / PresenterKandidat.php
│   └── KontrakPresenterLowongan.php / PresenterLowongan.php
├── views/
│   ├── KontrakView.php / ViewKandidat.php
│   └── KontrakViewLowongan.php / ViewLowongan.php
└── template/                      # skin*.html (daftar), form*.html, style.css
```

### Tabel Baru: `lowongan`
`id, judul, departemen, lokasi, gaji, kuota, status (Buka/Tutup)`. Tidak memakai FK ke `kandidat`.

### Pembagian Tugas
| Komponen | Tanggung jawab |
|---|---|
| Model | Query SQL dengan prepared statement, mengubah baris DB menjadi entity |
| Presenter | Validasi, memanggil Model, menghitung total, merakit baris `<tr>`, escape HTML (`htmlspecialchars`), menyiapkan data form (prefill) |
| View | Membaca file template dan mengganti placeholder (`{{TABLE_ROWS}}`, `{{TOTAL_DATA}}`, `{{NAMA}}`, dst.) |

### Aturan Strict Passive View
- Tidak ada `require_once` Model/Entity, `foreach`, atau `count()` di `views/`.
- View hanya menerima `string`, `int`, dan `array` data siap pakai.
- Komunikasi lewat interface: Presenter bergantung pada `KontrakModel` / `KontrakView` (dan versi `…Lowongan`), bukan class konkret.

## Alur Program
**Read:** `index.php?page=X` → Presenter `tampilkan…()` → Model `getAll…()` → Presenter merakit `<tr>` + total → `View::renderDaftar()` mengisi `skin*.html`.

**Create/Update (form):** `?page=X&aksi=form[&id=N]` → Presenter `tampilkanForm…($id)` → (jika ubah) Model `get…ById()` → `View::renderForm()` mengisi `form*.html` (prefill).

**Submit (POST `action` = tambah/ubah/hapus):** `index.php` memanggil method Presenter → Presenter validasi → Model `add/update/delete` (prepared statement) → redirect ke daftar. Jika validasi gagal, Presenter melempar `InvalidArgumentException` yang ditangkap `index.php`.

**Delete:** tombol Hapus di tiap baris berupa form POST `action=hapus` dengan konfirmasi `confirm()`.

## Dokumentasi
Screenrecord: lihat folder `dokumentasi/`.
