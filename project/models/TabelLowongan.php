<?php
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/Lowongan.php';
require_once __DIR__ . '/KontrakModelLowongan.php';

class TabelLowongan extends DB implements KontrakModelLowongan {
    private function mapRowToLowongan(array $row): Lowongan {
        return new Lowongan(
            (int)$row['id'], $row['judul'], $row['departemen'], $row['lokasi'],
            (int)$row['gaji'], (int)$row['kuota'], $row['status']
        );
    }

    public function getAllLowongan(): array {
        $this->executeQuery("SELECT * FROM lowongan ORDER BY id DESC");
        $rows = $this->getAllResult();
        $list = [];
        foreach ($rows as $row) {
            $list[] = $this->mapRowToLowongan($row);
        }
        return $list;
    }

    public function getLowonganById(int $id): ?Lowongan {
        $this->executeQuery("SELECT * FROM lowongan WHERE id = :id LIMIT 1", [':id' => $id]);
        $rows = $this->getAllResult();
        return empty($rows) ? null : $this->mapRowToLowongan($rows[0]);
    }

    public function addLowongan(string $judul, string $departemen, string $lokasi, int $gaji, int $kuota, string $status): bool {
        return $this->executeQuery(
            "INSERT INTO lowongan (judul, departemen, lokasi, gaji, kuota, status)
             VALUES (:judul, :dep, :lokasi, :gaji, :kuota, :status)",
            [':judul' => $judul, ':dep' => $departemen, ':lokasi' => $lokasi,
             ':gaji' => $gaji, ':kuota' => $kuota, ':status' => $status]
        );
    }

    public function updateLowongan(int $id, string $judul, string $departemen, string $lokasi, int $gaji, int $kuota, string $status): bool {
        return $this->executeQuery(
            "UPDATE lowongan SET judul = :judul, departemen = :dep, lokasi = :lokasi,
             gaji = :gaji, kuota = :kuota, status = :status WHERE id = :id",
            [':judul' => $judul, ':dep' => $departemen, ':lokasi' => $lokasi,
             ':gaji' => $gaji, ':kuota' => $kuota, ':status' => $status, ':id' => $id]
        );
    }

    public function deleteLowongan(int $id): bool {
        return $this->executeQuery("DELETE FROM lowongan WHERE id = :id", [':id' => $id]);
    }
}
