<?php
include "../../config/database.php";
include "../../includes/auth.php";

// Kepsek tidak bisa input/edit absensi
require_role(['Guru', 'Operator', 'TU']);

$id_kelas_pilihan = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
?>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/sidebar.php"; ?>

<div class="container-fluid">
    <h2 class="fw-bold mb-3">
         Absensi Harian
    </h2>

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php
                        $q_kelas = mysqli_query($conn, "SELECT * FROM kelas");
                        while($k = mysqli_fetch_array($q_kelas)){
                            $select = ($id_kelas_pilihan == $k['id_kelas']) ? 'selected' : '';
                            echo "<option value='".$k['id_kelas']."' $select>".$k['nama_kelas']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Tampilkan
                    </button>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <a href="rekap.php" class="btn btn-success w-100">
                        <i class="fa-solid fa-chart-column"></i> Rekap
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php if($id_kelas_pilihan): ?>
    <form action="proses_absensi.php" method="POST">

        <div class="card mb-3">
            <div class="card-body">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control w-25" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $sql = "SELECT r.id_riwayat, s.nama_lengkap 
                                FROM riwayat_kelas r 
                                JOIN siswa s ON r.nisn = s.nisn 
                                WHERE r.id_kelas = '$id_kelas_pilihan'
                                ORDER BY s.nama_lengkap ASC";

                        $query = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($query) == 0){
                            echo "<tr><td colspan='4' class='text-center'>Tidak ada siswa</td></tr>";
                        } else {
                            while($d = mysqli_fetch_array($query)){
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d['nama_lengkap'] ?></td>
                            <td>
                                <select name="status[<?= $d['id_riwayat'] ?>]" class="form-select">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Alpha">Alpha</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="keterangan[<?= $d['id_riwayat'] ?>]" class="form-control">
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <button type="submit" name="simpan" class="btn btn-success">
                    <i class="fa-solid fa-save"></i> Simpan Absensi
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include "../../includes/footer.php"; ?>
