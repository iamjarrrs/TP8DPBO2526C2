<?php
include_once __DIR__ . '/DB.php';

// Model materi mengelola materi belajar yang terkait dengan mata kuliah.
class CourseMaterial extends DB
{
    // Ambil materi beserta kode dan nama mata kuliah untuk halaman daftar.
    public function getMaterials()
    {
        $sql = 'SELECT course_materials.*, courses.code AS course_code, courses.name AS course_name '
            . 'FROM course_materials JOIN courses ON courses.id = course_materials.course_id '
            . 'ORDER BY course_materials.id DESC';
        $this->execute($sql);
    }

    // Cari satu materi untuk mengisi form edit.
    public function getMaterial(int $id)
    {
        $stmt = $this->connection()->prepare('SELECT * FROM course_materials WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    // Simpan materi baru atau perbarui materi sesuai ID yang diberikan.
    public function save(?int $id, int $courseId, string $title, string $description, string $url)
    {
        if ($id === null) {
            $stmt = $this->connection()->prepare('INSERT INTO course_materials (course_id, title, description, resource_url) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isss', $courseId, $title, $description, $url);
        } else {
            $stmt = $this->connection()->prepare('UPDATE course_materials SET course_id = ?, title = ?, description = ?, resource_url = ? WHERE id = ?');
            $stmt->bind_param('isssi', $courseId, $title, $description, $url, $id);
        }
        $stmt->execute();
    }

    // Hapus materi berdasarkan primary key.
    public function delete(int $id)
    {
        $stmt = $this->connection()->prepare('DELETE FROM course_materials WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
}
