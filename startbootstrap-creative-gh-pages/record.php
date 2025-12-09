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
    // 防止 SQL injection，??""是如果 $_POST["searchtxt"] 不存在 或 為 null，就使用 ""（空字串）當作預設值
    $searchtxt = mysqli_real_escape_string($conn, $_POST["searchtxt"] ?? "");

    $where = [];
    if ($searchtxt) {
        $where[] = "(record_id like '%$searchtxt%' or records like '%$searchtxt%' or record_point like '%$searchtxt%')";
    }
    $sql = "SELECT * FROM record";
    // $sql .= 是 字串相加，implode() 會把陣列變成一個字串，如果 $where 陣列裡有條件，就將它們用 AND 串接成 WHERE 子句，並加到 SQL 語句後方
    if (count($where) > 0) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    // 如果 $order 不是空的，就加入 ORDER BY 排序條件
    if ($order) {
        $sql .= " ORDER BY $order";
    }
    // 使用資料庫連線 $conn 執行 SQL 指令 $sql，並把結果存到 $result
    $result = mysqli_query($conn, $sql);
?>

<!-- 將 padding-top 調整為 90px 避免 header 蓋住內容 -->
<div class="container-fluid position-relative" style="padding-top:90px; padding-bottom:120px;">
    <!-- + 按鈕固定右上 -->
     <?php if ($_SESSION['userrole']==='M'): ?> 
                    <a href="record_insert.php" class="btn btn-danger position-fixed" 
                     style="top:3rem; right:1rem; z-index:1050;">＋</a>
                <?php else: 
                    echo " "?>
                <?php endif; ?>


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
                <!-- htmlspecialchars避免 XSS 攻擊 -->
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
// 這是 jQuery 的標準寫法。
// $(document).ready(function() {...})是等整個網頁（DOM）載入完成後，再執行裡面的程式碼。
$(document).ready(function() {
    // 抓取 id="jobTable" 的 HTML 元素
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
