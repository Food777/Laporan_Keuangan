<?php
require_once __DIR__ . '/Laravel/vendor/autoload.php';
use Faker\Factory;

$conn = new mysqli("localhost", "root", "", "laporan_keuangan");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$faker = Factory::create('id_ID');

// ambil ID terakhir
$result = $conn->query("SELECT id FROM journal ORDER BY id DESC LIMIT 1");
$startNumber = 1;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $startNumber = (int)$row['id'] + 1;
}

// ambil semua income & expense
$income_result = $conn->query("SELECT id FROM income");
$expense_result = $conn->query("SELECT id FROM expense");

$income_ids = [];
$expense_ids = [];

while($i = $income_result->fetch_assoc()) $income_ids[] = $i['id'];
while($e = $expense_result->fetch_assoc()) $expense_ids[] = $e['id'];

// generate 50 data dummy
for ($i=1; $i<=50; $i++) {
    $item = $faker->sentence(3); // contoh item
    $tanggal = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
    
    // income atau expense saja
    $is_income = $faker->boolean;
    if($is_income && count($income_ids) > 0){
        $income_id = $faker->randomElement($income_ids);
        $expense_id = 0;
    } else if(count($expense_ids) > 0){
        $expense_id = $faker->randomElement($expense_ids);
        $income_id = 0;
    } else {
        $income_id = 0;
        $expense_id = 0;
    }

    $nominal = $faker->numberBetween(10000, 1000000);

    $sql = "INSERT INTO journal (item, tanggal, income_id, expense_id, nominal) 
            VALUES ('$item', '$tanggal', $income_id, $expense_id, $nominal)";
    $conn->query($sql);
}

echo "✅ 50 data dummy berhasil diinsert ke table journal";
$conn->close();
