<?php
// Passive View: hanya membaca template & mengganti placeholder.
// View tidak mengenal Model/Entity dan tidak melakukan perulangan data.
class ViewKandidat implements KontrakView {
    private function load(string $file): string {
        return file_get_contents(__DIR__ . '/../template/' . $file);
    }

    public function renderDaftar(string $rowsHtml, int $totalData): string {
        return strtr($this->load('skin.html'), [
            '{{TABLE_ROWS}}' => $rowsHtml,
            '{{TOTAL_DATA}}' => (string)$totalData,
        ]);
    }

    public function renderForm(string $judul, string $action, array $formData): string {
        $keys = array_map(fn($k) => '{{' . strtoupper($k) . '}}', array_keys($formData));
        $html = strtr($this->load('form.html'), array_combine($keys, array_values($formData)));
        $html = strtr($html, ['{{TITLE}}' => $judul, '{{ACTION}}' => $action]);
        // kosongkan placeholder yang tidak terisi (mode tambah)
        return preg_replace('/\{\{[A-Z_]+\}\}/', '', $html);
    }
}
