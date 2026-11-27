# TP9DPBO2526C2

## Janji
Saya Refan Maher Aysel dengan NIM 2509129 mengerjakan Tugas Praktikum 9 dalam mata kuliah Desain dan Pemrograman Berorientasi Objek untuk keberkahan-Nya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin.

## Tema
**Sistem Rekrutmen Karyawan**, website PHP Native dengan arsitektur **Model-View-Presenter (MVP)** pendekatan **Strict Passive View** untuk mengelola data kandidat (Candidates) dan lowongan kerja (Job Openings). Program ini adalah pengembangan dari starter project MVP (fitur Read dan Form tampilan kandidat), dilengkapi fitur CRUD kandidat yang utuh, ditambah satu tabel dan modul MVP baru yaitu **lowongan**.

## Desain Database

Database `mvp_kandidat_db` terdiri dari 2 tabel. File SQL ada di `mvp_db.sql`.

| Tabel | Kolom | Keterangan |
|---|---|---|
| `kandidat` (lama) | `id` (PK, auto increment), `nama`, `posisi`, `domisili`, `skorTes`, `pengalamanTahun` | Data kandidat karyawan. Daftar diurutkan dari `skorTes` tertinggi |
| `lowongan` (baru) | `id` (PK, auto increment), `judul`, `departemen`, `lokasi`, `gaji`, `kuota`, `status` (`Buka` / `Tutup`) | Data lowongan kerja yang dibuka perusahaan |

Kedua tabel berdiri sendiri (tidak memakai foreign key atau JOIN), sesuai ketentuan tugas bahwa relasi bersifat opsional.

```
kandidat      lowongan
(mandiri)     (mandiri)
```

Aturan data:
- `skorTes` harus berada pada rentang 0 sampai 100, dan `pengalamanTahun` tidak boleh negatif.
- `kuota` lowongan minimal 1, `gaji` tidak boleh negatif, dan `status` hanya boleh `Buka` atau `Tutup`.
- Aturan tersebut divalidasi di Presenter, selain atribut `required`, `min`, dan `max` pada form HTML.

## Desain Program

Program memakai **OOP dengan PDO (prepared statement)** dan struktur folder MVP sesuai modul:

```
MVP_Kandidat/
├── index.php
├── mvp_db.sql
├── models/          DB, Kandidat, Lowongan, TabelKandidat, TabelLowongan,
│                    KontrakModel, KontrakModelLowongan
├── presenters/      PresenterKandidat, PresenterLowongan,
│                    KontrakPresenter, KontrakPresenterLowongan
├── views/           ViewKandidat, ViewLowongan,
│                    KontrakView, KontrakViewLowongan
└── template/        skin.html, form.html, skin_lowongan.html,
                     form_lowongan.html, style.css
```

| File | Peran |
|---|---|
| `index.php` | Titik masuk (composition root) sekaligus router. Merakit Model, View, dan Presenter, lalu memilih aksi berdasarkan `$_GET['page']`, `$_GET['aksi']`, dan `$_POST['action']` (`tambah`, `ubah`, `hapus`). Setelah POST, melakukan redirect ke halaman daftar. |
| `models/DB.php` (`DB`) | Class induk. Membuka koneksi PDO, menjalankan query lewat `executeQuery()` (prepared statement), dan mengambil hasil lewat `getAllResult()`. |
| `models/Kandidat.php`, `Lowongan.php` | Entity (objek data) dengan getter. |
| `models/TabelKandidat.php` (`TabelKandidat`) | Mewarisi `DB` dan mengimplementasikan `KontrakModel`. Query tabel `kandidat`: `getAllKandidat`, `getKandidatById`, `addKandidat`, `updateKandidat`, `deleteKandidat`. |
| `models/TabelLowongan.php` (`TabelLowongan`) | Mewarisi `DB` dan mengimplementasikan `KontrakModelLowongan`. Query tabel `lowongan`: `getAllLowongan`, `getLowonganById`, `addLowongan`, `updateLowongan`, `deleteLowongan`. |
| `models/Kontrak*.php` | Interface (kontrak) yang harus dipenuhi Model. |
| `presenters/Presenter*.php` | Penghubung Model dan View. Menerima input, validasi, memanggil Model, menghitung total data, merakit baris tabel `<tr>`, menyiapkan data prefill form, dan meng-escape output dengan `htmlspecialchars`. |
| `presenters/KontrakPresenter*.php` | Interface (kontrak) yang harus dipenuhi Presenter. |
| `views/View*.php` | View pasif. Hanya membaca file template dan mengganti placeholder (`{{TABLE_ROWS}}`, `{{TOTAL_DATA}}`, `{{NAMA}}`, dan lainnya). |
| `views/Kontrak*.php` | Interface (kontrak) yang harus dipenuhi View: `renderDaftar()` dan `renderForm()`. |
| `template/*.html` | Kerangka HTML dengan placeholder. `style.css` dipakai semua halaman. |

### Penerapan Strict Passive View
- Folder `views/` tidak memakai `require_once` ke Model atau Entity, tidak ada `foreach`, dan tidak ada `count()`.
- View hanya menerima `string`, `int`, dan `array` siap tampil dari Presenter.
- Seluruh komunikasi lewat interface: Presenter bergantung pada `KontrakModel` dan `KontrakView` (serta versi `Lowongan`), bukan pada class konkret.

Keamanan dasar: query memakai prepared statement (parameter terikat), input divalidasi di Presenter, dan output di-escape dengan `htmlspecialchars` sebelum masuk ke template.

## Penjelasan Alur

Contoh: membuka halaman Lowongan (`index.php?page=lowongan`).

1. **Browser** mengirim request ke `index.php?page=lowongan`.
2. **`index.php`** membuat `TabelLowongan` (Model), `ViewLowongan` (View), dan `PresenterLowongan` (Presenter), lalu memanggil `tampilkanLowongan()`.
3. **Presenter** meminta data ke **Model**: `getAllLowongan()`.
4. **Model** menjalankan query lewat `DB::executeQuery()`, mengubah tiap baris menjadi objek `Lowongan`, lalu mengembalikannya ke Presenter.
5. **Presenter** menghitung total, merakit baris tabel `<tr>` (dengan format gaji dan badge status), lalu mengirim `string` hasilnya ke View.
6. **View** membaca `template/skin_lowongan.html`, mengganti `{{TABLE_ROWS}}` dan `{{TOTAL_DATA}}`, lalu menampilkannya ke user.

Alur aksi CRUD (pola sama di Kandidat dan Lowongan, contoh Lowongan):

| Aksi | Request | Yang terjadi |
|---|---|---|
| **Create** | Klik **+ Tambah Lowongan** (`?page=lowongan&aksi=form`), isi form, klik **Simpan** (`POST action=tambah`) | `tampilkanFormLowongan()` menampilkan form kosong → `index.php` → `PresenterLowongan::tambahLowongan()` → validasi → `TabelLowongan::addLowongan()` → redirect ke daftar |
| **Read** | Buka `index.php?page=lowongan` | Alur 1–6 di atas |
| **Update** | Klik **Edit** (`?page=lowongan&aksi=form&id=ID`), form terisi data lama (prefill), klik **Simpan** (`POST action=ubah`) | `tampilkanFormLowongan($id)` → `getLowonganById()` → `ubahLowongan()` → validasi → `updateLowongan()` → redirect |
| **Delete** | Klik **Hapus**, konfirmasi (`POST action=hapus`) | `hapusLowongan()` → `deleteLowongan()` → redirect |

Halaman Kandidat (`index.php?page=kandidat`) memakai pola yang sama. Bedanya, daftar kandidat diurutkan dari skor tes tertinggi.

**Alur Validasi**:
- Jika data tidak valid (misalnya skor di atas 100 atau kuota kurang dari 1), Presenter melempar `InvalidArgumentException`.
- Exception ditangkap di `index.php`, pesan kesalahan ditampilkan, dan data tidak disimpan.

**Alur Data Tidak Ditemukan**:
- Jika `id` pada form Edit tidak ada di database, Presenter mengalihkan user kembali ke halaman daftar.

## Dokumentasi

Halaman Kandidat
<img width="1917" height="957" alt="tp9 web kandidat" src="https://github.com/user-attachments/assets/9bc17e31-912c-403a-a458-46aee8d1d4dd" />


Add Kandidat
<img width="1917" height="957" alt="tp9 add kandidat" src="https://github.com/user-attachments/assets/3f06a5a8-03b5-4e6a-a863-654c9c8c8ed1" />
<img width="1917" height="960" alt="tp9 add kandidat2" src="https://github.com/user-attachments/assets/bcb628e8-28da-4845-bed2-a1da26ad92ec" />

Edit Kandidat
<img width="1917" height="957" alt="tp9 edit kandidat" src="https://github.com/user-attachments/assets/6303a000-f58a-4fa4-90bb-3f9865dea0f6" />
<img width="1917" height="960" alt="tp9 edit kandidat2" src="https://github.com/user-attachments/assets/62e1c0bd-79b6-4646-80fa-d354aabd3e8c" />

Delete Kandidat
<img width="1915" height="957" alt="tp9 hapus kandidat" src="https://github.com/user-attachments/assets/5694714b-9bba-477e-bc88-886dde72388a" />


Halaman Lowongan
<img width="1917" height="952" alt="tp9 web lowongan" src="https://github.com/user-attachments/assets/41124465-114d-49fd-bbd2-14308870b1dd" />


Add Lowongan
<img width="1917" height="956" alt="tp9 add lowongan" src="https://github.com/user-attachments/assets/c4a31685-7a9b-48c4-8746-b5604240aec3" />
<img width="1917" height="960" alt="tp9 add lowongan 2" src="https://github.com/user-attachments/assets/d37fc7db-92d6-4daf-b44c-d7217b4b4961" />


Edit Lowongan
<img width="1895" height="957" alt="tp9 edit lowongan" src="https://github.com/user-attachments/assets/7a41f120-56c3-4d2b-9d72-805ce66ca019" />
<img width="1917" height="957" alt="tp9 edit lowongan2" src="https://github.com/user-attachments/assets/59afa4bb-346f-4ef3-8437-32fb2e177c7a" />

Delete Lowongan
<img width="1917" height="962" alt="tp9 hapus lowongan" src="https://github.com/user-attachments/assets/600bbbe9-f47e-4c47-9113-4fc8a338c3b4" />
