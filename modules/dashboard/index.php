<?php
include "../../config/database.php";
include "../../includes/auth.php";

check_login();

// Total siswa (semua)
$q_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
$total_siswa = mysqli_fetch_assoc($q_siswa)['total'];

// Total guru
$q_guru = mysqli_query($conn, "SELECT COUNT(*) as total FROM guru");
$total_guru = mysqli_fetch_assoc($q_guru)['total'];

// Kehadiran hari ini
$today = date('Y-m-d');

$q_hadir = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM absensi 
    WHERE tanggal = '$today' AND status = 'Hadir'
");
$total_hadir = mysqli_fetch_assoc($q_hadir)['total'];

?>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/sidebar.php"; ?>

<div class="container-fluid" style="padding: 25px; background:#f3f4f6; min-height:100vh;">

    <!-- HEADER -->
    <h2 style="font-weight:700; margin-bottom:5px;">Dashboard</h2>
    <div style="color:#6b7280; margin-bottom:25px;">
        Selamat Datang, <strong><?= $_SESSION['nama_user'] ?></strong> (Role: <?= $_SESSION['role'] ?>)
    </div>

    <!-- WRAPPER -->
    <div style="
        background:white;
        padding:25px;
        border-radius:16px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    ">

        <!-- CARD WRAPPER -->
        <div style="display:flex; gap:20px; flex-wrap:wrap;">

            <!-- CARD -->
            <div style="
                flex:1;
                min-width:250px;
                background:#ffffff;
                padding:20px;
                border-radius:14px;
                border:4px solid #e5e7eb;
                box-shadow:0 8px 18px rgba(0,0,0,0.06);
            ">
                <div style="
                    width:48px;
                    height:48px;
                    background:#d1fae5;
                    border-radius:12px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:15px;
                ">
                    <i class="fa-solid fa-users" style="color:#10b981;"></i>
                </div>

                <div style="color:#6b7280; font-size:14px;">Total Siswa Aktif</div>
                <div style="font-size:24px; font-weight:700;"><?= $total_siswa ?></div>
            </div>

            <!-- CARD -->
            <div style="
                flex:1;
                min-width:250px;
                background:#ffffff;
                padding:20px;
                border-radius:14px;
                border:4px solid #e5e7eb;
                box-shadow:0 8px 18px rgba(0,0,0,0.06);
            ">
                <div style="
                    width:48px;
                    height:48px;
                    background:#d1fae5;
                    border-radius:12px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:15px;
                ">
                    <i class="fa-solid fa-calendar-check" style="color:#10b981;"></i>
                </div>

                <div style="color:#6b7280; font-size:14px;">Total Hadir Hari Ini</div>
                <div style="font-size:24px; font-weight:700;"><?= $total_hadir ?></div>
            </div>

            <!-- CARD -->
            <div style="
                flex:1;
                min-width:250px;
                background:#ffffff;
                padding:20px;
                border-radius:14px;
                border:4px solid #e5e7eb;
                box-shadow:0 8px 18px rgba(0,0,0,0.06);
            ">
                <div style="
                    width:48px;
                    height:48px;
                    background:#d1fae5;
                    border-radius:12px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:15px;
                ">
                    <i class="fa-solid fa-user-tie" style="color:#10b981;"></i>
                </div>

                <div style="color:#6b7280; font-size:14px;">Total Guru</div>
                <div style="font-size:24px; font-weight:700;"><?= $total_guru ?></div>
            </div>

        </div>

    </div>

</div>

<?php include "../../includes/footer.php"; ?>
