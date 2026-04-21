<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$guru_query = mysqli_query($conn, "SELECT nip, nama_lengkap FROM guru");
$mapel_query = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel");

if (isset($_POST['submit'])) {
    $nip = $_POST['nip'];
    $kode_mapel = $_POST['kode_mapel'];
    $jenis_file = $_POST['jenis_file'];
    
    $nama_file_asli = $_FILES['berkas']['name'];
    $tmp_file = $_FILES['berkas']['tmp_name'];
    $ukuran_file = $_FILES['berkas']['size'];
    $error = $_FILES['berkas']['error'];

    $ekstensi_diizinkan = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));

    if ($error === 0) {
        if (in_array($ekstensi_file, $ekstensi_diizinkan)) {
            if ($ukuran_file <= 25000000) { 
                $nama_bersih = preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_file_asli);
                $nama_file_baru = time() . '_' . $nama_bersih;
                $path_tujuan = '../../assets/uploads/dokumen/' . $nama_file_baru;

                if (move_uploaded_file($tmp_file, $path_tujuan)) {
                    $sql = "INSERT INTO dokumen (nip, kode_mapel, jenis_file, nama_file, path_file) 
                            VALUES ('$nip', '$kode_mapel', '$jenis_file', '$nama_file_baru', '$path_tujuan')";
                    
                    if (mysqli_query($conn, $sql)) {
                        echo "<script>alert('Dokumen berhasil diunggah!'); window.location.href='index.php';</script>";
                    } else {
                        echo "<script>alert('Gagal menyimpan ke database: " . mysqli_error($conn) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Gagal memindahkan file. Pastikan folder assets/uploads/dokumen/ sudah ada!');</script>";
                }
            } else {
                echo "<script>alert('Ukuran file terlalu besar! Maksimal 25MB.');</script>";
            }
        } else {
            echo "<script>alert('Ekstensi file tidak valid! Harap unggah PDF, Word, Excel, atau PPT.');</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan saat mengunggah file. (Error Code: $error)');</script>";
    }
}
?>

<div id="page-content-wrapper">
    <div class="main-content">
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h4 class="fw-bold m-0">Unggah Dokumen Baru</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="upload.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pengunggah (Guru)</label>
                                <select name="nip" class="form-select" required>
                                    <option value="">-- Pilih Guru --</option>
                                    <?php while ($g = mysqli_fetch_assoc($guru_query)) { ?>
                                        <option value="<?= $g['nip'] ?>"><?= $g['nama_lengkap'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mata Pelajaran</label>
                                <select name="kode_mapel" class="form-select" required>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    <?php while ($m = mysqli_fetch_assoc($mapel_query)) { ?>
                                        <option value="<?= $m['kode_mapel'] ?>"><?= $m['nama_mapel'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jenis Dokumen</label>
                                <select name="jenis_file" class="form-select" required>
                                    <option value="RPP">RPP</option>
                                    <option value="Silabus">Silabus</option>
                                    <option value="Modul">Modul Ajar</option>
                                    <option value="ATP">ATP</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Pilih File (Maks 25MB)</label>
                                <input type="file" name="berkas" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="submit" class="btn btn-success"><i class='bi bi-cloud-arrow-up'></i> Simpan Dokumen</button>
                                <a href="index.php" class="btn btn-light">Batal / Kembali</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>