<?php
require_once '../../config/database.php';
include '../../includes/header.php';
include '../../includes/sidebar.php';

// Ambil data dropdown
$guru_query = mysqli_query($conn, "SELECT nip, nama_lengkap FROM guru ORDER BY nama_lengkap");
$mapel_query = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel ORDER BY nama_mapel");

$error = [];
$success = "";

// PROSES UPLOAD
if (isset($_POST['submit'])) {

    $nip         = mysqli_real_escape_string($conn, $_POST['nip']);
    $kode_mapel  = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
    $jenis_file  = mysqli_real_escape_string($conn, $_POST['jenis_file']);

    $file_name   = $_FILES['berkas']['name'];
    $tmp_file    = $_FILES['berkas']['tmp_name'];
    $file_size   = $_FILES['berkas']['size'];
    $file_error  = $_FILES['berkas']['error'];

    // Validasi
    if (empty($nip)) $error[] = "Guru harus dipilih";
    if (empty($kode_mapel)) $error[] = "Mapel harus dipilih";

    $allowed_ext = ['pdf','doc','docx','xls','xlsx','ppt','pptx'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if ($file_error !== 0) {
        $error[] = "Terjadi kesalahan saat upload file";
    }

    if (!in_array($ext, $allowed_ext)) {
        $error[] = "Format file tidak didukung";
    }

    if ($file_size > 50000000) {
        $error[] = "Ukuran file maksimal 25MB";
    }

    // Jika tidak ada error
    if (empty($error)) {

        $nama_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
        $path = "../../assets/dokumen/" . $nama_baru;

        if (move_uploaded_file($tmp_file, $path)) {

            $sql = "INSERT INTO dokumen (nip, kode_mapel, jenis_file, nama_file, path_file)
                    VALUES ('$nip','$kode_mapel','$jenis_file','$nama_baru','$path')";

            if (mysqli_query($conn, $sql)) {
                $success = "Dokumen berhasil diupload";
            } else {
                $error[] = "Gagal simpan database";
            }

        } else {
            $error[] = "Gagal upload file ke server";
        }
    }
}
?>

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Upload Dokumen</h4>

        <a href="index.php" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- ALERT -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                <?php foreach($error as $e): ?>
                    <li><?= $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Guru</label>
                        <select name="nip" class="form-select" required>
                            <option value="">Pilih Guru</option>
                            <?php while ($g = mysqli_fetch_assoc($guru_query)): ?>
                                <option value="<?= $g['nip'] ?>"><?= $g['nama_lengkap'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="kode_mapel" class="form-select" required>
                            <option value="">Pilih Mapel</option>
                            <?php while ($m = mysqli_fetch_assoc($mapel_query)): ?>
                                <option value="<?= $m['kode_mapel'] ?>"><?= $m['nama_mapel'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Dokumen</label>
                        <select name="jenis_file" class="form-select">
                            <option value="RPP">RPP</option>
                            <option value="Silabus">Silabus</option>
                            <option value="Modul">Modul</option>
                            <option value="ATP">ATP</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">File</label>
                        <input type="file" name="berkas" class="form-control" required>
                        <small class="text-muted">Max 25MB (pdf, word, excel, ppt)</small>
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-light border px-4">
                        Batal
                    </a>

                    <button type="submit" name="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>