<?php
require_once __DIR__ . '/KontrakPresenter.php';

class PresenterKandidat implements KontrakPresenter {
    private KontrakModel $model;
    private KontrakView $view;

    public function __construct(KontrakModel $model, KontrakView $view) {
        $this->model = $model;
        $this->view = $view;
    }

    private function e(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }

    private function buildRowHtml(int $no, Kandidat $k): string {
        $id = $k->getId();
        return '<tr>'
            . '<td>' . $no . '</td>'
            . '<td><b>' . $this->e($k->getNama()) . '</b></td>'
            . '<td>' . $this->e($k->getPosisi()) . '</td>'
            . '<td>' . $this->e($k->getDomisili()) . '</td>'
            . '<td>' . $k->getSkorTes() . ' / 100</td>'
            . '<td>' . $k->getPengalamanTahun() . ' Tahun</td>'
            . '<td class="aksi">'
            . '<a class="edit" href="index.php?page=kandidat&aksi=form&id=' . $id . '">Edit</a>'
            . '<form method="POST" action="index.php?page=kandidat" onsubmit="return confirm(\'Hapus kandidat ini?\')">'
            . '<input type="hidden" name="action" value="hapus">'
            . '<input type="hidden" name="id" value="' . $id . '">'
            . '<button type="submit" class="hapus">Hapus</button></form>'
            . '</td></tr>';
    }

    public function tampilkanKandidat(): string {
        $listKandidat = $this->model->getAllKandidat();
        $totalKandidat = count($listKandidat);

        $rowsHtml = '';
        if (empty($listKandidat)) {
            $rowsHtml = '<tr><td colspan="7" style="text-align:center;color:#6b7280;padding:24px;">Belum ada data kandidat.</td></tr>';
        } else {
            $no = 1;
            foreach ($listKandidat as $kandidat) {
                $rowsHtml .= $this->buildRowHtml($no, $kandidat);
                $no++;
            }
        }
        return $this->view->renderDaftar($rowsHtml, $totalKandidat);
    }

    public function tampilkanFormKandidat(?int $id = null): string {
        if ($id !== null) {
            $kandidat = $this->model->getKandidatById($id);
            if ($kandidat === null) {
                header("Location: index.php?page=kandidat");
                exit();
            }
            $formData = [
                'id' => $kandidat->getId(),
                'nama' => $this->e($kandidat->getNama()),
                'posisi' => $this->e($kandidat->getPosisi()),
                'domisili' => $this->e($kandidat->getDomisili()),
                'skorTes' => $kandidat->getSkorTes(),
                'pengalamanTahun' => $kandidat->getPengalamanTahun(),
            ];
            return $this->view->renderForm("Ubah Data Kandidat", "ubah", $formData);
        }
        return $this->view->renderForm("Tambah Kandidat Baru", "tambah", []);
    }

    private function validasi(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalaman): void {
        if (trim($nama) === '' || trim($posisi) === '' || trim($domisili) === '') {
            throw new InvalidArgumentException('Nama, posisi, dan domisili tidak boleh kosong.');
        }
        if ($skorTes < 0 || $skorTes > 100) {
            throw new InvalidArgumentException('Skor tes harus 0-100.');
        }
        if ($pengalaman < 0) {
            throw new InvalidArgumentException('Pengalaman tidak boleh negatif.');
        }
    }

    public function tambahKandidat(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $this->validasi($nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
        $this->model->addKandidat(trim($nama), trim($posisi), trim($domisili), $skorTes, $pengalamanTahun);
    }

    public function ubahKandidat(int $id, string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $this->validasi($nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
        $this->model->updateKandidat($id, trim($nama), trim($posisi), trim($domisili), $skorTes, $pengalamanTahun);
    }

    public function hapusKandidat(int $id): void {
        $this->model->deleteKandidat($id);
    }
}
