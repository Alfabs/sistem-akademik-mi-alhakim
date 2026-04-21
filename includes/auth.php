<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

function require_role($roles) {
    if (!isset($_SESSION['role'])) {
        echo "Akses ditolak";
        exit;
    }

    if (is_array($roles)) {
        if (!in_array($_SESSION['role'], $roles)) {
            echo "Akses ditolak";
            exit;
        }
    } else {
        if ($_SESSION['role'] !== $roles) {
            echo "Akses ditolak";
            exit;
        }
    }
}