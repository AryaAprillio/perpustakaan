<?php
require 'koneksi.php';

// Ambil data anggota berdasarkan ID
$id = (int) ($_GET['id'] ?? 0);

$stmt = $koneksi->prepare(
    "SELECT id_anggota, nim, nama, email, no_hp
     FROM anggota
     WHERE id_anggota = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Jika data tidak ditemukan
if (!$row) {
    redirect('anggota.php', 'danger', 'Data anggota tidak ditemukan.');
}

$judul_halaman = 'Edit Anggota';
$menu_aktif = 'anggota';

require 'layout/header.php';
?>

<h1 class="mb-3">Edit Data Anggota</h1>

<form action="proses_anggota.php" method="post">

    <input type="hidden" name="id_anggota"
        value="<?= $row['id_anggota'] ?>">

    <div class="row mb-3">
        <label for="nim" class="col-sm-2 col-form-label">NIM</label>
        <div class="col-sm-4">
            <input type="text" class="form-control"
                id="nim" name="nim"
                value="<?= htmlspecialchars($row['nim']) ?>"
                maxlength="20" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="nama" class="col-sm-2 col-form-label">Nama</label>
        <div class="col-sm-4">
            <input type="text" class="form-control"
                id="nama" name="nama"
                value="<?= htmlspecialchars($row['nama']) ?>"
                maxlength="100" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="email" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-4">
            <input type="email" class="form-control"
                id="email" name="email"
                value="<?= htmlspecialchars($row['email'] ?? '') ?>"
                maxlength="100">
        </div>
    </div>

    <div class="row mb-3">
        <label for="no_hp" class="col-sm-2 col-form-label">No. HP</label>
        <div class="col-sm-4">
            <input type="text" class="form-control"
                id="no_hp" name="no_hp"
                value="<?= htmlspecialchars($row['no_hp'] ?? '') ?>"
                maxlength="20">
        </div>
    </div>

    <input type="submit" value="Simpan Perubahan"
        name="update" class="btn btn-info">

    <a href="anggota.php" class="btn btn-secondary">Kembali</a>

</form>

<?php require 'layout/footer.php'; ?>