<?php
include "../../config/database.php";
// include "../../includes/auth.php";
// require_role(['Guru','Operator']);

$id_kelas_pilihan = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
?>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/sidebar.php"; ?>

<div class="container-fluid">
    <h2 class="fw-bold mb-3">
        <i class="fa-solid fa-chart-bar"></i> Rekap Absensi
    </h2>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php
                        $q_kelas = mysqli_query($conn, "SELECT * FROM kelas");
                        while($k = mysqli_fetch_array($q_kelas)){
                            $sel = ($id_kelas_pilihan == $k['id_kelas']) ? 'selected' : '';
                            echo "<option value='".$k['id_kelas']."' $sel>".$k['nama_kelas']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </div>

                <?php if($id_kelas_pilihan): ?>
                <div class="col-md-3">
                    <a href="export_excel.php?id_kelas=<?= $id_kelas_pilihan ?>" class="btn btn-success w-100">
                        <i class="fa-solid fa-file-excel"></i> Export
                    </a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if($id_kelas_pilihan): ?>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>H</th>
                        <th>I</th>
                        <th>S</th>
                        <th>A</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no=1;
                $sql = "SELECT s.nama_lengkap, 
                        SUM(CASE WHEN a.status='Hadir' THEN 1 ELSE 0 END) h,
                        SUM(CASE WHEN a.status='Izin' THEN 1 ELSE 0 END) i,
                        SUM(CASE WHEN a.status='Sakit' THEN 1 ELSE 0 END) s,
                        SUM(CASE WHEN a.status='Alpha' THEN 1 ELSE 0 END) a,
                        COUNT(a.status) total
                        FROM siswa s
                        JOIN riwayat_kelas r ON s.nisn=r.nisn
                        LEFT JOIN absensi a ON r.id_riwayat=a.id_riwayat
                        WHERE r.id_kelas='$id_kelas_pilihan'
                        GROUP BY s.nisn";

                $q = mysqli_query($conn,$sql);
                while($d=mysqli_fetch_array($q)){
                ?>
                    <tr class="text-center">
                        <td><?= $no++ ?></td>
                        <td class="text-start"><?= $d['nama_lengkap'] ?></td>
                        <td><?= $d['h'] ?></td>
                        <td><?= $d['i'] ?></td>
                        <td><?= $d['s'] ?></td>
                        <td><?= $d['a'] ?></td>
                        <td><?= $d['total'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include "../../includes/footer.php"; ?>