<?php
require_once __DIR__ . '/Template.php';

// View mengubah data dosen menjadi baris HTML lalu mengisi template halaman.
class LecturerView
{
    public function render(array $data, ?string $message = null)
    {
        $rows = '';
        $no = 1;
        foreach ($data as $lecturer) {
            // Escape setiap atribut dari database sebelum dimasukkan ke markup.
            $id = e((string) $lecturer['id']);
            $rows .= '<tr><td>' . $no++ . '</td><td>' . e($lecturer['name']) . '</td>'
                . '<td>' . e($lecturer['nidn']) . '</td><td>' . e($lecturer['phone']) . '</td>'
                . '<td>' . e($lecturer['join_date']) . '</td><td>'
                . '<a class="btn btn-sm btn-warning" href="index.php?page=lecturer-edit&amp;id=' . $id . '">Edit</a> '
                . '<form class="d-inline" method="post" action="index.php?page=lecturer-delete" onsubmit="return confirm(\'Hapus dosen ini?\')">'
                . '<input type="hidden" name="id" value="' . $id . '"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>';
        }

        $template = new Template('lecturers.html');
        $template->replace('LECTURER_ROWS', $rows ?: '<tr><td colspan="6">Belum ada data dosen.</td></tr>');
        $template->replace('STATUS_MESSAGE', $message ? '<div class="alert alert-warning">' . e($message) . '</div>' : '');
        $template->write();
    }

    // Render form tambah/edit dengan nilai awal dan pesan validasi dari controller.
    public function renderForm(?array $lecturer, ?int $id, ?string $error)
    {
        $template = new Template('lecturer_form.html');
        $template->replace('FORM_TITLE', $id ? 'Edit Dosen' : 'Tambah Dosen');
        $template->replace('FORM_ACTION', $id ? 'index.php?page=lecturer-edit&amp;id=' . e((string) $id) : 'index.php?page=lecturer-create');
        $template->replace('NAME', e($lecturer['name'] ?? ''));
        $template->replace('NIDN', e($lecturer['nidn'] ?? ''));
        $template->replace('PHONE', e($lecturer['phone'] ?? ''));
        $template->replace('JOIN_DATE', e($lecturer['join_date'] ?? ''));
        $template->replace('ERROR_MESSAGE', $error ? '<div class="alert alert-danger">' . e($error) . '</div>' : '');
        $template->write();
    }
}
