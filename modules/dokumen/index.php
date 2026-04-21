<?php
require_once '../../config/database.php';
include '../../includes/header.php'; 

$where_clause = ""; 
$mapel_terpilih = "";

if (isset($_GET['mapel']) && $_GET['mapel'] != '') {
    $mapel_terpilih = mysqli_real_escape_string($conn, $_GET['mapel']);
    $where_clause = "WHERE d.kode_mapel = '$mapel_terpilih'";
}
?>

<div id="page-content-wrapper">
    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0">Dokumen Pembelajaran</h3>
            <a href="upload.php" class="btn btn-success"><i class='bi bi-cloud-arrow-up'></i> Unggah Dokumen</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                
                <form method="GET" action="index.php" class="mb-4 w-25">
                    <label for="mapel" class="form-label text-muted small">Filter Mata Pelajaran</label>
                    <select name="mapel" id="mapel" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Mata Pelajaran</option>
                        <?php
                        $mapel_query = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel");
                        while ($m = mysqli_fetch_assoc($mapel_query)) {
                            $selected = ($m['kode_mapel'] == $mapel_terpilih) ? 'selected' : '';
                            echo "<option value='" . $m['kode_mapel'] . "' $selected>" . $m['nama_mapel'] . "</option>";
                        }
                        ?>
                    </select>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama File</th>
                                <th>Jenis</th>
                                <th>Mata Pelajaran</th>
                                <th>Pengunggah</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT d.*, g.nama_lengkap, m.nama_mapel 
                                      FROM dokumen d 
                                      JOIN guru g ON d.nip = g.nip 
                                      JOIN mapel m ON d.kode_mapel = m.kode_mapel 
                                      $where_clause
                                      ORDER BY d.tgl_unggah DESC";
                            
                            $result = mysqli_query($conn, $query);
                            $no = 1;

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td class='fw-semibold'>" . htmlspecialchars($row['nama_file']) . "</td>";
                                    echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($row['jenis_file']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['nama_mapel']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                                    echo "<td>" . date('d M Y, H:i', strtotime($row['tgl_unggah'])) . "</td>";
                                    echo "<td>
                                            <a href='download.php?id=" . $row['id_dokumen'] . "' class='btn btn-sm btn-primary'><i class='bi bi-download'></i></a>
                                            <a href='hapus.php?id=" . $row['id_dokumen'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus dokumen ini?\")'><i class='bi bi-trash'></i></a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Belum ada dokumen yang diunggah.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>