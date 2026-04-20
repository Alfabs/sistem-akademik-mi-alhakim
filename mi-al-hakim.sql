-- ======================================================
-- DATABASE: manajemen_sekolah
-- ======================================================

CREATE DATABASE IF NOT EXISTS manajemen_sekolah;
USE manajemen_sekolah;

-- ======================================================
-- 1. TABEL users (untuk autentikasi & hak akses)
-- ======================================================
CREATE TABLE users (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- simpan hash password
    role ENUM('Operator', 'Guru', 'TU', 'Kepsek') NOT NULL
);

-- ======================================================
-- 2. TABEL siswa
-- ======================================================
CREATE TABLE siswa (
    nisn VARCHAR(20) PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50),
    tgl_lahir DATE,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan'),
    alamat TEXT,
    asal_sekolah VARCHAR(100)
);

-- ======================================================
-- 3. TABEL guru
-- ======================================================
CREATE TABLE guru (
    nip VARCHAR(20) PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15),
    id_user INT(11) UNIQUE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);

-- ======================================================
-- 4. TABEL mapel (mata pelajaran)
-- ======================================================
CREATE TABLE mapel (
    kode_mapel VARCHAR(10) PRIMARY KEY,
    nama_mapel VARCHAR(50) NOT NULL
);

-- ======================================================
-- 5. TABEL kelas
-- ======================================================
CREATE TABLE kelas (
    id_kelas INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(10) NOT NULL -- contoh: '1A', '2B', dst.
);

-- ======================================================
-- 6. TABEL tahun_ajaran
-- ======================================================
CREATE TABLE tahun_ajaran (
    id_ta INT(11) AUTO_INCREMENT PRIMARY KEY,
    tahun VARCHAR(9) NOT NULL, -- contoh: '2025/2026'
    semester ENUM('Ganjil', 'Genap') NOT NULL,
    status_aktif TINYINT(1) DEFAULT 0 -- 1 = aktif, 0 = arsip
);

-- ======================================================
-- 7. TABEL riwayat_kelas (menghubungkan siswa, kelas, dan tahun ajaran)
-- ======================================================
CREATE TABLE riwayat_kelas (
    id_riwayat INT(11) AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20) NOT NULL,
    id_kelas INT(11) NOT NULL,
    id_ta INT(11) NOT NULL,
    FOREIGN KEY (nisn) REFERENCES siswa(nisn) ON DELETE CASCADE,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE,
    FOREIGN KEY (id_ta) REFERENCES tahun_ajaran(id_ta) ON DELETE CASCADE,
    UNIQUE KEY unique_siswa_kelas_ta (nisn, id_kelas, id_ta) -- mencegah duplikasi
);

-- ======================================================
-- 8. TABEL absensi
-- ======================================================
CREATE TABLE absensi (
    id_absensi INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_riwayat INT(11) NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (id_riwayat) REFERENCES riwayat_kelas(id_riwayat) ON DELETE CASCADE,
    UNIQUE KEY unique_absensi_harian (id_riwayat, tanggal) -- satu siswa satu tanggal hanya satu record
);

-- ======================================================
-- 9. TABEL nilai
-- ======================================================
CREATE TABLE nilai (
    id_nilai INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_riwayat INT(11) NOT NULL,
    kode_mapel VARCHAR(10) NOT NULL,
    nip VARCHAR(20) NOT NULL,
    jenis_nilai ENUM('UH', 'UTS', 'UAS') NOT NULL,
    nilai_angka DECIMAL(5,2) NOT NULL CHECK (nilai_angka >= 0 AND nilai_angka <= 100),
    waktu_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_riwayat) REFERENCES riwayat_kelas(id_riwayat) ON DELETE CASCADE,
    FOREIGN KEY (kode_mapel) REFERENCES mapel(kode_mapel) ON DELETE CASCADE,
    FOREIGN KEY (nip) REFERENCES guru(nip) ON DELETE CASCADE
);

-- ======================================================
-- 10. TABEL dokumen (RPP, modul ajar, CP, ATP)
-- ======================================================
CREATE TABLE dokumen (
    id_dokumen INT(11) AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) NOT NULL,
    kode_mapel VARCHAR(10) NOT NULL,
    jenis_file ENUM('RPP', 'Silabus', 'Modul', 'ATP') NOT NULL,
    nama_file VARCHAR(100) NOT NULL,
    path_file TEXT NOT NULL, -- lokasi penyimpanan file
    tgl_unggah DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nip) REFERENCES guru(nip) ON DELETE CASCADE,
    FOREIGN KEY (kode_mapel) REFERENCES mapel(kode_mapel) ON DELETE CASCADE
);

-- ======================================================
-- (Opsional) Tambahkan indeks untuk performa
-- ======================================================
CREATE INDEX idx_absensi_tanggal ON absensi(tanggal);
CREATE INDEX idx_nilai_jenis ON nilai(jenis_nilai);
CREATE INDEX idx_dokumen_jenis ON dokumen(jenis_file);