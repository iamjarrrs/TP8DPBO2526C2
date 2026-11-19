<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../models/Lecturer.php';
include_once __DIR__ . '/../views/LecturerView.php';

// Controller dosen menerima request, memakai model, lalu meneruskan data ke view.
class LecturerController
{
    // Properti untuk menyimpan model dosen yang dipakai controller.
    private $lecturer;

    // Buat model; koneksi dibuka saat aksi dijalankan.
    public function __construct()
    {
        $this->lecturer = new Lecturer(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name);
    }

    // READ: ambil daftar dosen, tutup koneksi, lalu render template daftar.
    public function index()
    {
        $this->lecturer->open();
        $this->lecturer->getLecturers();
        // Pindahkan hasil query ke array agar view menerima semua baris sekaligus.
        $data = array();
        while ($row = $this->lecturer->getResult()) {
            array_push($data, $row);
        }
        $this->lecturer->close();
        // Tampilkan pesan setelah operasi hapus ditolak oleh relasi database.
        $message = null;
        if (($_GET['error'] ?? '') === 'lecturer-linked') {
            $message = 'Dosen tidak dapat dihapus karena masih menjadi pengampu mata kuliah.';
        } elseif (($_GET['error'] ?? '') === 'delete-failed') {
            $message = 'Data dosen gagal dihapus. Silakan coba lagi.';
        }
        (new LecturerView())->render($data, $message);
    }

    // Method aksi tambah; form menerima data melalui POST.
    public function add()
    {
        $this->form();
    }

    // Method aksi edit; ID menentukan dosen yang akan dimuat ke form.
    public function edit()
    {
        // ID target dibaca dari query string, mengikuti pola request pada mvc9.
        $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) { header('Location: index.php'); exit; }
        $this->form($id);
    }

    // CREATE/UPDATE: GET menampilkan form dan POST memvalidasi lalu menyimpan data.
    private function form(?int $id = null)
    {
        $this->lecturer->open();
        $lecturer = $id ? $this->lecturer->getLecturer($id) : null;
        if ($id && !$lecturer) {
            $this->lecturer->close();
            header('Location: index.php');
            exit;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $nidn = trim($_POST['nidn'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $joinDate = trim($_POST['join_date'] ?? '');
            // Pastikan tanggal valid dan berbentuk YYYY-MM-DD sebelum simpan ke database.
            $validDate = DateTime::createFromFormat('Y-m-d', $joinDate);
            if ($name !== '' && $nidn !== '' && $phone !== '' && $validDate && $validDate->format('Y-m-d') === $joinDate) {
                $this->lecturer->save($id, $name, $nidn, $phone, $joinDate);
                $this->lecturer->close();
                header('Location: index.php');
                exit;
            }
            $error = 'Lengkapi semua data dengan format tanggal yang valid.';
            $lecturer = ['name' => $name, 'nidn' => $nidn, 'phone' => $phone, 'join_date' => $joinDate];
        }

        $this->lecturer->close();
        (new LecturerView())->renderForm($lecturer, $id, $error);
    }

    // DELETE: jalankan penghapusan; foreign key mencegah penghapusan dosen yang masih terkait.
    public function delete()
    {
        // ID penghapusan dibaca dari form POST agar aksi tidak dijalankan lewat tautan GET.
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) { header('Location: index.php'); exit; }
        $this->lecturer->open();
        try {
            $this->lecturer->delete($id);
            $this->lecturer->close();
            header('Location: index.php');
        } catch (mysqli_sql_exception $exception) {
            // Foreign key RESTRICT menolak hapus dosen yang masih dipakai mata kuliah.
            $this->lecturer->close();
            $error = (int) $exception->getCode() === 1451 ? 'lecturer-linked' : 'delete-failed';
            header('Location: index.php?error=' . $error);
        }
        exit;
    }
}
