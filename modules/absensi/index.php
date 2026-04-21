<?php
include "../../config/database.php"; 

// Ambil ID Kelas dari filter, jika tidak ada set default kosong
$id_kelas_pilihan = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Harian - MI Al-Hakim</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f9fafb;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg); 
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
        }

        .container { 
            max-width: 1100px; 
            margin: auto; 
            background: white; 
            padding: 32px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); 
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 15px;
        }

        h2 { margin: 0; font-size: 24px; font-weight: 700; color: var(--text-main); }

        .btn { 
            padding: 10px 20px; 
            border-radius: 8px; 
            border: none; 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-rekap { 
            background-color: #4f46e5 !important; 
            color: white !important; 
        }
        .btn-rekap:hover { 
            background-color: #4338ca !important; 
            transform: translateY(-1px); 
        }

        .filter-card { 
            background: #f8fafc; 
            padding: 24px; 
            border-radius: 12px; 
            margin-bottom: 24px; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            border: 1px solid #e2e8f0;
        }

        select, input[type="date"] { 
            padding: 10px 16px; 
            border-radius: 8px; 
            border: 1px solid #d1d5db; 
            outline: none;
            font-size: 14px;
            background: white;
        }

        .btn-tampil { 
            background-color: #4f46e5 !important; 
            color: white !important; 
        }
        .btn-tampil:hover { 
            background-color: #4338ca !important; 
        }

        .table-wrapper { overflow: hidden; border: 1px solid #e5e7eb; border-radius: 12px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f9fafb; padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid #e5e7eb; }
        td { padding: 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        tr:hover { background-color: #f8fafc; }

        .radio-group { display: flex; gap: 12px; }
        .radio-item { display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500; font-size: 13px; }

        .keterangan-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            outline: none;
        }
        .keterangan-input:focus { border-color: var(--primary); }

        .btn-save { 
            background: #10b981; 
            color: white; 
            width: 100%; 
            justify-content: center; 
            margin-top: 24px; 
            font-size: 16px; 
            padding: 14px;
        }
        .btn-save:hover { background: #059669; }

        .empty-state {
            text-align: center;
            padding: 60px;
            color: var(--text-muted);
            background: #f9fafb;
            border-radius: 12px;
            border: 2px dashed #e2e8f0;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h2>Absensi Harian</h2>
        <a href="rekap.php" class="btn btn-rekap">Lihat Rekap Per Siswa →</a>
    </header>

    <div class="filter-card">
        <form method="GET" action="" style="display:flex; gap:12px; align-items:center; width:100%;">
            <label><strong>Kelas:</strong></label>
            <select name="id_kelas" required>
                <option value="" disabled <?php echo ($id_kelas_pilihan == '') ? 'selected' : ''; ?> hidden>Pilih Kelas</option>
                <?php
                $q_kelas = mysqli_query($conn, "SELECT * FROM kelas");
                while($k = mysqli_fetch_array($q_kelas)){
                    $select = ($id_kelas_pilihan == $k['id_kelas']) ? 'selected' : '';
                    echo "<option value='".$k['id_kelas']."' $select>".$k['nama_kelas']."</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-tampil">Tampilkan Siswa</button>
        </form>
    </div>

    <?php if($id_kelas_pilihan): ?>
    <form action="proses_absensi.php" method="POST">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <label><strong>Tanggal Absen:</strong></label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Siswa</th>
                        <th>Status Kehadiran</th>
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
                        echo "<tr><td colspan='4' align='center' style='padding:40px;'>Tidak ada siswa di kelas ini.</td></tr>";
                    } else {
                        while($data = mysqli_fetch_array($query)){
                        ?>
                        <tr>
                            <td style="color: var(--text-muted);"><?= $no++ ?></td>
                            <td><strong><?= $data['nama_lengkap'] ?></strong></td>
                            <td>
                                <div class="radio-group">
                                    <label class="radio-item"><input type="radio" name="status[<?= $data['id_riwayat'] ?>]" value="Hadir" checked> Hadir</label>
                                    <label class="radio-item"><input type="radio" name="status[<?= $data['id_riwayat'] ?>]" value="Izin"> Izin</label>
                                    <label class="radio-item"><input type="radio" name="status[<?= $data['id_riwayat'] ?>]" value="Sakit"> Sakit</label>
                                    <label class="radio-item"><input type="radio" name="status[<?= $data['id_riwayat'] ?>]" value="Alpha"> Alpha</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="keterangan[<?= $data['id_riwayat'] ?>]" placeholder="Catatan..." class="keterangan-input">
                            </td>
                        </tr>
                        <?php 
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if(mysqli_num_rows($query) > 0): ?>
            <button type="submit" name="simpan" class="btn btn-save">✓ Simpan Absensi</button>
        <?php endif; ?>
    </form>
    <?php else: ?>
        <div class="empty-state">
            <p style="margin: 0;">Silakan pilih kelas terlebih dahulu untuk menampilkan daftar siswa.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>