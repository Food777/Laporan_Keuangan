<?php
$con = mysqli_connect("localhost", "root", "", "laporan_keuangan");
if (!$con) die("Koneksi gagal: " . mysqli_connect_error());

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

/* ===== FILTER BULAN & TAHUN ===== */
$filter_bulan = $_GET['bulan'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';

$where = [];

if (!empty($filter_bulan)) {
    $where[] = "MONTH(j.tanggal) = '$filter_bulan'";
}

if (!empty($filter_tahun)) {
    $where[] = "YEAR(j.tanggal) = '$filter_tahun'";
}

$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

/* ===== QUERY ===== */
$journal_result = mysqli_query($con, "
    SELECT j.id, j.item, j.tanggal,
           i.name AS income_name,
           e.name AS expense_name,
           j.nominal
    FROM journal j
    LEFT JOIN income i ON j.income_id = i.id
    LEFT JOIN expense e ON j.expense_id = e.id
    $whereSQL
    ORDER BY j.tanggal ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Journal Keuangan</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: #FFFFFF;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #1565C0;
            margin-top: 30px;
        }

        .top-bar {
            text-align: right;
            margin-bottom: 15px;
        }

        a.button {
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            margin: 2px;
            display: inline-block;
        }

        a.add { background: #42A5F5; }
        a.edit { background: #1E88E5; }
        a.delete { background: #EF5350; }

        form {
            background: #FFFFFF;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        label {
            font-weight: bold;
            color: #1565C0;
        }

        select, button {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #B3D7FF;
            font-family: 'Nunito', sans-serif;
        }

        button {
            background: #42A5F5;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1E88E5;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: #FFFFFF;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #E0E0E0;
        }

        th {
            background: #64B5F6;
            color: white;
        }

        tr:hover {
            background: #F5F9FF;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Journal Keuangan</h2>

    <div class="top-bar">
        <a href="add.php" class="button add">+ Tambah Journal</a>
    </div>

    <!-- FILTER BULAN & TAHUN -->
    <form method="get">
        <label>Bulan:</label>
        <select name="bulan">
            <option value="">-- Semua Bulan --</option>
            <?php
            for ($b = 1; $b <= 12; $b++) {
                $selected = ($filter_bulan == $b) ? 'selected' : '';
                echo "<option value='$b' $selected>" . date("F", mktime(0,0,0,$b,1)) . "</option>";
            }
            ?>
        </select>

        <label style="margin-left:10px;">Tahun:</label>
        <select name="tahun">
            <option value="">-- Semua Tahun --</option>
            <?php
            for ($t = date('Y'); $t >= 2020; $t--) {
                $selected = ($filter_tahun == $t) ? 'selected' : '';
                echo "<option value='$t' $selected>$t</option>";
            }
            ?>
        </select>

        <button type="submit">Filter</button>
    </form>

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
        if ($journal_result->num_rows > 0) {
            $no = 1;
            while ($row = $journal_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>{$row['item']}</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                echo "<td>{$row['income_name']}</td>";
                echo "<td>{$row['expense_name']}</td>";
                echo "<td>" . rupiah($row['nominal']) . "</td>";
                echo "<td>
                        <a href='edit.php?id={$row['id']}' class='button edit'>Edit</a>
                        <a href='delete.php?id={$row['id']}' class='button delete'
                           onclick=\"return confirm('Yakin ingin hapus?');\">Hapus</a>
                      </td>";
                echo "</tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='7'>Data tidak ada</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
