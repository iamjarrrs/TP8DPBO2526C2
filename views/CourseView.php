<?php
require_once __DIR__ . '/Template.php';

// View mata kuliah merender daftar dan form dengan template HTML terpisah.
class CourseView
{
    public function render(array $data)
    {
        $rows = '';
        $no = 1;
        foreach ($data as $course) {
            // Escape nilai kolom sebelum membuat baris tabel untuk mencegah HTML injection.
            $id = e((string) $course['id']);
            $rows .= '<tr><td>' . $no++ . '</td><td>' . e($course['code']) . '</td><td>' . e($course['name']) . '</td>'
                . '<td>' . e((string) $course['credits']) . '</td><td>' . e($course['lecturer_name']) . '</td><td>'
                . '<a class="btn btn-sm btn-warning" href="index.php?page=course-edit&amp;id=' . $id . '">Edit</a> '
                . '<form class="d-inline" method="post" action="index.php?page=course-delete" onsubmit="return confirm(\'Hapus mata kuliah ini?\')">'
                . '<input type="hidden" name="id" value="' . $id . '"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>';
        }

        $template = new Template('courses.html');
        $template->replace('COURSE_ROWS', $rows ?: '<tr><td colspan="6">Belum ada data mata kuliah.</td></tr>');
        $message = ($_GET['error'] ?? '') === 'delete-failed' ? 'Mata kuliah gagal dihapus. Silakan coba lagi.' : '';
        $template->replace('STATUS_MESSAGE', $message ? '<div class="alert alert-warning">' . e($message) . '</div>' : '');
        $template->write();
    }

    // Buat option daftar dosen; option yang sedang dipakai ditandai selected.
    public function renderForm(?array $course, ?int $id, array $lecturers, ?string $error)
    {
        $options = '<option value="">Pilih dosen</option>';
        foreach ($lecturers as $lecturer) {
            $lecturerId = (int) $lecturer['id'];
            $selected = (int) ($course['lecturer_id'] ?? 0) === $lecturerId ? ' selected' : '';
            $options .= '<option value="' . e((string) $lecturerId) . '"' . $selected . '>' . e($lecturer['name']) . '</option>';
        }

        $template = new Template('course_form.html');
        $template->replace('FORM_TITLE', $id ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah');
        $template->replace('FORM_ACTION', $id ? 'index.php?page=course-edit&amp;id=' . e((string) $id) : 'index.php?page=course-create');
        $template->replace('CODE', e($course['code'] ?? ''));
        $template->replace('NAME', e($course['name'] ?? ''));
        $template->replace('CREDITS', e((string) ($course['credits'] ?? 3)));
        $template->replace('LECTURER_OPTIONS', $options);
        $template->replace('ERROR_MESSAGE', $error ? '<div class="alert alert-danger">' . e($error) . '</div>' : '');
        $template->replace('LECTURER_WARNING', $lecturers ? '' : '<div class="alert alert-warning">Tambahkan dosen terlebih dahulu.</div>');
        $template->replace('DISABLED', $lecturers ? '' : 'disabled');
        $template->write();
    }
}
