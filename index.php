<?php
// Front controller seperti index.php pada mvc9: semua aksi dirutekan dari satu pintu masuk.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/DB.php';
require_once __DIR__ . '/controllers/LecturerController.php';
require_once __DIR__ . '/controllers/CourseController.php';
require_once __DIR__ . '/controllers/CourseMaterialController.php';

// Ambil parameter route. Seperti contoh mvc9, aksi dipilih dengan if dan pengecekan GET/POST.
$page = $_GET['page'] ?? 'lecturers';

// POST dari form dosen diproses controller; ID pada URL membedakan tambah dan edit.
if (isset($_POST['save_lecturer'])) {
    $controller = new LecturerController();
    if ($page === 'lecturer-edit') { $controller->edit(); } else { $controller->add(); }
} elseif (isset($_POST['save_course'])) {
    // POST form mata kuliah memakai pola yang sama, dengan dosen sebagai relasi.
    $controller = new CourseController();
    if ($page === 'course-edit') { $controller->edit(); } else { $controller->add(); }
} elseif (isset($_POST['save_material'])) {
    // Form materi memakai route create/edit dan menyimpan relasi course_id.
    $controller = new CourseMaterialController();
    if ($page === 'material-edit') { $controller->edit(); } else { $controller->add(); }
} elseif (!empty($_GET['page']) && $_GET['page'] === 'lecturer-delete') {
    // Hapus hanya boleh melalui POST; ID dibaca dari form seperti pada pola mvc9.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
    (new LecturerController())->delete();
} elseif (!empty($_GET['page']) && $_GET['page'] === 'course-delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=courses'); exit; }
    (new CourseController())->delete();
} elseif (!empty($_GET['page']) && $_GET['page'] === 'material-delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=materials'); exit; }
    (new CourseMaterialController())->delete();
} elseif ($page === 'lecturer-edit') {
    // GET dengan ID menampilkan form edit dosen.
    (new LecturerController())->edit();
} elseif ($page === 'lecturer-create') {
    (new LecturerController())->add();
} elseif ($page === 'course-edit') {
    // GET dengan ID menampilkan form edit mata kuliah.
    (new CourseController())->edit();
} elseif ($page === 'course-create') {
    (new CourseController())->add();
} elseif ($page === 'material-edit') {
    (new CourseMaterialController())->edit();
} elseif ($page === 'material-create') {
    (new CourseMaterialController())->add();
} elseif ($page === 'courses') {
    (new CourseController())->index();
} elseif ($page === 'materials') {
    (new CourseMaterialController())->index();
} elseif ($page === 'lecturers') {
    (new LecturerController())->index();
} else {
    // Route yang tidak dikenal kembali ke halaman awal seperti redirect pada mvc9.
    header('Location: index.php');
    exit;
}
