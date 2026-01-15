<?php
$con = mysqli_connect("localhost", "root", "", "laporan_keuangan");
if (!$con) die("Koneksi gagal: " . mysqli_connect_error());

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

/* ===== HAPUS SEMUA ===== */
if(isset($_GET['delete_all']) && $_GET['delete_all'] == 'true'){
    mysqli_query($con, "DELETE FROM journal");
    header("Location: index.php");
    exit;
}

/* ===== FILTER ===== */
$filter_tanggal = $_GET['tanggal'] ?? '';
$filter_bulan   = $_GET['bulan'] ?? '';
$filter_tahun   = $_GET['tahun'] ?? '';
$perPage        = $_GET['per_page'] ?? 10; // default 10
$page           = $_GET['page'] ?? 1;

$where = [];

if (!empty($filter_tanggal)) {
    $where[] = "j.tanggal = '$filter_tanggal'";
} else {
    if (!empty($filter_bulan)) $where[] = "MONTH(j.tanggal) = '$filter_bulan'";
    if (!empty($filter_tahun))  $where[] = "YEAR(j.tanggal) = '$filter_tahun'";
}

$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

/* ===== PAGINATION ===== */
$totalResult = mysqli_query($con, "SELECT COUNT(*) as total FROM journal j $whereSQL");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];

if($perPage === 'all'){
    $perPageSQL = '';
    $totalPages = 1;
    $no = 1;
} else {
    $perPage = (int)$perPage;
    $page = (int)$page;
    $totalPages = ceil($totalRecords / $perPage);
    $perPageSQL = "LIMIT " . (($page-1)*$perPage) . ", $perPage";
    $no = (($page-1)*$perPage) + 1;
}

/* ===== QUERY DATA ===== */
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
    $perPageSQL
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
        a.delete-all { background: #D32F2F; }

        form {
            background: #FFFFFF;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        label {
            font-weight: bold;
            color: #1565C0;
        }

        input, select, button {
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

        .per-page-select select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: #FFFFFF;
            border: 1px solid #B3D7FF;
            border-radius: 5px;
            padding: 8px;
            cursor: pointer;
        }

        select[multiple], select[size] {
            direction: rtl;
        }

        .pagination {
            margin-top: 15px;
        }

        .pagination a {
            display: inline-block;
            padding: 5px 10px;
            margin: 2px;
            background: #42A5F5;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .pagination a.active {
            background: #1E88E5;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Journal Keuangan</h2>

    <div class="top-bar">
        <a href="add.php" class="button add">+ Tambah Journal</a>
        <a href="?delete_all=true" class="button delete-all" onclick="return confirm('Yakin ingin menghapus semua data?');">Hapus Semua</a>
    </div>

    <form method="get">
        <label>Tanggal:</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($filter_tanggal) ?>">

        <label>Bulan:</label>
        <select name="bulan">
            <option value="">-- Semua --</option>
            <?php for ($b = 1; $b <= 12; $b++):
                $selected = ($filter_bulan == $b) ? 'selected' : '';
                echo "<option value='$b' $selected>" . date("F", mktime(0,0,0,$b,1)) . "</option>";
            endfor; ?>
        </select>

        <label>Tahun:</label>
        <select name="tahun">
            <option value="">-- Semua --</option>
            <?php for ($t = date('Y'); $t >= 2020; $t--):
                $selected = ($filter_tahun == $t) ? 'selected' : '';
                echo "<option value='$t' $selected>$t</option>";
            endfor; ?>
        </select>

        <label>Per Page:</label>
        <select name="per_page" onchange="this.form.submit()">
            <?php
            $options = [10,20,30,40,50,'all'];
            foreach($options as $opt){
                $selected = ($perPage == $opt) ? 'selected' : '';
                echo "<option value='$opt' $selected>$opt</option>";
            }
            ?>
        </select>

        <button type="submit">Filter</button>
        <a href="index.php" class="button delete">Reset</a>
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
            while ($row = $journal_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>".htmlspecialchars($row['item'])."</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                echo "<td>".htmlspecialchars($row['income_name'])."</td>";
                echo "<td>".htmlspecialchars($row['expense_name'])."</td>";
                echo "<td>" . rupiah($row['nominal']) . "</td>";
                echo "<td>
                        <a href='edit.php?id={$row['id']}' class='button edit'>Edit</a>
                        <a href='delete.php?id={$row['id']}' class='button delete' onclick=\"return confirm('Yakin ingin hapus?');\">Hapus</a>
                      </td>";
                echo "</tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='7'>Data tidak ada</td></tr>";
        }
        ?>
    </table>

    <?php if($perPage != 'all' && $totalPages > 1): ?>
    <div class="pagination">
        <?php for($p=1; $p<=$totalPages; $p++):
            $active = ($p == $page) ? 'active' : '';
            $queryParams = $_GET;
            $queryParams['page'] = $p;
            $url = $_SERVER['PHP_SELF'] . '?' . http_build_query($queryParams);
        ?>
            <a href="<?= $url ?>" class="<?= $active ?>"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div>

</body>
</html>
