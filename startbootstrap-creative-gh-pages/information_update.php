<?php
session_start();

if ($_SESSION['userrole'] != 'M') {
    include('header.php');
    $message = '只有管理員可以修改住戶資料';
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
    require_once 'db1.php';
    $action = $_GET["action"] ?? "";
    
    if ($action == "confirmed"){
      // update data
      $student_id = $_GET["student_id"];
      $name = $_POST["name"];
      $room_number = $_POST["room_number"];
      $contact = $_POST["contact"];
      $sql = "UPDATE residents SET name=?, room_number=?, contact=? WHERE student_id=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "ssss", $name, $room_number, $contact, $student_id);
      $result = mysqli_stmt_execute($stmt);
      mysqli_close($conn);
      header('Location: information.php');
    }
    else {
      // show data
      $student_id = $_GET["student_id"];
      $sql = "SELECT student_id, name, room_number, contact FROM residents WHERE student_id=?";
      $stmt = mysqli_stmt_init($conn);
      mysqli_stmt_prepare($stmt, $sql);
      mysqli_stmt_bind_param($stmt, "s", $student_id);
      $res = mysqli_stmt_execute($stmt);
      if ($res){
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
<div class="container">
  <form action="information_update.php?student_id=<?=$student_id?>&action=confirmed" method="post">
    <div class="mb-3 row">
      <label for="_student_id" class="col-sm-2 col-form-label">學號</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="student_id" id="_student_id" 
          placeholder="學號" value="<?=$student_id?>" required>
      </div>
    </div>
  <div class="mb-3 row">
      <label for="_name" class="col-sm-2 col-form-label">姓名</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" id="_name" 
          placeholder="姓名" value="<?=$name?>" required>
      </div>
    </div>
    <div class="mb-3 row">
      <label for="_room_number" class="col-sm-2 col-form-label">房號</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="room_number" id="_room_number" 
          placeholder="房號" value="<?=$room_number?>" required>
      </div>
    </div>
    <div class="mb-3 row">
      <label for="_contact" class="col-sm-2 col-form-label">聯絡方式</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="contact" id="_contact" 
          placeholder="聯絡方式" value="<?=$contact?>" required>
      </div>
    </div>
    <input class="btn btn-primary" type="submit" value="送出">
  </form>
</div>
<?php
require_once "footer.php";
?>
