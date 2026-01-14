<?php
$con = mysqli_connect("localhost", "root", "", "laporan_keuangan");
if (!$con) die("Koneksi gagal: " . mysqli_connect_error());

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = "DELETE FROM journal WHERE id = $id";
    mysqli_query($con, $sql);
}

// Kembali ke index setelah delete
header("Location: index.php");
exit;
?>
