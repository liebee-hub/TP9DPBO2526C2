<?php
interface KontrakModelLowongan {
    public function getAllLowongan(): array;
    public function getLowonganById(int $id): ?Lowongan;
    public function addLowongan(string $judul, string $departemen, string $lokasi, int $gaji, int $kuota, string $status): bool;
    public function updateLowongan(int $id, string $judul, string $departemen, string $lokasi, int $gaji, int $kuota, string $status): bool;
    public function deleteLowongan(int $id): bool;
}
