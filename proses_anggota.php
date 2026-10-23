
<?php
require 'koneksi.php';

//  SIMPAN DATA BARU 
if (isset($_POST['submit'])) {
    $nim = trim($_POST['nim']);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);

    // Cek duplikat NIM
    $cek = $koneksi->prepare(
        "SELECT id_anggota FROM anggota WHERE nim = ?"
    );
    $cek->bind_param("s", $nim);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $cek->close();

        redirect(
            'form_anggota.php',
            'warning',
            'NIM tersebut sudah terdaftar. Gunakan NIM lain.'
        );
    }
    $cek->close();

    // Simpan data anggota
    $sql = "INSERT INTO anggota (nim, nama, email, no_hp)
            VALUES (?, ?, ?, ?)";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssss", $nim, $nama, $email, $no_hp);

    if ($stmt->execute()) {
        $stmt->close();
        redirect(
            'anggota.php',
            'success',
            'Data anggota berhasil disimpan.'
        );
    } else {
        $stmt->close();
        redirect(
            'form_anggota.php',
            'danger',
            'Gagal menyimpan data anggota.'
        );
    }
}

//  UBAH DATA 
if (isset($_POST['update'])) {
    $id_anggota = (int) $_POST['id_anggota'];
    $nim = trim($_POST['nim']);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);

    // Cek apakah NIM digunakan anggota lain
    $cek = $koneksi->prepare(
        "SELECT id_anggota FROM anggota
         WHERE nim = ? AND id_anggota != ?"
    );
    $cek->bind_param("si", $nim, $id_anggota);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $cek->close();

        redirect(
            "edit_anggota.php?id=$id_anggota",
            'warning',
            'NIM sudah digunakan anggota lain.'
        );
    }
    $cek->close();

    // Update data anggota
    $sql = "UPDATE anggota
            SET nim = ?, nama = ?, email = ?, no_hp = ?
            WHERE id_anggota = ?";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param(
        "ssssi",
        $nim,
        $nama,
        $email,
        $no_hp,
        $id_anggota
    );

    if ($stmt->execute()) {
        $stmt->close();
        redirect(
            'anggota.php',
            'success',
            'Data anggota berhasil diubah.'
        );
    } else {
        $stmt->close();
        redirect(
            "edit_anggota.php?id=$id_anggota",
            'danger',
            'Gagal mengubah data anggota.'
        );
    }
}

// Jika file dibuka langsung tanpa mengirim form
redirect('anggota.php', 'warning', 'Silakan gunakan formulir anggota.');