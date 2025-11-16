<!-- 加總違規的程式碼 -->
<?php
require_once '資料庫.php';
require_once 'header.php';

session_start();

// 檢查登入
if (!isset($_SESSION["login變數"]) || $_SESSION["login變數"] !== true) {
    header('Location: login.php');
    exit;
}

// 查詢違規次數
$sql = "SELECT 違規 FROM 資料表 WHERE id = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$Foul = $row["違規"];

// 若收到 POST 遞增一次
if ($_POST) {
    $Foul += 1;

    $update = "UPDATE 資料表 SET 違規 = $Foul WHERE id = 1";
    mysqli_query($conn, $update);

    echo "回報成功";
}
?>

<?php require_once 'footer.php'; ?>
