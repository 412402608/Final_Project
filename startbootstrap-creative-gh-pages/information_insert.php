<?php
session_start();

if ($_SESSION['role'] != 'M') {
    include('header.php');
    $message = '只有管理員可新增住戶資料';
    ?>
    <div class="alert alert-primary" role="alert">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php
    include('footer.php');
    exit;
}
try {
  require_once 'db.php';
  $msg = "";
  if ($_POST) {
    // insert data
    $student_id = $_POST["student_id"];
    $name = $_POST["name"];
    $room_number = $_POST["room_number"];
    $contact = $_POST["contact"];

    $sql = "INSERT INTO residents (student_id, name, room_number, contact) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $student_id, $name, $room_number, $contact);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
      header('Location: information.php');
      exit;
    }
    else {
      $msg = "無法新增資料";
    }
  }
  require_once "header.php";
?>
<div class="container-fluid position-relative" style="padding-top:90px; padding-bottom:120px;">
<div class="container">
<form action="information_insert.php" method="post">
  <div class="mb-3 row">
    <label for="_student_id" class="col-sm-2 col-form-label">學號</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="student_id" id="_student_id" placeholder="學號" required>
    </div>
  </div>
  <div class="mb-3 row">
    <label for="_name" class="col-sm-2 col-form-label">姓名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="name" id="_name" placeholder="姓名" required>
    </div>
  </div>
  <div class="mb-3 row">
    <label for="_room_number" class="col-sm-2 col-form-label">房號</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="room_number" id="_room_number" placeholder="房號" required>
    </div>
  </div>
  <div class="mb-3 row">
    <label for="_contact" class="col-sm-2 col-form-label">聯絡方式</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="contact" id="_contact" placeholder="聯絡方式" required>
    </div>
  </div>
  <input class="btn btn-primary" type="submit" value="送出">
  <?=$msg?>
</form>
</div>

<?php
  mysqli_close($conn);
}
//catch exception
catch(Exception $e) {
  echo 'Message: ' . $e->getMessage();
}
require_once "footer.php";

?>
