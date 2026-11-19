<?php
require_once __DIR__ . '/../config.php';

// Kelas dasar koneksi database yang diwarisi model-model aplikasi.
class DB
{
    protected ?mysqli $connection = null;
    private ?mysqli_result $result = null;
    private string $dbHost;
    private string $dbUser;
    private string $dbPass;
    private string $dbName;

    // Konstruktor menyimpan konfigurasi yang dikirim controller ke model.
    public function __construct(string $host, string $user, string $pass, string $name)
    {
        $this->dbHost = $host;
        $this->dbUser = $user;
        $this->dbPass = $pass;
        $this->dbName = $name;
    }

    // Buka koneksi menggunakan nilai yang disimpan pada class Config.
    public function open()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connection = new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
        $this->connection->set_charset('utf8mb4');
    }

    // Tutup koneksi setelah controller selesai memakai model.
    public function close()
    {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    // Berikan akses aman ke koneksi untuk prepared statement pada model turunan.
    protected function connection(): mysqli
    {
        if ($this->connection === null) {
            throw new RuntimeException('Koneksi database belum dibuka.');
        }
        return $this->connection;
    }

    // Jalankan query SELECT sederhana dan simpan hasilnya untuk dibaca baris demi baris.
    public function execute(string $query)
    {
        $this->result = $this->connection()->query($query);
    }

    // Ambil baris berikutnya sebagai array; null menandakan semua baris sudah dibaca.
    public function getResult(): ?array
    {
        if ($this->result === null) {
            return null;
        }
        return $this->result->fetch_assoc() ?: null;
    }
}
