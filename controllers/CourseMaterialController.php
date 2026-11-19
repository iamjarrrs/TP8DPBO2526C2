<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../models/CourseMaterial.php';
include_once __DIR__ . '/../models/Course.php';
include_once __DIR__ . '/../views/CourseMaterialView.php';

// Controller materi mengatur alur CRUD dan pemilihan relasi mata kuliah.
class CourseMaterialController
{
    private $material;
    private $course;

    // Dua model dibutuhkan karena form materi harus memilih mata kuliah.
    public function __construct()
    {
        $host = Config::$db_host;
        $user = Config::$db_user;
        $pass = Config::$db_pass;
        $name = Config::$db_name;
        $this->material = new CourseMaterial($host, $user, $pass, $name);
        $this->course = new Course($host, $user, $pass, $name);
    }

    // READ: ambil baris materi dan teruskan hasilnya ke view.
    public function index()
    {
        $this->material->open();
        $this->material->getMaterials();
        $data = array();
        while ($row = $this->material->getResult()) {
            array_push($data, $row);
        }
        $this->material->close();
        (new CourseMaterialView())->render($data);
    }

    // CREATE: tampilkan form baru atau proses isian POST.
    public function add()
    {
        $this->form();
    }

    // UPDATE: ID materi dibaca dari query string.
    public function edit()
    {
        $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) { header('Location: index.php?page=materials'); exit; }
        $this->form($id);
    }

    // Form create dan edit berbagi validasi serta view yang sama.
    private function form(?int $id = null)
    {
        $this->material->open();
        $this->course->open();
        $material = $id ? $this->material->getMaterial($id) : null;
        if ($id && !$material) {
            $this->material->close();
            $this->course->close();
            header('Location: index.php?page=materials');
            exit;
        }

        $this->course->getCourses();
        $courses = array();
        while ($row = $this->course->getResult()) {
            array_push($courses, $row);
        }
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = filter_var($_POST['course_id'] ?? '', FILTER_VALIDATE_INT);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            // Kolom resource_url pada database tetap ada untuk kompatibilitas, tetapi form tidak memakainya.
            $url = '';

            // Pastikan relasi mata kuliah benar dan isian materi lengkap.
            if ($courseId && $this->course->getCourse($courseId) && $title !== '' && $description !== '') {
                $this->material->save($id, $courseId, $title, $description, $url);
                $this->material->close();
                $this->course->close();
                header('Location: index.php?page=materials');
                exit;
            }

            $error = 'Lengkapi mata kuliah, judul, dan deskripsi.';
            $material = ['course_id' => $courseId ?: 0, 'title' => $title, 'description' => $description, 'resource_url' => $url];
        }

        $this->material->close();
        $this->course->close();
        (new CourseMaterialView())->renderForm($material, $id, $courses, $error);
    }

    // DELETE: ID materi dibaca dari form POST.
    public function delete()
    {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) { header('Location: index.php?page=materials'); exit; }
        $this->material->open();
        try {
            $this->material->delete($id);
            $this->material->close();
            header('Location: index.php?page=materials');
        } catch (mysqli_sql_exception $exception) {
            // Kembali ke daftar jika database menolak operasi hapus.
            $this->material->close();
            header('Location: index.php?page=materials&error=delete-failed');
        }
        exit;
    }
}
