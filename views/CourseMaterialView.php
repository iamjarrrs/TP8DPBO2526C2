<?php
include_once __DIR__ . '/Template.php';

// View materi mengubah data hasil model menjadi tabel dan form HTML.
class CourseMaterialView
{
    public function render(array $data)
    {
        $rows = '';
        $no = 1;
        foreach ($data as $material) {
            // ID dipakai untuk aksi edit/hapus; kolom pertama tetap nomor urut.
            $id = e((string) $material['id']);
            $rows .= '<tr><td>' . $no++ . '</td><td>' . e($material['course_code']) . ' - ' . e($material['course_name']) . '</td>'
                . '<td>' . e($material['title']) . '</td><td>' . e($material['description']) . '</td><td>'
                . '<a class="btn btn-sm btn-warning" href="index.php?page=material-edit&amp;id=' . $id . '">Edit</a> '
                . '<form class="d-inline" method="post" action="index.php?page=material-delete" onsubmit="return confirm(\'Hapus materi ini?\')">'
                . '<input type="hidden" name="id" value="' . $id . '"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>';
        }

        $template = new Template('materials.html');
        $template->replace('MATERIAL_ROWS', $rows ?: '<tr><td colspan="5">Belum ada materi.</td></tr>');
        $message = ($_GET['error'] ?? '') === 'delete-failed' ? 'Materi gagal dihapus. Silakan coba lagi.' : '';
        $template->replace('STATUS_MESSAGE', $message ? '<div class="alert alert-warning">' . e($message) . '</div>' : '');
        $template->write();
    }

    // Render form materi sekaligus pilihan mata kuliah yang tersedia.
    public function renderForm(?array $material, ?int $id, array $courses, ?string $error)
    {
        $options = '<option value="">Pilih mata kuliah</option>';
        foreach ($courses as $course) {
            $courseId = (int) $course['id'];
            $selected = (int) ($material['course_id'] ?? 0) === $courseId ? ' selected' : '';
            $options .= '<option value="' . e((string) $courseId) . '"' . $selected . '>'
                . e($course['code'] . ' - ' . $course['name']) . '</option>';
        }

        $template = new Template('material_form.html');
        $template->replace('FORM_TITLE', $id ? 'Edit Materi' : 'Tambah Materi');
        $template->replace('FORM_ACTION', $id ? 'index.php?page=material-edit&amp;id=' . e((string) $id) : 'index.php?page=material-create');
        $template->replace('COURSE_OPTIONS', $options);
        $template->replace('TITLE', e($material['title'] ?? ''));
        $template->replace('DESCRIPTION', e($material['description'] ?? ''));
        $template->replace('RESOURCE_URL', e($material['resource_url'] ?? ''));
        $template->replace('ERROR_MESSAGE', $error ? '<div class="alert alert-danger">' . e($error) . '</div>' : '');
        $template->replace('COURSE_WARNING', $courses ? '' : '<div class="alert alert-warning">Tambahkan mata kuliah terlebih dahulu.</div>');
        $template->replace('DISABLED', $courses ? '' : 'disabled');
        $template->write();
    }
}
