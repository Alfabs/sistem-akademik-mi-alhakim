<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/auth.php';

$current_page = $_SERVER['REQUEST_URI'];
$role = $_SESSION['role'] ?? '';

/**
 * Cek menu aktif berdasarkan keyword URL
 */
function activeMenu($keyword)
{
    global $current_page;
    return strpos($current_page, $keyword) !== false ? 'active' : '';
}
?>

<!-- Sidebar -->
<div id="sidebar-wrapper" class="bg-white border-end shadow-sm" style="width:260px; min-height:100vh;">

    <!-- Logo / Judul -->
    <div class="sidebar-heading text-center py-4 border-bottom">
        <h5 class="fw-bold m-0 text-primary">
            <i class="fa-solid fa-school me-2"></i> MI AL-HAKIM
        </h5>
    </div>

    <!-- Menu -->
    <div class="list-group list-group-flush p-3">

        <!-- Dashboard (Semua Role) -->
        <a href="<?= BASE_URL ?>modules/dashboard/index.php"
           class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/dashboard/') ?>">
            <i class="fa-solid fa-chart-line me-2"></i> Dashboard
        </a>

        <!-- Murid (Semua Role, tapi Operator Full CRUD) -->
        <a href="<?= BASE_URL ?>modules/siswa/index.php"
           class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/siswa/') ?>">
            <i class="fa-solid fa-users me-2"></i> Data Siswa
        </a>

        <!-- Absensi (Semua kecuali Kepsek) -->
        <?php if (in_array($role, ['Operator', 'Guru', 'TU'])): ?>
        <a href="<?= BASE_URL ?>modules/absensi/index.php"
           class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/absensi/') ?>">
            <i class="fa-solid fa-user-check me-2"></i> Absensi
        </a>
        <?php endif; ?>

        <!-- Nilai (Semua kecuali TU) -->
        <?php if (in_array($role, ['Operator', 'Guru', 'Kepsek'])): ?>
        <a href="<?= BASE_URL ?>modules/nilai/index.php"
           class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/nilai/') ?>">
            <i class="fa-solid fa-clipboard-list me-2"></i> Nilai Siswa
        </a>
        <?php endif; ?>

        <!-- Dokumen (Semua Role) -->
        <a href="<?= BASE_URL ?>modules/dokumen/index.php"
           class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/dokumen/') ?>">
            <i class="fa-solid fa-folder-open me-2"></i> Dokumen
        </a>

        <!-- Laporan (Semua Role) -->
        <a href="<?= BASE_URL ?>modules/laporan/index.php"
           class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/laporan/') ?>">
            <i class="fa-solid fa-file-waveform me-2"></i> Laporan
        </a>

        <!-- Menu Khusus Operator -->
        <?php if ($role === 'Operator'): ?>
            <div class="mt-3 mb-1 small text-muted fw-bold px-3">PENGATURAN</div>
            
            <a href="<?= BASE_URL ?>modules/kelas/index.php"
               class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/kelas/') ?>">
                <i class="fas fa-school me-2"></i> Kelas & TA
            </a>

            <a href="<?= BASE_URL ?>modules/guru/index.php"
               class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/guru/') ?>">
                <i class="fas fa-chalkboard-teacher me-2"></i> Data Guru
            </a>

            <a href="<?= BASE_URL ?>modules/users/index.php"
               class="list-group-item list-group-item-action rounded mb-2 <?= activeMenu('/users/') ?>">
                <i class="fa-solid fa-user-gear me-2"></i> Manajemen User
            </a>
        <?php endif; ?>

        <!-- Logout -->
        <a href="<?= BASE_URL ?>modules/auth/logout.php"
           class="list-group-item list-group-item-action text-danger rounded mt-4">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
        </a>

    </div>
</div>

<!-- Page Content -->
<div id="page-content-wrapper">

<nav class="navbar navbar-light bg-white border-bottom px-4 py-3">
    <div class="ms-auto d-flex align-items-center gap-3">
        <div class="text-end">
            <div class="fw-bold"><?= $_SESSION['nama_user'] ?? 'User' ?></div>
            <small class="text-muted"><?= $_SESSION['role'] ?? 'Guest' ?></small>
        </div>
        <div style="width:40px;height:40px;border-radius:50%;background:#0d6efd; display:flex; align-items:center; justify-content:center; color:white;">
            <i class="fa-solid fa-user"></i>
        </div>
    </div>
</nav>

<div class="main-content">
