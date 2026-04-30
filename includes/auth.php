<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login
 */
if (!function_exists('check_login')) {
    function check_login() {
        if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
            // Redirect ke root index.php
            // Menggunakan script JS agar lebih aman terhadap perbedaan level direktori
            echo "<script>window.location.href = '/index.php';</script>";
            exit;
        }
    }
}

/**
 * Cek hak akses berdasarkan role
 * @param array|string $allowed_roles
 */
if (!function_exists('require_role')) {
    function require_role($allowed_roles) {
        check_login();
        
        if (is_string($allowed_roles)) {
            $allowed_roles = [$allowed_roles];
        }
        
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
            echo "<script>
                    alert('Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
                    window.location.href = '/modules/dashboard/index.php';
                  </script>";
            exit;
        }
    }
}

/**
 * Helper untuk mengecek role tanpa redirect
 */
if (!function_exists('has_role')) {
    function has_role($roles) {
        if (!isset($_SESSION['role'])) return false;
        if (is_string($roles)) {
            return $_SESSION['role'] === $roles;
        }
        return in_array($_SESSION['role'], $roles);
    }
}
?>
