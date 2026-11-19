<?php
require_once __DIR__ . '/DB.php';

// Model dosen mewarisi helper koneksi DB dan berisi query tabel lecturers.
class Lecturer extends DB
{
    // Ambil seluruh data untuk halaman daftar dosen.
    public function getLecturers()
    {
        $this->execute('SELECT * FROM lecturers ORDER BY id DESC');
    }

    // Cari satu dosen untuk mengisi form edit.
    public function getLecturer(int $id): ?array
    {
        $stmt = $this->connection()->prepare('SELECT * FROM lecturers WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    // ID null menjalankan INSERT; ID terisi menjalankan UPDATE.
    public function save(?int $id, string $name, string $nidn, string $phone, string $joinDate)
    {
        if ($id === null) {
            $stmt = $this->connection()->prepare('INSERT INTO lecturers (name, nidn, phone, join_date) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $name, $nidn, $phone, $joinDate);
        } else {
            $stmt = $this->connection()->prepare('UPDATE lecturers SET name = ?, nidn = ?, phone = ?, join_date = ? WHERE id = ?');
            $stmt->bind_param('ssssi', $name, $nidn, $phone, $joinDate, $id);
        }
        $stmt->execute();
    }

    // Hapus berdasarkan ID; foreign key menolak penghapusan dosen yang masih terkait.
    public function delete(int $id)
    {
        $stmt = $this->connection()->prepare('DELETE FROM lecturers WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
}
