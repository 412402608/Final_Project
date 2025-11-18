<?php
// 顯示錯誤訊息
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 資料庫連線設定
$host = 'localhost';
$db   = 'finalwork';   // 你的資料庫名稱
$user = 'root';       // MySQL 帳號
$pass = '';           // MySQL 密碼，如果沒有就留空
$port = 3307;         // MySQL 埠號，XAMPP 預設是 3306

// 建立 PDO 連線
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "資料庫連線成功！<br>";

    // 測試查詢 Residents 表
    $stmt = $pdo->query("SELECT * FROM Residents");
    $residents = $stmt->fetchAll();

    if ($residents) {
        echo "住民資料如下：<br>";
        echo "<pre>";
        print_r($residents);
        echo "</pre>";
    } else {
        echo "目前 Residents 表沒有資料。";
    }

} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}
?>
