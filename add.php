<!-- 加總違規的程式碼 -->
<?php
require_once '資料庫.php';
require_once 'header.php';

// 啟動資料庫
session_start();

if (!isset($_SESSION["login變數"]) || $_SESSION["login變數"] !== true){
    header('Location: login.php');
}

$Foul = "SELECT 違規 FROM 資料庫";

if ($_POST){
    $Foul+=1;
    echo '回報成功';
}

?>


<?php
require_once 'footer.php';
?>