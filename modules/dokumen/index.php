<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

check_login();

$role = $_SESSION['role'];
$nip_user = $_SESSION['nip'] ?? '';

$where_clause = ""; 
$mapel_terpilih = "";

if (isset($_GET['mapel']) && $_GET['mapel'] != '') {
    $mapel_terpilih = mysqli_real_escape_string($conn, $_GET['mapel']);
    $where_clause = "WHERE d.kode_mapel = '$mapel_terpilih'";
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Dokumen Pembelajaran</h4>
        <?php if ($role === 'Guru'): ?>
        <a href="upload.php" class="btn btn-success">
            <i class="fa-solid fa-upload"></i> Unggah Dokumen
        </a>
        <?php endif; ?>
    </div>

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="mapel" class="form-select">
                        <option value="">Semua</option>
                        <?php
                        $mapel_query = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel");
                        while ($m = mysqli_fetch_assoc($mapel_query)) {
                            $selected = ($m['kode_mapel'] == $mapel_terpilih) ? 'selected' : '';
                            echo "<option value='{$m['kode_mapel']}' $selected>{$m['nama_mapel']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Jenis</th>
                        <th>Mapel</th>
                        <th>Pengunggah</th>
                        <th>Tanggal</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT d.*, g.nama_lengkap, m.nama_mapel 
                              FROM dokumen d 
                              JOIN guru g ON d.nip = g.nip 
                              JOIN mapel m ON d.kode_mapel = m.kode_mapel 
                              $where_clause
                              ORDER BY d.tgl_unggah DESC";

                    $result = mysqli_query($conn, $query);
                    $no = 1;

                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>

                        <td 
                            style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                            title="<?= $row['nama_file'] ?>"
                        >
                            <?= $row['nama_file'] ?>
                        </td>

                        <td>
                            <span class="badge bg-secondary"><?= $row['jenis_file'] ?></span>
                        </td>
                        <td><?= $row['nama_mapel'] ?></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['tgl_unggah'])) ?></td>
                        <td>
                            <a href="download.php?id=<?= $row['id_dokumen'] ?>" 
                               class="btn btn-success btn-sm py-0 px-2">
                               Download
                            </a>

                            <?php if ($role === 'Operator' || ($role === 'Guru' && $row['nip'] === $nip_user)): ?>
                            <a href="hapus.php?id=<?= $row['id_dokumen'] ?>" 
                               class="btn btn-danger btn-sm py-0 px-2"
                               onclick="return confirm('Yakin hapus?')">
                               Hapus
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada dokumen</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>