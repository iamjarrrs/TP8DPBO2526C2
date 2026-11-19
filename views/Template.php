<?php
// Template membaca file HTML lalu mengganti token dengan isi yang disiapkan view.
class Template
{
    private string $content;

    public function __construct(string $filename)
    {
        $path = __DIR__ . '/../templates/' . $filename;
        $this->content = file_get_contents($path) ?: '';
    }

    // Mengganti token lengkap seperti {{PAGE_TITLE}}; nilai HTML tabel sudah di-escape per kolom.
    public function replace(string $token, string $value)
    {
        $this->content = str_replace('{{' . $token . '}}', $value, $this->content);
    }

    // Hilangkan token yang tidak dipakai sebelum mencetak halaman.
    public function write()
    {
        echo preg_replace('/\{\{[A-Z_]+\}\}/', '', $this->content);
    }
}
