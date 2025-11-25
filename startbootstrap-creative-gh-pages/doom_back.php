
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
arrive = "SELECT $_SESSION(出席) FROM 資料庫";
if ($_POST){
    arrive=有
    echo '回報成功';
}


?>
 <section class="page-section bg-primary" id="about">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="text-white mt-0">簽到頁面</h2>
                        <hr class="divider divider-light" />
                        <p class="text-white-75 mb-4">按下按鈕以簽到</p>
                        <a class="btn btn-light btn-xl" href="success.php">簽到</a>
                    </div>
                </div>
            </div>
</section>





<!--  回報鍵   -->
<?php
try {
  $postid = "";
  $company = "";
  $content = "";
  $pdate = "";
  if ($_GET) {
    require_once 'db.php';
    $action = $_GET["action"]??"";
    if ($action=="confirmed"){
      //delete data
      $postid = $_GET["postid"];
      $sql="delete from job where postid=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "i", $postid);
      $result = mysqli_stmt_execute($stmt);
      mysqli_close($conn);
      header('location:job.php');
    }
    else{
      //show data
      $postid = $_GET["postid"];
      $sql="select postid, company, content, pdate from job where postid=?";    
      // $result = mysqli_query($conn, $sql);
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "i", $postid);
      $res = mysqli_stmt_execute($stmt);
      if ($res){
        mysqli_stmt_bind_result($stmt, $postid, $company, $content, $pdate);
        mysqli_stmt_fetch($stmt);
      }
    }//confirmed else
    mysqli_close($conn);
  }//$_GET
} catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}
?>
<a href="doominsert.php?postid=<?=$postid?>&action=confirmed" class="btn">簽到</a>



<?php
require_once 'footer.php';
?>
