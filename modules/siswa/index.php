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
<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Data Siswa</h4>
        <a href="<?= BASE_URL ?>modules/siswa/tambah.php"
           class="btn btn-primary">
            + Tambah Siswa
        </a>
    </div>

    <!-- ALERT -->
    <?php if ($pesan): ?>
        <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show">
            <?= $pesan ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="id_ta" class="form-select">
                        <option value="0">Semua</option>
                        <?php foreach ($list_ta as $ta): ?>
                            <option value="<?= $ta['id_ta'] ?>" <?= $ta['id_ta']==$filter_ta?'selected':'' ?>>
                                <?= $ta['tahun'] ?> (<?= $ta['semester'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select">
                        <option value="0">Semua</option>
                        <?php foreach ($list_kelas as $kl): ?>
                            <option value="<?= $kl['id_kelas'] ?>" <?= $kl['id_kelas']==$filter_kelas?'selected':'' ?>>
                                <?= $kl['nama_kelas'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-success w-100">Filter</button>
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
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Tgl Lahir</th>
                        <th>Alamat</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data_siswa)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data_siswa as $i => $s): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= $s['nisn'] ?></td>
                            <td><?= $s['nama_lengkap'] ?></td>
                            <td><?= $s['jenis_kelamin'] ?></td>
                            <td>
                                <?= $s['tgl_lahir'] ? date('d/m/Y', strtotime($s['tgl_lahir'])) : '-' ?>
                            </td>
                            <td><?= $s['alamat'] ?></td>
                            <td>
                                <a style="color: white;" href="detail.php?nisn=<?= $s['nisn'] ?>"
                                    class="btn btn-success btn-sm py-0 px-2">
                                    Lihat
                                </a>

                                <a style="color: white;" href="edit.php?nisn=<?= $s['nisn'] ?>&id_ta=<?= $filter_ta ?>"
                                   class="btn btn-warning btn-sm py-0 px-2">Edit</a>

                                <a href="index.php?hapus=<?= $s['nisn'] ?>&id_ta=<?= $filter_ta ?>&id_kelas=<?= $filter_kelas ?>"
                                   class="btn btn-danger btn-sm py-0 px-2"
                                   onclick="return confirm('Yakin hapus?')">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- ACTION -->
    <div class="d-flex justify-content-between mt-3">

        <div>
            <a href="cetak_pdf.php?id_ta=<?= $filter_ta ?>&id_kelas=<?= $filter_kelas ?>"
               class="btn btn-secondary btn-sm" target="_blank">
                Cetak PDF
            </a>

            <a href="export_excel.php?id_ta=<?= $filter_ta ?>&id_kelas=<?= $filter_kelas ?>"
               class="btn btn-success btn-sm">
                Export Excel
            </a>
        </div>

        <div>
            <a href="import_excel.php" class="btn btn-primary btn-sm">
                Import
            </a>
        </div>

    </div>

</div>

<?php
include_once '../../includes/footer.php';
