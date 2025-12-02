<?php
session_start();
if (empty($_SESSION['useraccount'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}
include('header.php');
?>
<?php


try {
    require_once 'db1.php';
    $order = $_POST["order"]??"";
    $searchtxt = mysqli_real_escape_string($conn, $_POST["searchtxt"] ?? "");

    $where = [];
    if ($searchtxt) {
        $where[] = "(student_id like '%$searchtxt%' or name like '%$searchtxt%' or room_number like '%$searchtxt%' or contact like '%$searchtxt%')";
    }
    $sql = "SELECT * FROM residents";
    if (count($where) > 0) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    if ($order) {
        $sql .= " ORDER BY $order";
    }
    $result = mysqli_query($conn, $sql);
?>

<!-- 將 padding-top 調整為 90px 避免 header 蓋住內容 -->
<div class="container-fluid position-relative" style="padding-top:90px; padding-bottom:120px;">
    <!-- + 按鈕固定右上 -->
    <a href="information_insert.php" class="btn btn-danger position-fixed" 
   style="top:3rem; right:1rem; z-index:1050;">＋</a>


    <!-- 搜尋與排序表單 -->
    <form action="information.php" method="post" class="row g-2 align-items-center mb-2">
        <div class="col-auto">
            <select name="order" class="form-select">
                <option value="">選擇排序欄位</option>
                <option value="student_id" <?=($order=="student_id")?'selected':''?>>學號</option>
                <option value="name" <?=($order=="name")?'selected':''?>>姓名</option>
                <option value="room_number" <?=($order=="room_number")?'selected':''?>>房號</option>
                <option value="contact" <?=($order=="contact")?'selected':''?>>聯絡方式</option>
            </select>
        </div>
        <div class="col-auto">
            <input type="text" name="searchtxt" class="form-control" placeholder="搜尋學號/姓名/房號/聯絡方式" value="<?=htmlspecialchars($searchtxt)?>">
        </div>
        <div class="col-auto">
            <input type="submit" class="btn btn-info" value="搜尋">
        </div>
    </form>

    <!-- 表格 -->
    <table id="jobTable" class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>學號</th>
                <th>姓名</th>
                <th>房號</th>
                <th>聯絡方式</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?=htmlspecialchars($row["student_id"])?></td>
                <td><?=htmlspecialchars($row["name"])?></td>
                <td><?=htmlspecialchars($row["room_number"])?></td>
                <td><?=htmlspecialchars($row["contact"])?></td>
                <td>
                    <a href="information_insert.php?student_id=<?=$row["student_id"]?>" class="btn btn-primary btn-sm">新增</a>
                    <a href="information_delete.php?student_id=<?=$row['student_id']?>" class="btn btn-danger btn-sm">刪除</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#jobTable').DataTable({
        "paging": true,      // 分頁
        "ordering": true,    // 排序
        "searching": false   // 關閉搜尋框
    });
});
</script>

<?php
    mysqli_close($conn); 
} catch(Exception $e) {
    echo 'Message: ' . $e->getMessage();
}
require_once "footer.php"; 
?>
