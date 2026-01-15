<?php
$con = mysqli_connect("localhost", "root", "", "laporan_keuangan");
if (!$con) die("Koneksi gagal: " . mysqli_connect_error());

$income_result = mysqli_query($con, "SELECT * FROM income ORDER BY id ASC");
$expense_result = mysqli_query($con, "SELECT * FROM expense ORDER BY id ASC");

if(isset($_POST['submit'])){
    $item = $_POST['item'];
    $tanggal = $_POST['tanggal'];
    $income_id = $_POST['income_id'];
    $expense_id = $_POST['expense_id'];
    $nominal = $_POST['nominal'];

    if($income_id != 0 && $expense_id != 0){
        die("Income dan Expense tidak boleh diisi bersamaan.");
    }

    $sql = "INSERT INTO journal (item, tanggal, income_id, expense_id, nominal)
            VALUES ('$item', '$tanggal', $income_id, $expense_id, $nominal)";
    mysqli_query($con, $sql);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Journal</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background: #FFFFFF;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 420px;
        margin: 60px auto;
        background: #FFFFFF;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        color: #1565C0;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 6px;
        color: #1565C0;
    }

    input, select {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #B3D7FF;
        font-family: 'Nunito', sans-serif;
        box-sizing: border-box;
    }

    input:focus, select:focus {
        outline: none;
        border-color: #42A5F5;
        box-shadow: 0 0 0 2px rgba(66,165,245,0.2);
    }

    button {
        width: 100%;
        padding: 12px;
        background: #42A5F5;
        color: #FFFFFF;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
    }

    button:hover {
        background: #1E88E5;
    }

    a {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #1565C0;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

</head>
<body>

<div class="container">
    <h2>Tambah Journal</h2>
    <form method="post">
        <div class="form-group">
            <label>Item</label>
            <input type="text" name="item" required>
        </div>
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" required>
        </div>
        <div class="form-group">
            <label>Income</label>
            <select name="income_id">
                <?php while($i = mysqli_fetch_assoc($income_result)) {
                    echo "<option value='{$i['id']}'>{$i['name']}</option>";
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label>Expense</label>
            <select name="expense_id">
                <?php mysqli_data_seek($expense_result, 0); while($e = mysqli_fetch_assoc($expense_result)) {
                    echo "<option value='{$e['id']}'>{$e['name']}</option>";
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label>Nominal</label>
            <input type="number" name="nominal" required>
        </div>
        <button type="submit" name="submit">Simpan</button>
    </form>
    <a href="index.php">&larr; Kembali</a>
</div>

</body>
</html>
