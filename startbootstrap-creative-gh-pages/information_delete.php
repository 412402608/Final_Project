<?php

if ($_SESSION['userrole'] != 'M') {
    include('header.php');
    $message = '只有管理員可以刪除住戶資料';
    ?>
    <div class="alert alert-primary" role="alert">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php
    include('footer.php');
    exit;  
}
try {
  $student_id = "";
  $name = "";
  $room_number = "";
  $contact = "";
  if ($_GET) {
    require_once 'db.php';
    $action = $_GET["action"] ?? "";
    if ($action == "confirmed") {
      // delete data
      $student_id = $_GET["student_id"];
      $sql = "DELETE FROM residents WHERE student_id=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "s", $student_id);
      $result = mysqli_stmt_execute($stmt);
      mysqli_close($conn);
      header('Location:information.php');
    }
    else {
      // show data
      $student_id = $_GET["student_id"];
      $sql = "SELECT student_id, name, room_number, contact FROM residents WHERE student_id=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "s", $student_id);
      $res = mysqli_stmt_execute($stmt);
      if ($res) {
        mysqli_stmt_bind_result($stmt, $student_id, $name, $room_number, $contact);
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
      <td>學號</td>
      <td>姓名</td>
      <td>房號</td>
      <td>聯絡方式</td>
    </tr>
    <tr>
      <td><?= $student_id ?></td>
      <td><?= $name ?></td>
      <td><?= $room_number ?></td>
      <td><?= $contact ?></td>
    </tr>
  </table>
  <a href="information_delete.php?student_id=<?= $student_id ?>&action=confirmed" class="btn btn-danger">刪除</a>
</div>
<?php
require_once "footer.php";
?>
