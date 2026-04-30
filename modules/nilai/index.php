<?php
include "../../config/database.php";
include "../../includes/auth.php";

// TU tidak bisa akses nilai
require_role(['Operator', 'Guru', 'Kepsek']);

$role = $_SESSION['role'];
$is_readonly = ($role === 'Kepsek');

// Ambil filter dari URL
$id_kelas    = isset($_GET['id_kelas']) ? (int)$_GET['id_kelas'] : 0;
$kode_mapel  = isset($_GET['kode_mapel']) ? $_GET['kode_mapel'] : '';
$jenis_nilai = isset($_GET['jenis_nilai']) ? $_GET['jenis_nilai'] : 'UH';

// Ambil data dropdown
$query_kelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
$query_mapel = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel ORDER BY nama_mapel");

// Jika Guru, ambil NIP dari session
$nip = $_SESSION['nip'] ?? '198001012010011001';

// Pesan notifikasi
$pesan = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/sidebar.php"; ?>

<div class="container-fluid">
    <h2 class="fw-bold mb-3">
        <?= $is_readonly ? 'Lihat' : 'Input' ?> Nilai Siswa
    </h2>

    <?php if ($pesan): ?>
        <div class="alert alert-info"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php while($k = mysqli_fetch_assoc($query_kelas)): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= $id_kelas == $k['id_kelas'] ? 'selected' : '' ?>>
                                <?= $k['nama_kelas'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Mapel</label>
                    <select name="kode_mapel" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php 
                        mysqli_data_seek($query_mapel, 0);
                        while($m = mysqli_fetch_assoc($query_mapel)): ?>
                            <option value="<?= $m['kode_mapel'] ?>" <?= $kode_mapel == $m['kode_mapel'] ? 'selected' : '' ?>>
                                <?= $m['nama_mapel'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Jenis</label>
                    <select name="jenis_nilai" class="form-select">
                        <option value="UH" <?= $jenis_nilai == 'UH' ? 'selected' : '' ?>>UH</option>
                        <option value="UTS" <?= $jenis_nilai == 'UTS' ? 'selected' : '' ?>>UTS</option>
                        <option value="UAS" <?= $jenis_nilai == 'UAS' ? 'selected' : '' ?>>UAS</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php if ($id_kelas && $kode_mapel): ?>
<form action="proses_input.php" method="POST">
    <input type="hidden" name="kode_mapel" value="<?= $kode_mapel ?>">
    <input type="hidden" name="jenis_nilai" value="<?= $jenis_nilai ?>">
    <input type="hidden" name="nip" value="<?= $nip ?>">

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
<?php
$query_siswa = mysqli_query($conn, "
    SELECT r.id_riwayat, s.nisn, s.nama_lengkap
    FROM riwayat_kelas r
    JOIN siswa s ON r.nisn = s.nisn
    WHERE r.id_kelas = $id_kelas
");

$no = 1;
while($s = mysqli_fetch_assoc($query_siswa)):
$cek = mysqli_query($conn, "
    SELECT nilai_angka FROM nilai 
    WHERE id_riwayat={$s['id_riwayat']}
    AND kode_mapel='$kode_mapel'
    AND jenis_nilai='$jenis_nilai'
");
$n = mysqli_fetch_assoc($cek);
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $s['nisn'] ?></td>
    <td><?= $s['nama_lengkap'] ?></td>
    <td>
        <input type="hidden" name="id_riwayat[]" value="<?= $s['id_riwayat'] ?>">
        <input type="number" name="nilai[]" class="form-control"
            min="0" max="100" step="0.01"
            value="<?= $n['nilai_angka'] ?? '' ?>" required <?= $is_readonly ? 'disabled' : '' ?>>
    </td>
</tr>
<?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php if(!$is_readonly): ?>
        <div class="card-footer">
            <button class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
        <?php endif; ?>
    </div>
</form>
<?php endif; ?>
</div>
<?php include "../../includes/footer.php"; ?>
