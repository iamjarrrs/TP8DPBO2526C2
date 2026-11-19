<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../models/Course.php';
include_once __DIR__ . '/../models/Lecturer.php';
include_once __DIR__ . '/../views/CourseView.php';

// Controller mata kuliah mengatur alur request CRUD antara model dan view.
class CourseController
{
    // Properti model untuk tabel mata kuliah dan daftar dosen relasinya.
    private $course;
    private $lecturer;

    // Buat dua model karena form mata kuliah perlu daftar dosen.
    public function __construct()
    {
        $this->course = new Course(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
        $this->lecturer = new Lecturer(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
    }

    // READ: ambil daftar mata kuliah beserta nama dosen pengampu.
    public function index()
    {
        $this->course->open();
        $this->course->getCourses();
        // Kumpulkan baris hasil JOIN untuk diteruskan ke view.
        $data = array();
        while ($row = $this->course->getResult()) {
            array_push($data, $row);
        }
        $this->course->close();
        (new CourseView())->render($data);
    }

    // Method aksi tambah untuk menampilkan dan memproses form mata kuliah baru.
    public function add()
    {
        $this->form();
    }

    // Method aksi edit untuk memuat data sesuai ID.
    public function edit()
    {
        // ID mata kuliah dibaca dari query string seperti parameter edit pada mvc9.
        $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) { header('Location: index.php?page=courses'); exit; }
        $this->form($id);
    }

    // CREATE/UPDATE: GET memuat form; POST memeriksa input sebelum menyimpan.
    private function form(?int $id = null)
    {
        $this->course->open();
        $this->lecturer->open();
        $course = $id ? $this->course->getCourse($id) : null;
        if ($id && !$course) {
            $this->course->close();
            $this->lecturer->close();
            header('Location: index.php?page=courses');
            exit;
        }
        $lecturers = $this->lecturer->getLecturers();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $credits = filter_input(INPUT_POST, 'credits', FILTER_VALIDATE_INT);
            $lecturerId = filter_input(INPUT_POST, 'lecturer_id', FILTER_VALIDATE_INT);
            // Cek rentang SKS dan pastikan dosen yang dipilih ada di database.
            if ($code !== '' && $name !== '' && $credits >= 1 && $credits <= 6 && $lecturerId && $this->lecturer->getLecturer($lecturerId)) {
                $this->course->save($id, $code, $name, $credits, $lecturerId);
                $this->course->close();
                $this->lecturer->close();
                header('Location: index.php?page=courses');
                exit;
            }
            $error = 'Periksa kembali semua isian. SKS harus 1–6 dan dosen harus dipilih.';
            $course = ['code' => $code, 'name' => $name, 'credits' => $credits ?: 1, 'lecturer_id' => $lecturerId ?: 0];
        }

        $this->course->close();
        $this->lecturer->close();
        (new CourseView())->renderForm($course, $id, $lecturers, $error);
    }

    // DELETE: menghapus satu mata kuliah menggunakan ID dari router.
    public function delete()
    {
        // Form POST mengirimkan ID baris yang akan dihapus.
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) { header('Location: index.php?page=courses'); exit; }
        $this->course->open();
        try {
            $this->course->delete($id);
            $this->course->close();
            header('Location: index.php?page=courses');
        } catch (mysqli_sql_exception $exception) {
            // Hindari exception database tampil sebagai halaman error mentah.
            $this->course->close();
            header('Location: index.php?page=courses&error=delete-failed');
        }
        exit;
    }
}
