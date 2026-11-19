<?php
require_once __DIR__ . '/DB.php';

// Model mata kuliah menangani query tabel courses dan relasinya dengan dosen.
class Course extends DB
{
    // JOIN mengambil nama dosen supaya daftar lebih mudah dibaca.
    public function getCourses()
    {
        $sql = 'SELECT courses.*, lecturers.name AS lecturer_name FROM courses JOIN lecturers ON lecturers.id = courses.lecturer_id ORDER BY courses.id DESC';
        $this->execute($sql);
    }

    // Cari mata kuliah untuk mengisi form edit.
    public function getCourse(int $id): ?array
    {
        $stmt = $this->connection()->prepare('SELECT * FROM courses WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    // Simpan mata kuliah baru atau ubah kolom data yang sudah ada.
    public function save(?int $id, string $code, string $name, int $credits, int $lecturerId)
    {
        if ($id === null) {
            $stmt = $this->connection()->prepare('INSERT INTO courses (code, name, credits, lecturer_id) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssii', $code, $name, $credits, $lecturerId);
        } else {
            $stmt = $this->connection()->prepare('UPDATE courses SET code = ?, name = ?, credits = ?, lecturer_id = ? WHERE id = ?');
            $stmt->bind_param('ssiii', $code, $name, $credits, $lecturerId, $id);
        }
        $stmt->execute();
    }

    // Hapus mata kuliah dengan prepared statement berdasarkan primary key.
    public function delete(int $id)
    {
        $stmt = $this->connection()->prepare('DELETE FROM courses WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
}
