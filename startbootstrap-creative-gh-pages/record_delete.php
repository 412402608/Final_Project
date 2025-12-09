<?php
session_start();

if ($_SESSION['userrole'] != 'M') {
    include('header.php');
    $message = '只有管理員可以刪除違規資料';
    ?>
    <div class="alert alert-primary" role="alert">
        <!-- 把特殊 HTML 字元轉成安全的 HTML 實體，避免 HTML 或 JavaScript 被直接執行，常用於防止 XSS 攻擊。 -->
        <?= htmlspecialchars($message) ?>
    </div>
    <?php
    include('footer.php');
    exit;
}
// try放置可能會發生錯誤或例外的程式碼，此處指為管理員的情況
try {
  $record_id = "";
  $records = "";
  $record_point = "";
  $recordnm="";
  if ($_GET) {
    require_once 'db1.php';
    $action = $_GET["action"] ?? "";
    if ($action == "confirmed") {
      // delete data
      $recordnm = $_GET["recordnm"];
      $sql = "DELETE FROM record WHERE recordnm=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "s", $recordnm);
      $result = mysqli_stmt_execute($stmt);
      mysqli_close($conn);
      header('Location:record.php');
    }
    else {
      // show data
      $recordnm = $_GET["recordnm"];
      $sql = "SELECT recordnm, record_id, records, record_point FROM record WHERE recordnm=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "s", $recordnm);
      $res = mysqli_stmt_execute($stmt);
      if ($res) {
        mysqli_stmt_bind_result($stmt, $recordnm, $record_id, $records, $record_point);
        mysqli_stmt_fetch($stmt);
      }
      mysqli_close($conn);
    } // confirmed else
  } // $_GET
} catch(Exception $e) {
  echo 'Message: ' . $e->getMessage();
}
require_once "header.php";
?>
<div class="container-fluid position-relative" style="padding-top:90px; padding-bottom:120px;">
<div class="container">
  <table class="table table-bordered table-striped">
    <tr>
      <td>違規單號</td>
      <td>學生帳號</td>
      <td>違規事項</td>
      <td>違規時間</td>
    </tr>
    <tr>
      <td><?= $recordnm ?></td>
      <td><?= $record_id ?></td>
      <td><?= $records ?></td>
      <td><?= $record_point ?></td>
    </tr>
  </table>
  <a href="record_delete.php?recordnm=<?= $recordnm ?>&action=confirmed" class="btn btn-danger">刪除</a>
</div>
<?php
require_once "footer.php";
?>
