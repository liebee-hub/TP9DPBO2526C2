<?php
// Composition root: merakit Model + View + Presenter lalu merutekan request.
require_once 'models/TabelKandidat.php';
require_once 'models/TabelLowongan.php';
require_once 'views/KontrakView.php';
require_once 'views/ViewKandidat.php';
require_once 'views/KontrakViewLowongan.php';
require_once 'views/ViewLowongan.php';
require_once 'presenters/PresenterKandidat.php';
require_once 'presenters/PresenterLowongan.php';

$page = $_GET['page'] ?? 'kandidat';
if (!in_array($page, ['kandidat', 'lowongan'], true)) {
    $page = 'kandidat';
}

$kandidatPresenter = new PresenterKandidat(new TabelKandidat(), new ViewKandidat());
$lowonganPresenter = new PresenterLowongan(new TabelLowongan(), new ViewLowongan());

// ----- Handle POST -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($page === 'kandidat') {
            $nama = $_POST['nama'] ?? '';
            $posisi = $_POST['posisi'] ?? '';
            $domisili = $_POST['domisili'] ?? '';
            $skor = (int)($_POST['skorTes'] ?? 0);
            $pengalaman = (int)($_POST['pengalamanTahun'] ?? 0);
            if ($action === 'tambah') {
                $kandidatPresenter->tambahKandidat($nama, $posisi, $domisili, $skor, $pengalaman);
            } elseif ($action === 'ubah') {
                $kandidatPresenter->ubahKandidat($id, $nama, $posisi, $domisili, $skor, $pengalaman);
            } elseif ($action === 'hapus') {
                $kandidatPresenter->hapusKandidat($id);
            }
        } else {
            $judul = $_POST['judul'] ?? '';
            $dep = $_POST['departemen'] ?? '';
            $lokasi = $_POST['lokasi'] ?? '';
            $gaji = (int)($_POST['gaji'] ?? 0);
            $kuota = (int)($_POST['kuota'] ?? 0);
            $status = $_POST['status'] ?? '';
            if ($action === 'tambah') {
                $lowonganPresenter->tambahLowongan($judul, $dep, $lokasi, $gaji, $kuota, $status);
            } elseif ($action === 'ubah') {
                $lowonganPresenter->ubahLowongan($id, $judul, $dep, $lokasi, $gaji, $kuota, $status);
            } elseif ($action === 'hapus') {
                $lowonganPresenter->hapusLowongan($id);
            }
        }
    } catch (InvalidArgumentException $e) {
        echo '<p style="font-family:Arial;color:#b91c1c">' . htmlspecialchars($e->getMessage()) .
             '</p><a href="javascript:history.back()">Kembali</a>';
        exit();
    }
    header("Location: index.php?page=" . $page);
    exit();
}

// ----- Handle GET -----
$aksi = $_GET['aksi'] ?? 'daftar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($page === 'kandidat') {
    echo $aksi === 'form' ? $kandidatPresenter->tampilkanFormKandidat($id) : $kandidatPresenter->tampilkanKandidat();
} else {
    echo $aksi === 'form' ? $lowonganPresenter->tampilkanFormLowongan($id) : $lowonganPresenter->tampilkanLowongan();
}
