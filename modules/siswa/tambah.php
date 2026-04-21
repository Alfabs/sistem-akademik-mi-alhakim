<?php

require_once '../../config/database.php';
// require_once '../../includes/auth.php';

$error   = [];
$success = '';

// Ambil daftar kelas & tahun ajaran untuk dropdown
$list_kelas = [];
$res_kelas  = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC");
while ($row = mysqli_fetch_assoc($res_kelas)) $list_kelas[] = $row;

$list_ta  = [];
$res_ta   = mysqli_query($conn, "SELECT id_ta, tahun, semester, status_aktif FROM tahun_ajaran ORDER BY id_ta DESC");
while ($row = mysqli_fetch_assoc($res_ta)) $list_ta[] = $row;

// Default: ambil id_ta & id_kelas dari URL (kalau user klik tombol dari index)
$default_ta    = isset($_GET['id_ta'])    ? (int)$_GET['id_ta']    : 0;
$default_kelas = isset($_GET['id_kelas']) ? (int)$_GET['id_kelas'] : 0;

//
// PROSES SIMPAN DATA
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitasi input
    $nisn         = trim(mysqli_real_escape_string($conn, $_POST['nisn']         ?? ''));
    $nama_lengkap = trim(mysqli_real_escape_string($conn, $_POST['nama_lengkap'] ?? ''));
    $tempat_lahir = trim(mysqli_real_escape_string($conn, $_POST['tempat_lahir'] ?? ''));
    $tgl_lahir    = trim($_POST['tgl_lahir']    ?? '');
    $jenis_kelamin= $_POST['jenis_kelamin'] ?? '';
    $alamat       = trim(mysqli_real_escape_string($conn, $_POST['alamat']       ?? ''));
    $asal_sekolah = trim(mysqli_real_escape_string($conn, $_POST['asal_sekolah'] ?? ''));
    $id_kelas     = (int)($_POST['id_kelas']  ?? 0);
    $id_ta        = (int)($_POST['id_ta']     ?? 0);

    // Validasi
    if (empty($nisn))          $error[] = 'NISN tidak boleh kosong.';
    if (!preg_match('/^\d{8,20}$/', $nisn)) $error[] = 'NISN harus berupa angka (8–20 digit).';
    if (empty($nama_lengkap))  $error[] = 'Nama lengkap tidak boleh kosong.';
    if (empty($tgl_lahir))     $error[] = 'Tanggal lahir tidak boleh kosong.';
    if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) $error[] = 'Jenis kelamin tidak valid.';
    if ($id_kelas === 0)       $error[] = 'Kelas harus dipilih.';
    if ($id_ta    === 0)       $error[] = 'Tahun ajaran harus dipilih.';

    // Cek NISN sudah ada
    if (empty($error)) {
        $cek = mysqli_query($conn, "SELECT nisn FROM siswa WHERE nisn='$nisn'");
        if (mysqli_num_rows($cek) > 0) {
            $error[] = "NISN <strong>$nisn</strong> sudah terdaftar di sistem.";
        }
    }

    // Simpan jika tidak ada error
    if (empty($error)) {
        mysqli_begin_transaction($conn);
        try {
            // 1. Insert ke tabel siswa
            $sql_siswa = "INSERT INTO siswa (nisn, nama_lengkap, tempat_lahir, tgl_lahir, jenis_kelamin, alamat, asal_sekolah)
                          VALUES ('$nisn', '$nama_lengkap', '$tempat_lahir', '$tgl_lahir', '$jenis_kelamin', '$alamat', '$asal_sekolah')";
            if (!mysqli_query($conn, $sql_siswa)) throw new Exception(mysqli_error($conn));

            // 2. Insert ke riwayat_kelas
            $sql_riwayat = "INSERT INTO riwayat_kelas (nisn, id_kelas, id_ta)
                            VALUES ('$nisn', $id_kelas, $id_ta)";
            if (!mysqli_query($conn, $sql_riwayat)) throw new Exception(mysqli_error($conn));

            mysqli_commit($conn);
            header("Location: index.php?id_ta=$id_ta&id_kelas=$id_kelas&msg=" . urlencode("Siswa <strong>$nama_lengkap</strong> berhasil ditambahkan.") . "&type=success");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error[] = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }

    // Kembalikan nilai POST ke form jika ada error
    $default_ta    = $id_ta;
    $default_kelas = $id_kelas;
}
?>

<?php
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Tambah Siswa Baru</h2>

        <a href="index.php?id_ta=<?= $default_ta ?>&id_kelas=<?= $default_kelas ?>"
           class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    <!-- ERROR -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-4 shadow-sm">
            <ul class="mb-0 ps-3">
                <?php foreach($error as $e): ?>
                    <li><?= $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <form method="POST"
                  action="tambah.php?id_ta=<?= $default_ta ?>&id_kelas=<?= $default_kelas ?>">

                <!-- DATA PRIBADI -->
                <h5 class="fw-bold mb-3 text-success">Data Pribadi</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">NISN *</label>
                        <input type="text"
                               name="nisn"
                               maxlength="20"
                               class="form-control"
                               value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>">
                        <small class="text-muted">8–20 digit angka</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text"
                               name="nama_lengkap"
                               class="form-control"
                               value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text"
                               name="tempat_lahir"
                               class="form-control"
                               value="<?= htmlspecialchars($_POST['tempat_lahir'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir *</label>
                        <input type="date"
                               name="tgl_lahir"
                               class="form-control"
                               value="<?= htmlspecialchars($_POST['tgl_lahir'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="">Pilih</option>

                            <option value="Laki-laki"
                                <?= (($_POST['jenis_kelamin'] ?? '')=='Laki-laki') ? 'selected':'' ?>>
                                Laki-laki
                            </option>

                            <option value="Perempuan"
                                <?= (($_POST['jenis_kelamin'] ?? '')=='Perempuan') ? 'selected':'' ?>>
                                Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Asal Sekolah</label>
                        <input type="text"
                               name="asal_sekolah"
                               class="form-control"
                               value="<?= htmlspecialchars($_POST['asal_sekolah'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat"
                                  rows="3"
                                  class="form-control"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                    </div>

                </div>

                <!-- PENEMPATAN -->
                <h5 class="fw-bold mb-3 text-success">Penempatan Kelas</h5>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Tahun Ajaran *</label>
                        <select name="id_ta" class="form-select">
                            <option value="0">Pilih Tahun Ajaran</option>

                            <?php foreach($list_ta as $ta): ?>
                            <option value="<?= $ta['id_ta'] ?>"
                                <?= ($ta['id_ta']==$default_ta)?'selected':'' ?>>

                                <?= $ta['tahun'] ?> (<?= $ta['semester'] ?>)
                                <?= $ta['status_aktif'] ? ' - Aktif' : '' ?>

                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kelas *</label>
                        <select name="id_kelas" class="form-select">
                            <option value="0">Pilih Kelas</option>

                            <?php foreach($list_kelas as $kl): ?>
                            <option value="<?= $kl['id_kelas'] ?>"
                                <?= ($kl['id_kelas']==$default_kelas)?'selected':'' ?>>

                                <?= $kl['nama_kelas'] ?>

                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-4 d-flex justify-content-end gap-2">

                    <a href="index.php?id_ta=<?= $default_ta ?>&id_kelas=<?= $default_kelas ?>"
                       class="btn btn-light border px-4">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-success px-4">
                        Simpan Data
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

<?php include_once '../../includes/footer.php'; ?>