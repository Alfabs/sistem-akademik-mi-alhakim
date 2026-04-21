<?php
include "../../config/database.php"; 
$id_kelas_pilihan = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran Professional</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --secondary-hover: #475569;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --danger: #ef4444;
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
        }

        h2 { margin: 0; font-size: 24px; font-weight: 700; color: var(--text-main); }
        .periode { color: var(--text-muted); font-size: 14px; margin-top: 4px; }

        .btn-back { 
            padding: 10px 20px; 
            border-radius: 8px; 
            background: var(--secondary); 
            color: white !important; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-back:hover { 
            background: var(--secondary-hover); 
            transform: translateY(-1px);
        }

        .filter-card { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 12px; 
            margin-bottom: 24px; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            border: 1px solid #e5e7eb;
        }

        select { 
            padding: 10px 16px; 
            border-radius: 8px; 
            border: 1px solid #d1d5db; 
            outline: none;
            font-size: 14px;
            min-width: 200px;
        }

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
            transition: all 0.2s;
        }

        .btn-filter { background: var(--primary); color: white; }
        .btn-filter:hover { background: var(--primary-hover); }

        .btn-excel { background: white; color: var(--success); border: 1px solid var(--success); }
        .btn-excel:hover { background: var(--success); color: white; }

        .table-wrapper { overflow: hidden; border: 1px solid #e5e7eb; border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f9fafb; padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; }
        td { padding: 16px; border-top: 1px solid #e5e7eb; font-size: 14px; }
        tr:hover { background-color: #f8fafc; }

        .name-cell { font-weight: 600; color: var(--text-main); }
        
        .count-tag {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }
        .tag-hadir { background: #ecfdf5; color: var(--success); }
        .tag-izin { background: #fffbeb; color: var(--warning); }
        .tag-sakit { background: #eff6ff; color: var(--info); }
        .tag-alpha { background: #fef2f2; color: var(--danger); }
        .tag-total { background: #f3f4f6; color: var(--text-main); }

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
        <div>
            <h2>Rekapitulasi Kehadiran</h2>
            <div class="periode">Sistem Akademik MI Al-Hakim • Semester Genap 2026</div>
        </div>
        <a href="index.php" class="btn-back">← Kembali ke Form Absensi</a>
    </header>

    <div class="filter-card">
        <form method="GET" action="" style="display:flex; gap:12px; align-items:center; width:100%;">
            <select name="id_kelas" required>
                <option value="" disabled <?php echo ($id_kelas_pilihan == '') ? 'selected' : ''; ?> hidden>Pilih Kelas</option>
                <?php
                $q_kelas = mysqli_query($conn, "SELECT * FROM kelas");
                while($k = mysqli_fetch_array($q_kelas)){
                    $sel = ($id_kelas_pilihan == $k['id_kelas']) ? 'selected' : '';
                    echo "<option value='".$k['id_kelas']."' $sel>".$k['nama_kelas']."</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-filter">Terapkan Filter</button>
            <div style="flex-grow: 1;"></div>
            <?php if($id_kelas_pilihan): ?>
                <a href="export_excel.php?id_kelas=<?= $id_kelas_pilihan ?>" class="btn btn-excel">📥 Export ke Excel</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if($id_kelas_pilihan): ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Lengkap Siswa</th>
                    <th style="text-align:center;">Hadir</th>
                    <th style="text-align:center;">Izin</th>
                    <th style="text-align:center;">Sakit</th>
                    <th style="text-align:center;">Alpha</th>
                    <th style="text-align:center;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $sql = "SELECT s.nama_lengkap, 
                        SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END) as h,
                        SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) as i,
                        SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) as s,
                        SUM(CASE WHEN a.status = 'Alpha' THEN 1 ELSE 0 END) as a,
                        COUNT(a.status) as total
                        FROM siswa s
                        JOIN riwayat_kelas r ON s.nisn = r.nisn
                        LEFT JOIN absensi a ON r.id_riwayat = a.id_riwayat
                        WHERE r.id_kelas = '$id_kelas_pilihan'
                        GROUP BY s.nisn
                        ORDER BY s.nama_lengkap ASC";
                
                $query = mysqli_query($conn, $sql);
                if(mysqli_num_rows($query) == 0) {
                    echo "<tr><td colspan='7' style='text-align:center; color:var(--text-muted); padding: 40px;'>Data tidak tersedia untuk kelas ini.</td></tr>";
                } else {
                    while($d = mysqli_fetch_array($query)){
                    ?>
                    <tr>
                        <td style="color: var(--text-muted);"><?= $no++ ?></td>
                        <td class="name-cell"><?= $d['nama_lengkap'] ?></td>
                        <td align="center"><span class="count-tag tag-hadir"><?= $d['h'] ?></span></td>
                        <td align="center"><span class="count-tag tag-izin"><?= $d['i'] ?></span></td>
                        <td align="center"><span class="count-tag tag-sakit"><?= $d['s'] ?></span></td>
                        <td align="center"><span class="count-tag tag-alpha"><?= $d['a'] ?></span></td>
                        <td align="center"><span class="count-tag tag-total"><?= $d['total'] ?></span></td>
                    </tr>
                    <?php } 
                } ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <p style="margin: 0;">Silakan pilih kelas terlebih dahulu untuk melihat rekapitulasi data.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>