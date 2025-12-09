<?php
session_start();
if (empty($_SESSION['useraccount'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: log_in.php");
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
        $where[] = "(record_id like '%$searchtxt%' or records like '%$searchtxt%' or record_point like '%$searchtxt%')";
    }
    $sql = "SELECT * FROM record";
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
    <a href="record_insert.php" class="btn btn-danger position-fixed" 
   style="top:3rem; right:1rem; z-index:1050;">＋</a>


    <!-- 搜尋與排序表單 -->
    <form action="record.php" method="post" class="row g-2 align-items-center mb-2">
        <div class="col-auto">
            <select name="order" class="form-select">
                <option value="">選擇排序欄位</option>
                <option value="record_id" <?=($order=="record_id")?'selected':''?>>學生帳號</option>
                <option value="records" <?=($order=="records")?'selected':''?>>違規紀錄</option>
                <option value="record_point" <?=($order=="record_point")?'selected':''?>>違規點數</option>
            </select>
        </div>
        <div class="col-auto">
            <input type="text" name="searchtxt" class="form-control" placeholder="搜尋學生帳號/違規紀錄/點數" value="<?=htmlspecialchars($searchtxt)?>">
        </div>
        <div class="col-auto">
            <input type="submit" class="btn btn-info" value="搜尋">
        </div>
    </form>

    <!-- 表格 -->
    <table id="jobTable" class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>違規單號</th>
                <th>學生帳號</th>
                <th>違規紀錄</th>
                <th>違規點數</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?=htmlspecialchars($row["recordnm"])?></td>
                <td><?=htmlspecialchars($row["record_id"])?></td>
                <td><?=htmlspecialchars($row["records"])?></td>
                <td><?=htmlspecialchars($row["record_point"])?></td>
                <td>
                <?php if ($_SESSION['userrole']==='M'): ?> 
                    <a href="record_delete.php?recordnm=<?=$row['recordnm']?>" class="btn btn-danger btn-sm">刪除</a>
                <?php else: 
                    echo " "?>
                <?php endif; ?>
                    
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
