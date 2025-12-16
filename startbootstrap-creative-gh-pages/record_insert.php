<?php
session_start();
if ($_SESSION['userrole'] != 'M') {
    include('header.php');
    $message = '只有管理員可以新增違規資料';
    ?>
    <div class="alert alert-primary" role="alert">
    <?php
    include('footer.php');
    exit;
}
// try放置可能會發生錯誤或例外的程式碼，此處指為管理員的情況
try {
  require_once 'db.php';
  $msg = "";
  if ($_POST) {
    // insert data
    $record_id = $_POST["record_id"];
    $records = $_POST["records"];
    $record_point = $_POST["record_point"];

    $sql = "INSERT INTO record (record_id, records, record_point, recordnm) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $record_id, $records, $record_point, $recordnm);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
      header('Location: record.php');
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
<form action="record_insert.php" method="post">
  <div class="mb-3 row">
    <label for="_record_id" class="col-sm-2 col-form-label">姓名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="record_id" id="_record_id" required>
    </div>
  </div>
  <div class="mb-3 row">
    <label for="_records" class="col-sm-2 col-form-label">違規事項</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="records" id="_records" required>
    </div>
  </div>
  <div class="mb-3 row">
    <label for="_record_point" class="col-sm-2 col-form-label">違規點數</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="record_point" id="_record_point" 
      required>
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