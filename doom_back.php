
<?php
require_once '資料庫.php';
require_once 'header.php';

// 啟動資料庫
session_start();

// 如果login變數不存在，導向login.php(登入表單)
if ($_SESSION[login變數] != true){
    header('Location: login.php');
}
// 按下一個按鈕，以回報是否回到宿舍
arrive = SELECT $_SESSION(出席) FROM 資料庫;
if ($_POST){
    arrive=有
    echo '回報成功';
}
  

?>


<?php
require_once 'footer.php';
?>
