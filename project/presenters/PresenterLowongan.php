<?php
require_once __DIR__ . '/KontrakPresenterLowongan.php';

class PresenterLowongan implements KontrakPresenterLowongan {
    private KontrakModelLowongan $model;
    private KontrakViewLowongan $view;

    public function __construct(KontrakModelLowongan $model, KontrakViewLowongan $view) {
        $this->model = $model;
        $this->view = $view;
    }

    private function e(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }

    private function buildRowHtml(int $no, Lowongan $l): string {
        $id = $l->getId();
        $badge = $l->getStatus() === 'Buka' ? 'buka' : 'tutup';
        return '<tr>'
            . '<td>' . $no . '</td>'
            . '<td><b>' . $this->e($l->getJudul()) . '</b></td>'
            . '<td>' . $this->e($l->getDepartemen()) . '</td>'
            . '<td>' . $this->e($l->getLokasi()) . '</td>'
            . '<td>Rp ' . number_format($l->getGaji(), 0, ',', '.') . '</td>'
            . '<td>' . $l->getKuota() . ' orang</td>'
            . '<td><span class="badge ' . $badge . '">' . $this->e($l->getStatus()) . '</span></td>'
            . '<td class="aksi">'
            . '<a class="edit" href="index.php?page=lowongan&aksi=form&id=' . $id . '">Edit</a>'
            . '<form method="POST" action="index.php?page=lowongan" onsubmit="return confirm(\'Hapus lowongan ini?\')">'
            . '<input type="hidden" name="action" value="hapus">'
            . '<input type="hidden" name="id" value="' . $id . '">'
            . '<button type="submit" class="hapus">Hapus</button></form>'
            . '</td></tr>';
    }

    public function tampilkanLowongan(): string {
        $list = $this->model->getAllLowongan();
        $total = count($list);

        $rowsHtml = '';
        if (empty($list)) {
            $rowsHtml = '<tr><td colspan="8" style="text-align:center;color:#6b7280;padding:24px;">Belum ada data lowongan.</td></tr>';
        } else {
            $no = 1;
            foreach ($list as $lowongan) {
                $rowsHtml .= $this->buildRowHtml($no, $lowongan);
                $no++;
            }
        }
        return $this->view->renderDaftar($rowsHtml, $total);
    }

    public function tampilkanFormLowongan(?int $id = null): string {
        if ($id !== null) {
            $l = $this->model->getLowonganById($id);
            if ($l === null) {
                header("Location: index.php?page=lowongan");
                exit();
            }
            $formData = [
                'id' => $l->getId(),
                'judul' => $this->e($l->getJudul()),
                'departemen' => $this->e($l->getDepartemen()),
                'lokasi' => $this->e($l->getLokasi()),
                'gaji' => $l->getGaji(),
                'kuota' => $l->getKuota(),
                'selBuka' => $l->getStatus() === 'Buka' ? 'selected' : '',
                'selTutup' => $l->getStatus() === 'Tutup' ? 'selected' : '',
            ];
            return $this->view->renderForm("Ubah Data Lowongan", "ubah", $formData);
        }
        return $this->view->renderForm("Tambah Lowongan Baru", "tambah", ['selBuka' => 'selected']);
    }

    private function validasi(string $judul, string $departemen, string $lokasi, int $gaji, int $kuota, string $status): void {
        if (trim($judul) === '' || trim($departemen) === '' || trim($lokasi) === '') {
            throw new InvalidArgumentException('Judul, departemen, dan lokasi tidak boleh kosong.');
        }
        if ($gaji < 0 || $kuota < 1) {
            throw new InvalidArgumentException('Gaji tidak boleh negatif dan kuota minimal 1.');
        }
        if (!in_array($status, ['Buka', 'Tutup'], true)) {
            throw new InvalidArgumentException('Status harus Buka atau Tutup.');
        }
    }

    public function tambahLowongan(string $judul, string $departemen, string $lokasi, int $gaji, int $kuota, string $status): void {
        $this->validasi($judul, $departemen, $lokasi, $gaji, $kuota, $status);
        $this->model->addLowongan(trim($judul), trim($departemen), trim($lokasi), $gaji, $kuota, $status);
    }

    public function ubahLowongan(int $id, string $judul, string $departemen, string $lokasi, int $gaji, int $kuota, string $status): void {
        $this->validasi($judul, $departemen, $lokasi, $gaji, $kuota, $status);
        $this->model->updateLowongan($id, trim($judul), trim($departemen), trim($lokasi), $gaji, $kuota, $status);
    }

    public function hapusLowongan(int $id): void {
        $this->model->deleteLowongan($id);
    }
}
