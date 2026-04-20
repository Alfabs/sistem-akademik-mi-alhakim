<?php

require_once '../../config/database.php';
require_once '../../includes/auth.php';

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

<!--
     KONTEN: FORM TAMBAH SISWA
     -->
<div class="content-wrapper">

    <div class="page-header">
        <h2 class="page-title">Tambah Siswa Baru</h2>
        <a href="index.php?id_ta=<?= $default_ta ?>&id_kelas=<?= $default_kelas ?>"
           class="btn btn-back">← Kembali</a>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($error as $e): ?>
            <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="tambah.php?id_ta=<?= $default_ta ?>&id_kelas=<?= $default_kelas ?>">

            <!-- SECTION: Data Pribadi -->
            <div class="form-section">
                <h3 class="form-section-title">Data Pribadi</h3>
                <div class="form-grid">

                    <div class="form-group">
                        <label for="nisn">NISN <span class="required">*</span></label>
                        <input type="text" id="nisn" name="nisn"
                               value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>"
                               placeholder="Contoh: 1234567890"
                               maxlength="20" class="form-control">
                        <small class="form-hint">Nomor Induk Siswa Nasional (angka, 8–20 digit)</small>
                    </div>

                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                               value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>"
                               placeholder="Nama sesuai akta lahir"
                               maxlength="100" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir"
                               value="<?= htmlspecialchars($_POST['tempat_lahir'] ?? '') ?>"
                               placeholder="Kota/Kabupaten"
                               maxlength="50" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir <span class="required">*</span></label>
                        <input type="date" id="tgl_lahir" name="tgl_lahir"
                               value="<?= htmlspecialchars($_POST['tgl_lahir'] ?? '') ?>"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki"  <?= (($_POST['jenis_kelamin'] ?? '') === 'Laki-laki')  ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan"  <?= (($_POST['jenis_kelamin'] ?? '') === 'Perempuan')  ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asal_sekolah">Asal Sekolah</label>
                        <input type="text" id="asal_sekolah" name="asal_sekolah"
                               value="<?= htmlspecialchars($_POST['asal_sekolah'] ?? '') ?>"
                               placeholder="Nama SD/MI asal"
                               maxlength="100" class="form-control">
                    </div>

                    <div class="form-group form-group-full">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3"
                                  placeholder="Alamat lengkap siswa"
                                  class="form-control"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                    </div>

                </div>
            </div>

            <!-- SECTION: Penempatan Kelas -->
            <div class="form-section">
                <h3 class="form-section-title">Penempatan Kelas</h3>
                <div class="form-grid">

                    <div class="form-group">
                        <label for="id_ta">Tahun Ajaran <span class="required">*</span></label>
                        <select id="id_ta" name="id_ta" class="form-control">
                            <option value="0">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($list_ta as $ta): ?>
                            <option value="<?= $ta['id_ta'] ?>"
                                <?= ($ta['id_ta'] == $default_ta) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ta['tahun']) ?> (<?= $ta['semester'] ?>)
                                <?= $ta['status_aktif'] ? ' — Aktif' : '' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_kelas">Kelas <span class="required">*</span></label>
                        <select id="id_kelas" name="id_kelas" class="form-control">
                            <option value="0">-- Pilih Kelas --</option>
                            <?php foreach ($list_kelas as $kl): ?>
                            <option value="<?= $kl['id_kelas'] ?>"
                                <?= ($kl['id_kelas'] == $default_kelas) ? 'selected' : '' ?>>
                                Kelas <?= htmlspecialchars($kl['nama_kelas']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="form-actions">
                <a href="index.php?id_ta=<?= $default_ta ?>&id_kelas=<?= $default_kelas ?>"
                   class="btn btn-cancel">Batal</a>
                <button type="submit" class="btn btn-simpan">💾 Simpan Data Siswa</button>
            </div>

        </form>
    </div>

</div>

<?php
include_once '../../includes/footer.php';
?>