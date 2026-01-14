<?php
$con = mysqli_connect("localhost", "root", "", "laporan_keuangan");
if (!$con) die("Koneksi gagal: " . mysqli_connect_error());

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

$journal_result = mysqli_query($con, "SELECT j.id, j.item, j.tanggal,
                   i.name AS income_name,
                   e.name AS expense_name,
                   j.nominal
            FROM journal AS j
            LEFT JOIN income AS i ON j.income_id = i.id
            LEFT JOIN expense AS e ON j.expense_id = e.id
            ORDER BY j.tanggal ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Journal Keuangan</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background:#FFF4E1; margin:0; padding:0; }
        .container { width:90%; margin:auto; padding:20px; }
        h2 { text-align:center; color:#7D4E2A; margin-top:30px; }

        table { border-collapse: collapse; width:100%; background:#FFE9C4; border-radius:10px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        th, td { padding:12px; text-align:center; }
        th { background:#FFB74D; color:#fff; }
        tr:nth-child(even) { background:#FFF1D0; }
        tr:hover { background:#FFD699; transition:0.3s; }

        a.button { text-decoration:none; padding:8px 15px; border-radius:5px; color:white; font-weight:bold; margin:2px; transition:0.3s; }
        a.add { background:#FF8C42; }
        a.edit { background:#FFA726; }
        a.delete { background:#FF7043; }

        .top-bar { text-align:right; margin-bottom:10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Journal Keuangan</h2>
    <div class="top-bar">
        <a href="add.php" class="button add">+ Tambah Journal</a>
    </div>

    <table>
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Tanggal</th>
            <th>Income</th>
            <th>Expense</th>
            <th>Nominal</th>
            <th>Aksi</th>
        </tr>

<?php
if($journal_result->num_rows > 0){
    $no = 1;
    while($row = $journal_result->fetch_assoc()){
        $date = new DateTime($row['tanggal']);
        $formattedDate = $date->format('d-m-Y');
        echo "<tr>";
        echo "<td>{$no}</td>";
        echo "<td>{$row['item']}</td>";
        echo "<td>{$formattedDate}</td>";
        echo "<td>{$row['income_name']}</td>";
        echo "<td>{$row['expense_name']}</td>";
        echo "<td>" . rupiah($row['nominal']) . "</td>";
        echo "<td>
                <a href='edit.php?id={$row['id']}' class='button edit'>Edit</a>
                <a href='delete.php?id={$row['id']}' onclick=\"return confirm('Yakin ingin hapus?');\" class='button delete'>Hapus</a>
              </td>";
        echo "</tr>";
        $no++;
    }
}else{
    echo "<tr><td colspan='7'>Data tidak ada</td></tr>";
}
?>
    </table>
</div>
</body>
</html>
