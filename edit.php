<?php
$con = mysqli_connect("localhost", "root", "", "laporan_keuangan");
if (!$con) die("Koneksi gagal: " . mysqli_connect_error());

$income_result = mysqli_query($con, "SELECT * FROM income ORDER BY id ASC");
$expense_result = mysqli_query($con, "SELECT * FROM expense ORDER BY id ASC");

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$journal_result = mysqli_query($con, "SELECT * FROM journal WHERE id = $id");
$journal = mysqli_fetch_assoc($journal_result);

if(!$journal){
    echo "Data tidak ditemukan!";
    exit;
}

if(isset($_POST['submit'])){
    $item = $_POST['item'];
    $tanggal = $_POST['tanggal'];
    $income_id = $_POST['income_id'];
    $expense_id = $_POST['expense_id'];
    $nominal = $_POST['nominal'];

    if($income_id != 0 && $expense_id != 0){
        die("Income dan Expense tidak boleh diisi bersamaan.");
    }

    $sql = "UPDATE journal 
            SET item='$item', tanggal='$tanggal', income_id=$income_id, expense_id=$expense_id, nominal=$nominal
            WHERE id = $id";
    mysqli_query($con, $sql);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Journal</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background:#FFF4E1; padding:0; margin:0; }
        .container { width:400px; margin:50px auto; background:#FFE6B8; padding:30px; border-radius:15px; box-shadow:0 6px 15px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#7D4E2A; margin-bottom:20px; }
        .form-group { margin-bottom:15px; }
        label { display:block; font-weight:bold; margin-bottom:5px; color:#7D4E2A; }
        input, select { width:100%; padding:12px; border-radius:8px; border:1px solid #FFB74D; }
        button { width:100%; padding:12px; background:#FFA726; color:white; font-weight:bold; border:none; border-radius:8px; cursor:pointer; margin-top:10px; transition:0.3s; }
        button:hover { background:#FF8C42; }
        a { display:block; text-align:center; margin-top:10px; color:#7D4E2A; text-decoration:none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Journal</h2>
    <form method="post">
        <div class="form-group">
            <label>Item</label>
            <input type="text" name="item" value="<?php echo htmlspecialchars($journal['item']); ?>" required>
        </div>
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?php echo $journal['tanggal']; ?>" required>
        </div>
        <div class="form-group">
            <label>Income</label>
            <select name="income_id">
                <?php 
                mysqli_data_seek($income_result, 0);
                while($i = mysqli_fetch_assoc($income_result)) {
                    $selected = $i['id'] == $journal['income_id'] ? 'selected' : '';
                    echo "<option value='{$i['id']}' $selected>{$i['name']}</option>";
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label>Expense</label>
            <select name="expense_id">
                <?php 
                mysqli_data_seek($expense_result, 0);
                while($e = mysqli_fetch_assoc($expense_result)) {
                    $selected = $e['id'] == $journal['expense_id'] ? 'selected' : '';
                    echo "<option value='{$e['id']}' $selected>{$e['name']}</option>";
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label>Nominal</label>
            <input type="number" name="nominal" value="<?php echo $journal['nominal']; ?>" required>
        </div>
        <button type="submit" name="submit">Update</button>
    </form>
    <a href="index.php">&larr; Kembali</a>
</div>

</body>
</html>
