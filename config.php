<?php
// Konfigurasi database terpusat seperti pola Config pada project mvc9.
class Config
{
    public static string $db_host = 'localhost';
    public static string $db_user = 'root';
    public static string $db_pass = '';
    public static string $db_name = 'tp_mvc25';
}

// Escape teks dinamis sebelum dimasukkan ke dokumen HTML.
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
