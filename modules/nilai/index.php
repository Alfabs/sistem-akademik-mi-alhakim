<?php
// 1. Koneksi ke database dan cek session login (auth)
include "../../config/database.php";
include "../../includes/auth.php";   // Memastikan user sudah login
require_role(['Guru', 'Operator']); // Hanya Guru & Operator yang boleh akses

// 2. Ambil nilai dari URL (filter yang dipilih user)
$id_kelas   = isset($_GET['id_kelas'])   ? (int)$_GET['id_kelas']   : 0;
$kode_mapel = isset($_GET['kode_mapel']) ? $_GET['kode_mapel']       : '';
$jenis_nilai= isset($_GET['jenis_nilai'])? $_GET['jenis_nilai']      : 'UH';

// 3. Ambil data kelas dari database untuk dropdown
$query_kelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");

// 4. Ambil data mata pelajaran untuk dropdown
$query_mapel = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel ORDER BY nama_mapel");

// 5. Sementara, kita pakai NIP guru dummy (karena login masih sederhana). Nanti bisa diganti dengan NIP dari session.
$nip = '198001012010011001'; // Ganti nanti sesuai guru yang login

// 6. Tampilkan pesan sukses/gagal jika ada
$pesan = '';
if (isset($_GET['msg'])) $pesan = $_GET['msg'];
?>

<!-- 7. Mulai tampilan HTML dengan memanggil header dan sidebar -->
<?php include "../../includes/header.php"; ?>
<?php include "../../includes/sidebar.php"; ?>

<div class="container-fluid">
    <h2 class="fw-bold mb-3">📝 Input Nilai Siswa</h2>

    <?php if ($pesan): ?>
        <div class="alert alert-info"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <!-- Form Filter Kelas, Mapel, Jenis Nilai -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while($kelas = mysqli_fetch_assoc($query_kelas)): ?>
                            <option value="<?= $kelas['id_kelas'] ?>" <?= ($id_kelas == $kelas['id_kelas']) ? 'selected' : '' ?>>
                                <?= $kelas['nama_kelas'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="kode_mapel" class="form-select" required>
                        <option value="">-- Pilih Mapel --</option>
                        <?php 
                        // Reset pointer query_mapel karena sudah terpakai di atas
                        mysqli_data_seek($query_mapel, 0);
                        while($mapel = mysqli_fetch_assoc($query_mapel)): ?>
                            <option value="<?= $mapel['kode_mapel'] ?>" <?= ($kode_mapel == $mapel['kode_mapel']) ? 'selected' : '' ?>>
                                <?= $mapel['nama_mapel'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jenis Nilai</label>
                    <select name="jenis_nilai" class="form-select">
                        <option value="UH" <?= $jenis_nilai == 'UH' ? 'selected' : '' ?>>Ulangan Harian (UH)</option>
                        <option value="UTS" <?= $jenis_nilai == 'UTS' ? 'selected' : '' ?>>UTS</option>
                        <option value="UAS" <?= $jenis_nilai == 'UAS' ? 'selected' : '' ?>>UAS</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan Siswa</button>
                </div>
            </form>
        </div>
    </div>

    <?php 
    // Jika filter sudah dipilih (kelas dan mapel tidak kosong), tampilkan form input nilai
    if ($id_kelas && $kode_mapel): 
    ?>
    <form action="proses_input.php" method="POST">
        <!-- Kirim data tambahan via hidden input -->
        <input type="hidden" name="kode_mapel" value="<?= $kode_mapel ?>">
        <input type="hidden" name="jenis_nilai" value="<?= $jenis_nilai ?>">
        <input type="hidden" name="nip" value="<?= $nip ?>">

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Nilai (0-100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query ambil daftar siswa berdasarkan kelas yang dipilih
                        $query_siswa = mysqli_query($conn, "
                            SELECT r.id_riwayat, s.nisn, s.nama_lengkap
                            FROM riwayat_kelas r
                            JOIN siswa s ON r.nisn = s.nisn
                            WHERE r.id_kelas = $id_kelas
                            ORDER BY s.nama_lengkap
                        ");
                        if (mysqli_num_rows($query_siswa) == 0) {
                            echo "<tr><td colspan='4' class='text-center'>Tidak ada siswa di kelas ini</td></tr>";
                        } else {
                            $no = 1;
                            while($siswa = mysqli_fetch_assoc($query_siswa)):
                                // Cek apakah nilai sudah pernah diinput sebelumnya
                                $cek_nilai = mysqli_query($conn, "
                                    SELECT nilai_angka FROM nilai 
                                    WHERE id_riwayat = {$siswa['id_riwayat']} 
                                      AND kode_mapel = '$kode_mapel' 
                                      AND jenis_nilai = '$jenis_nilai'
                                ");
                                $nilai_existing = mysqli_fetch_assoc($cek_nilai);
                                $nilai_value = $nilai_existing ? $nilai_existing['nilai_angka'] : '';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $siswa['nisn'] ?></td>
                            <td><?= $siswa['nama_lengkap'] ?></td>
                            <td>
                                <input type="hidden" name="id_riwayat[]" value="<?= $siswa['id_riwayat'] ?>">
                                <input type="number" name="nilai[]" class="form-control" step="0.01" min="0" max="100" value="<?= $nilai_value ?>" required>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">💾 Simpan Semua Nilai</button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include "../../includes/footer.php"; ?>