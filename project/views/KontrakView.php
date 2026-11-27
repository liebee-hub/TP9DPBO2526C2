<?php
interface KontrakView {
    public function renderDaftar(string $rowsHtml, int $totalData): string;
    public function renderForm(string $judul, string $action, array $formData): string;
}
