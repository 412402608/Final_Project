<?php
session_start();

if ($_SESSION['userrole'] != 'M') {
    include('header.php');
    $message = '只有管理員可以刪除違規資料';
    ?>
    <div class="alert alert-primary" role="alert">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php
    include('footer.php');
    exit;
}
try {
  $record_id = "";
  $records = "";
  $record_time = "";
  if ($_GET) {
    require_once 'db1.php';
    $action = $_GET["action"] ?? "";
    if ($action == "confirmed") {
      // delete data
      $record_id = $_GET["record_id"];
      $sql = "DELETE FROM record WHERE record_id=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "s", $records);
      $result = mysqli_stmt_execute($stmt);
      mysqli_close($conn);
      header('Location:record.php');
    }
    else {
      // show data
      $student_id = $_GET["record_id"];
      $sql = "SELECT record_id, records, record_time FROM record WHERE record_id=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "s", $records);
      $res = mysqli_stmt_execute($stmt);
      if ($res) {
        mysqli_stmt_bind_result($stmt, $record_id, $records, $record_time);
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
      <td>學生帳號</td>
      <td>違規事項</td>
      <td>違規時間</td>
    </tr>
    <tr>
      <td><?= $record_id ?></td>
      <td><?= $records ?></td>
      <td><?= $record_time ?></td>
    </tr>
  </table>
  <a href="record_delete.php?record_id=<?= $record_id ?>&action=confirmed" class="btn btn-danger">刪除</a>
</div>
<?php
require_once "footer.php";
?>
