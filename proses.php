<?php
require 'koneksi.php';

function upload_sampul($file)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $nama_file = time() . '_' . basename($file['name']);
    move_uploaded_file($file['tmp_name'], 'sampul/' . $nama_file);
    return $nama_file;
}

if (isset($_POST['submit'])) {
    $kode_buku    = trim($_POST['kode_buku']);

    $judul        = trim($_POST['judul']);
    $pengarang    = trim($_POST['pengarang']);
    $penerbit     = trim($_POST['penerbit']);
    $tahun_terbit = (int) $_POST['tahun_terbit'];
    $stok         = (int) $_POST['stok'];


    $cek = $koneksi->prepare("SELECT id_buku FROM buku WHERE kode_buku = ?");
    $cek->bind_param("s", $kode_buku);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {

        $kode = htmlspecialchars($kode_buku);
        redirect(
            'form.php',
            'warning',
            "Kode buku <b>$kode</b> sudah terdaftar. Gunakan kode lain."
        );
    }

    $nama_sampul = upload_sampul($_FILES['sampul']) ?? '';

    $sql = "INSERT INTO buku
(kode_buku, judul, pengarang, penerbit, tahun_terbit, stok, sampul)
VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param(
        "ssssiis",
        $kode_buku,
        $judul,
        $pengarang,
        $penerbit,
        $tahun_terbit,
        $stok,
        $nama_sampul
    );
    if ($stmt->execute()) {
        redirect('buku.php', 'success', 'Data buku berhasil disimpan.');
    } else {
        redirect('form.php', 'danger', 'Gagal menyimpan data.');
    }
}

if (isset($_POST['update'])) {
    $id_buku      = (int) $_POST['id_buku'];
    $judul        = trim($_POST['judul']);
    $pengarang    = trim($_POST['pengarang']);
    $penerbit     = trim($_POST['penerbit']);
    $tahun_terbit = (int) $_POST['tahun_terbit'];
    $stok         = (int) $_POST['stok'];

    $nama_sampul = upload_sampul($_FILES['sampul']);

    if ($nama_sampul) {

        $sql = "UPDATE buku SET judul = ?, pengarang = ?, penerbit = ?,
tahun_terbit = ?, stok = ?, sampul = ?
WHERE id_buku = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param(
            "sssiisi",
            $judul,
            $pengarang,
            $penerbit,
            $tahun_terbit,
            $stok,
            $nama_sampul,
            $id_buku
        );
    } else {

        $sql = "UPDATE buku SET judul = ?, pengarang = ?, penerbit = ?,
tahun_terbit = ?, stok = ?
WHERE id_buku = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param(
            "sssiii",
            $judul,
            $pengarang,
            $penerbit,
            $tahun_terbit,
            $stok,
            $id_buku
        );
    }

    if ($stmt->execute()) {
        redirect('buku.php', 'success', 'Data buku berhasil diubah.');
    } else {
        redirect("edit.php?id=$id_buku", 'danger', 'Gagal mengubah data.');
    }
}
header('Location: buku.php');
exit;
