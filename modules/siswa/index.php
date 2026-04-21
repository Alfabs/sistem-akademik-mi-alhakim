<?php

require_once '../../config/database.php';
// require_once '../../includes/auth.php';


// AMBIL DATA FILTER: Tahun Ajaran & Kelas

$filter_ta   = isset($_GET['id_ta'])    ? (int)$_GET['id_ta']    : 0;
$filter_kelas = isset($_GET['id_kelas']) ? (int)$_GET['id_kelas'] : 0;

// Ambil semua tahun ajaran untuk dropdown
$query_ta = "SELECT id_ta, tahun, semester, status_aktif FROM tahun_ajaran ORDER BY id_ta DESC";
$result_ta = mysqli_query($conn, $query_ta);
$list_ta = [];
$default_ta = 0;
while ($row = mysqli_fetch_assoc($result_ta)) {
    $list_ta[] = $row;
    if ($row['status_aktif'] == 1 && $default_ta == 0) {
        $default_ta = $row['id_ta'];
    }
}
if ($filter_ta == 0 && $default_ta != 0) $filter_ta = $default_ta;

// Ambil semua kelas untuk dropdown
$query_kelas = "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
$result_kelas = mysqli_query($conn, $query_kelas);
$list_kelas = [];
while ($row = mysqli_fetch_assoc($result_kelas)) {
    $list_kelas[] = $row;
}

// 
// QUERY DATA SISWA berdasarkan filter
// 
$where_parts = [];
$params      = [];
$types       = '';

$sql = "
    SELECT
        s.nisn,
        s.nama_lengkap,
        s.jenis_kelamin,
        s.tgl_lahir,
        s.alamat,
        k.nama_kelas,
        ta.tahun,
        ta.semester
    FROM riwayat_kelas rk
    JOIN siswa s  ON rk.nisn      = s.nisn
    JOIN kelas k  ON rk.id_kelas  = k.id_kelas
    JOIN tahun_ajaran ta ON rk.id_ta = ta.id_ta
    WHERE 1=1
";

if ($filter_ta > 0) {
    $sql .= " AND rk.id_ta = ?";
    $params[] = $filter_ta;
    $types   .= 'i';
}
if ($filter_kelas > 0) {
    $sql .= " AND rk.id_kelas = ?";
    $params[] = $filter_kelas;
    $types   .= 'i';
}

$sql .= " ORDER BY s.nama_lengkap ASC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result_siswa = mysqli_stmt_get_result($stmt);

$data_siswa = [];
while ($row = mysqli_fetch_assoc($result_siswa)) {
    $data_siswa[] = $row;
}
$total_siswa = count($data_siswa);

// 
// HAPUS SISWA (jika ada aksi hapus)
// 
$pesan = '';
$tipe_pesan = '';
if (isset($_GET['hapus']) && !empty($_GET['hapus'])) {
    $nisn_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    // Hapus dari tabel siswa (CASCADE akan hapus riwayat_kelas, absensi, nilai terkait)
    $del = mysqli_query($conn, "DELETE FROM siswa WHERE nisn='$nisn_hapus'");
    if ($del) {
        $pesan = "Data siswa dengan NISN <strong>$nisn_hapus</strong> berhasil dihapus.";
        $tipe_pesan = 'success';
    } else {
        $pesan = "Gagal menghapus data siswa.";
        $tipe_pesan = 'danger';
    }
    // Redirect untuk menghindari resubmit
    header("Location: index.php?id_ta=$filter_ta&id_kelas=$filter_kelas&msg=" . urlencode($pesan) . "&type=$tipe_pesan");
    exit;
}
if (isset($_GET['msg'])) {
    $pesan = $_GET['msg'];
    $tipe_pesan = $_GET['type'] ?? 'info';
}

// 
// Ambil label tahun ajaran yang dipilih (untuk judul/export)

$label_ta = '';
foreach ($list_ta as $ta) {
    if ($ta['id_ta'] == $filter_ta) {
        $label_ta = $ta['tahun'] . ' - ' . $ta['semester'];
        break;
    }
}
$label_kelas = '';
foreach ($list_kelas as $kl) {
    if ($kl['id_kelas'] == $filter_kelas) {
        $label_kelas = $kl['nama_kelas'];
        break;
    }
}
?>

<?php

include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<!-- 
     KONTEN UTAMA: DATA SISWA
      -->
<div class="content-wrapper">

    <!-- Page Title -->
    <div class="page-header">
        <h2 class="page-title">Data Siswa</h2>
    </div>

    <!-- Alert / Notifikasi -->
    <?php if ($pesan): ?>
    <div class="alert alert-<?= htmlspecialchars($tipe_pesan) ?> alert-dismissible">
        <span><?= $pesan ?></span>
        <button class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
    <?php endif; ?>

    <!-- Filter Form -->
    <div class="filter-card">
        <form method="GET" action="index.php" class="filter-form">
            <div class="filter-group">
                <label class="filter-label">Tahun Ajaran</label>
                <select name="id_ta" class="filter-select">
                    <option value="0">-- Semua --</option>
                    <?php foreach ($list_ta as $ta): ?>
                    <option value="<?= $ta['id_ta'] ?>"
                        <?= ($ta['id_ta'] == $filter_ta) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ta['tahun']) ?> (<?= $ta['semester'] ?>)
                        <?= $ta['status_aktif'] ? ' ✓' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Kelas</label>
                <select name="id_kelas" class="filter-select">
                    <option value="0">-- Semua Kelas --</option>
                    <?php foreach ($list_kelas as $kl): ?>
                    <option value="<?= $kl['id_kelas'] ?>"
                        <?= ($kl['id_kelas'] == $filter_kelas) ? 'selected' : '' ?>>
                        Kelas <?= htmlspecialchars($kl['nama_kelas']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-search">Cari</button>
        </form>
    </div>

    <!-- Tabel Siswa -->
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Jenis Kelamin</th>
                    <th>Tgl Lahir</th>
                    <th>Alamat</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data_siswa)): ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        Tidak ada data siswa ditemukan.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($data_siswa as $i => $s): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($s['nisn']) ?></td>
                    <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                    <td><?= htmlspecialchars($s['jenis_kelamin']) ?></td>
                    <td>
                        <?php
                        if ($s['tgl_lahir']) {
                            $d = new DateTime($s['tgl_lahir']);
                            echo $d->format('d/m/Y');
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td class="alamat-cell"><?= htmlspecialchars($s['alamat'] ?? '-') ?></td>
                    <td class="aksi-cell">
                        <a href="lihat.php?nisn=<?= urlencode($s['nisn']) ?>&id_ta=<?= $filter_ta ?>"
                           class="btn-aksi btn-lihat">Lihat</a>
                        <a href="edit.php?nisn=<?= urlencode($s['nisn']) ?>&id_ta=<?= $filter_ta ?>"
                           class="btn-aksi btn-edit">Edit</a>
                        <a href="index.php?hapus=<?= urlencode($s['nisn']) ?>&id_ta=<?= $filter_ta ?>&id_kelas=<?= $filter_kelas ?>"
                           class="btn-aksi btn-hapus"
                           onclick="return confirm('Yakin ingin menghapus siswa <?= htmlspecialchars(addslashes($s['nama_lengkap'])) ?>?\nSemua data absensi dan nilai terkait juga akan terhapus!')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Action Bar Bawah -->
    <div class="bottom-actions">
        <div class="bottom-left">
            <a href="cetak_pdf.php?id_ta=<?= $filter_ta ?>&id_kelas=<?= $filter_kelas ?>"
               target="_blank" class="btn btn-pdf">
                Cetak PDF
            </a>
            <a href="import_excel.php" class="btn btn-import">
                Import
            </a>
            <a href="export_excel.php?id_ta=<?= $filter_ta ?>&id_kelas=<?= $filter_kelas ?>"
               class="btn btn-export">
                Export .xlsx
            </a>
        </div>
        <div class="bottom-right">
            <a href="tambah.php?id_ta=<?= $filter_ta ?>&id_kelas=<?= $filter_kelas ?>"
               class="btn btn-tambah">
                Tambah Siswa
            </a>
        </div>
    </div>

</div><!-- /.content-wrapper -->




</div>
</div>
</div>

</body>
</html>