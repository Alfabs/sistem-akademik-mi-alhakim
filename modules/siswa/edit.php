    <?php
    require_once '../../config/database.php';
    // require_once '../../includes/auth.php';

    $error   = [];
    $success = '';

    // AMBIL NISN DARI URL
    $nisn = trim($_GET['nisn'] ?? '');
    $back_ta    = (int)($_GET['id_ta']    ?? 0);
    $back_kelas = (int)($_GET['id_kelas'] ?? 0);

    if (empty($nisn)) {
        header("Location: index.php");
        exit;
    }

    // AMBIL DATA SISWA YANG AKAN DIEDIT
    $nisn_esc = mysqli_real_escape_string($conn, $nisn);
    $res = mysqli_query($conn, "SELECT * FROM siswa WHERE nisn='$nisn_esc'");
    if (mysqli_num_rows($res) === 0) {
        header("Location: index.php?msg=" . urlencode("Siswa tidak ditemukan.") . "&type=danger");
        exit;
    }
    $siswa = mysqli_fetch_assoc($res);

    // Ambil riwayat kelas siswa ini (untuk edit penempatan kelas)
    $res_riwayat = mysqli_query($conn, "
        SELECT rk.id_riwayat, rk.id_kelas, rk.id_ta
        FROM riwayat_kelas rk
        WHERE rk.nisn = '$nisn_esc'
        ORDER BY rk.id_ta DESC
        LIMIT 1
    ");
    $riwayat = mysqli_fetch_assoc($res_riwayat);
    $id_riwayat    = $riwayat['id_riwayat'] ?? null;
    $current_kelas = $riwayat['id_kelas']   ?? 0;
    $current_ta    = $riwayat['id_ta']      ?? $back_ta;

    // Ambil daftar kelas & tahun ajaran untuk dropdown
    $list_kelas = [];
    $res_kelas  = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC");
    while ($row = mysqli_fetch_assoc($res_kelas)) $list_kelas[] = $row;

    $list_ta  = [];
    $res_ta   = mysqli_query($conn, "SELECT id_ta, tahun, semester, status_aktif FROM tahun_ajaran ORDER BY id_ta DESC");
    while ($row = mysqli_fetch_assoc($res_ta)) $list_ta[] = $row;

    // PROSES UPDATE DATA
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Sanitasi input
        $nama_lengkap = trim(mysqli_real_escape_string($conn, $_POST['nama_lengkap'] ?? ''));
        $tempat_lahir = trim(mysqli_real_escape_string($conn, $_POST['tempat_lahir'] ?? ''));
        $tgl_lahir    = trim($_POST['tgl_lahir']    ?? '');
        $jenis_kelamin= $_POST['jenis_kelamin'] ?? '';
        $alamat       = trim(mysqli_real_escape_string($conn, $_POST['alamat']       ?? ''));
        $asal_sekolah = trim(mysqli_real_escape_string($conn, $_POST['asal_sekolah'] ?? ''));
        $id_kelas_baru= (int)($_POST['id_kelas']  ?? 0);
        $id_ta_baru   = (int)($_POST['id_ta']     ?? 0);

        // Validasi
        if (empty($nama_lengkap))  $error[] = 'Nama lengkap tidak boleh kosong.';
        if (empty($tgl_lahir))     $error[] = 'Tanggal lahir tidak boleh kosong.';
        if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) $error[] = 'Jenis kelamin tidak valid.';
        if ($id_kelas_baru === 0)  $error[] = 'Kelas harus dipilih.';
        if ($id_ta_baru    === 0)  $error[] = 'Tahun ajaran harus dipilih.';

        if (empty($error)) {
            mysqli_begin_transaction($conn);
            try {
                // 1. Update tabel siswa
                $sql_update = "
                    UPDATE siswa SET
                        nama_lengkap   = '$nama_lengkap',
                        tempat_lahir   = '$tempat_lahir',
                        tgl_lahir      = '$tgl_lahir',
                        jenis_kelamin  = '$jenis_kelamin',
                        alamat         = '$alamat',
                        asal_sekolah   = '$asal_sekolah'
                    WHERE nisn = '$nisn_esc'
                ";
                if (!mysqli_query($conn, $sql_update)) throw new Exception(mysqli_error($conn));

                // 2. Update / insert riwayat_kelas
                if ($id_riwayat) {
                    // Cek apakah kombinasi baru sudah ada di riwayat lain (bukan id_riwayat ini)
                    $cek_dup = mysqli_query($conn, "
                        SELECT id_riwayat FROM riwayat_kelas
                        WHERE nisn='$nisn_esc' AND id_kelas=$id_kelas_baru AND id_ta=$id_ta_baru
                        AND id_riwayat != $id_riwayat
                    ");
                    if (mysqli_num_rows($cek_dup) > 0) {
                        throw new Exception("Siswa sudah terdaftar di kelas dan tahun ajaran yang dipilih.");
                    }
                    $sql_riwayat = "
                        UPDATE riwayat_kelas SET
                            id_kelas = $id_kelas_baru,
                            id_ta    = $id_ta_baru
                        WHERE id_riwayat = $id_riwayat
                    ";
                } else {
                    // Belum ada riwayat sama sekali, insert baru
                    $sql_riwayat = "
                        INSERT INTO riwayat_kelas (nisn, id_kelas, id_ta)
                        VALUES ('$nisn_esc', $id_kelas_baru, $id_ta_baru)
                    ";
                }
                if (!mysqli_query($conn, $sql_riwayat)) throw new Exception(mysqli_error($conn));

                mysqli_commit($conn);
                header("Location: index.php?id_ta=$id_ta_baru&id_kelas=$id_kelas_baru&msg=" . urlencode("Data siswa <strong>$nama_lengkap</strong> berhasil diperbarui.") . "&type=success");
                exit;

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error[] = 'Gagal menyimpan perubahan: ' . $e->getMessage();
            }
        }

        // Jika ada error, gunakan nilai POST (bukan dari DB)
        $siswa['nama_lengkap']  = $_POST['nama_lengkap']  ?? $siswa['nama_lengkap'];
        $siswa['tempat_lahir']  = $_POST['tempat_lahir']  ?? $siswa['tempat_lahir'];
        $siswa['tgl_lahir']     = $_POST['tgl_lahir']     ?? $siswa['tgl_lahir'];
        $siswa['jenis_kelamin'] = $_POST['jenis_kelamin'] ?? $siswa['jenis_kelamin'];
        $siswa['alamat']        = $_POST['alamat']        ?? $siswa['alamat'];
        $siswa['asal_sekolah']  = $_POST['asal_sekolah']  ?? $siswa['asal_sekolah'];
        $current_kelas = $id_kelas_baru;
        $current_ta    = $id_ta_baru;
    }
    ?>

    <?php
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Edit Data Siswa</h2>

        <a href="index.php?id_ta=<?= $back_ta ?>&id_kelas=<?= $back_kelas ?>"
           class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    <!-- NISN -->
    <div class="alert alert-info rounded-4 shadow-sm">
        NISN : <strong><?= htmlspecialchars($nisn) ?></strong>
        <small class="text-muted">(tidak dapat diubah)</small>
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
                  action="edit.php?nisn=<?= urlencode($nisn) ?>&id_ta=<?= $back_ta ?>&id_kelas=<?= $back_kelas ?>">

                <h5 class="fw-bold text-success mb-3">Data Pribadi</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap"
                               class="form-control"
                               value="<?= htmlspecialchars($siswa['nama_lengkap']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="Laki-laki"
                                <?= $siswa['jenis_kelamin']=='Laki-laki'?'selected':'' ?>>
                                Laki-laki
                            </option>

                            <option value="Perempuan"
                                <?= $siswa['jenis_kelamin']=='Perempuan'?'selected':'' ?>>
                                Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir"
                               class="form-control"
                               value="<?= htmlspecialchars($siswa['tempat_lahir']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir *</label>
                        <input type="date" name="tgl_lahir"
                               class="form-control"
                               value="<?= $siswa['tgl_lahir'] ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah"
                               class="form-control"
                               value="<?= htmlspecialchars($siswa['asal_sekolah']) ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat"
                                  rows="3"
                                  class="form-control"><?= htmlspecialchars($siswa['alamat']) ?></textarea>
                    </div>

                </div>

                <h5 class="fw-bold text-success mb-3">Penempatan Kelas</h5>

                <div class="alert alert-warning rounded-4">
                    Mengubah kelas / tahun ajaran akan memindahkan data siswa ke kelas baru.
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Tahun Ajaran *</label>
                        <select name="id_ta" class="form-select">

                            <?php foreach($list_ta as $ta): ?>
                            <option value="<?= $ta['id_ta'] ?>"
                                <?= $ta['id_ta']==$current_ta?'selected':'' ?>>
                                <?= $ta['tahun'] ?> (<?= $ta['semester'] ?>)
                            </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kelas *</label>
                        <select name="id_kelas" class="form-select">

                            <?php foreach($list_kelas as $kl): ?>
                            <option value="<?= $kl['id_kelas'] ?>"
                                <?= $kl['id_kelas']==$current_kelas?'selected':'' ?>>
                                <?= $kl['nama_kelas'] ?>
                            </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">

                    <a href="index.php?id_ta=<?= $back_ta ?>&id_kelas=<?= $back_kelas ?>"
                       class="btn btn-light border px-4">
                        Batal
                    </a>

                    <button class="btn btn-success px-4">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

<?php include_once '../../includes/footer.php'; ?>