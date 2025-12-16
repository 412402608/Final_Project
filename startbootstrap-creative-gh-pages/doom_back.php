
<?php
require_once "header.php";
require_once "db.php";

// 檢查登入


// 取得登入者資訊 
// 從 session 中取出 key 的值，否則以空字串代替。
$useraccount = $_SESSION["useraccount"] ?? "";
$userrole = $_SESSION["userrole"] ?? "";
$userdoomnm = $_SESSION["userdoomnm"] ?? "";

// 若未登入，退回登入頁
if ($useraccount == "") {
    header("Location: log_out.php");
    exit;
}

// 當使用者按下「簽到」
// $_SERVER["REQUEST_METHOD"]是取得 HTTP 請求方法。
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 如果是管理員，可以選擇要登記哪位住民
    if ($userrole == "M") {
        $targetAccount = $_POST["targetAccount"];
        
        // 取得該住民的房號
        $sql = "SELECT userdoomnm FROM systemuser WHERE useraccount=?";
        // 將 SQL 語句轉成準備語句，準備後可以安全地綁定參數。
        $stmt = mysqli_prepare($conn, $sql);
        // 綁定參數到準備語句裡的 ?
        mysqli_stmt_bind_param($stmt, "s", $targetAccount);
        // 執行SELECT userdoomnm FROM systemuser WHERE useraccount='$targetAccount'
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        // mysqli_fetch_assoc() 會回傳一個 關聯陣列
        $row = mysqli_fetch_assoc($result);
        $targetDoom = $row["userdoomnm"] ?? "";
    } 
    // 若是住民本人，直接用自己的資料
    else {
        $targetAccount = $useraccount;
        $targetDoom = $userdoomnm;
    }

    // 新增回報紀錄
    $sql = "INSERT INTO returnlog (resident, returntime)
            VALUES (?, NOW())";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $targetAccount);

    mysqli_stmt_execute($stmt) or die("Execute failed: " . mysqli_stmt_error($stmt));

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: yes.php");
    exit;
}
?>

<section class="page-section bg-primary" id="about">
    <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-lg-8 text-center">

                <h2 class="text-white mt-0">宿舍返回簽到</h2>
                <hr class="divider divider-light" />
                <p class="text-white-75 mb-4">
                    <?php if ($userrole == "M") { ?>
                        管理員可替住民登記已返回宿舍
                    <?php } else { ?>
                        按下按鈕以回報你已返回宿舍
                    <?php } ?>
                </p>

                <form method="POST" action="">

                    <!-- 管理員可選住民帳號 -->
                    <?php if ($userrole == "M") { ?>
                        <div class="form-floating mb-3">
                            <select class="form-control" name="targetAccount" required>
                                <option value="" disabled selected>請選擇住民帳號</option>
                                
                                <?php
                                // 存取多筆資料的集合
                                $sql = "SELECT useraccount FROM systemuser WHERE userrole='S'";
                                $result = mysqli_query($conn, $sql);
                                // 從查詢結果中取出一筆資料
                                while ($row = mysqli_fetch_assoc($result)) {
                                // 這行輸出 HTML <option> 標籤，用於 <select> 下拉選單。讓管理員可選擇簽到帳號。
                                // value='...' → <option> 的值，送表單時會傳給 PHP

                                    echo "<option value='" . $row["useraccount"] . "'>" . $row["useraccount"] . "</option>";
                                }
                                ?>
                            </select>
                            <label>住民帳號</label>
                        </div>
                    <?php } ?>

                    <button class="btn btn-light btn-xl" type="submit">簽到</button>
                </form>

            </div>
        </div>
    </div>
</section>
<?php
try {
    $order = $_POST["order"]??"";
    $searchtxt = mysqli_real_escape_string($conn, $_POST["searchtxt"] ?? "");

    $where = [];
    if ($searchtxt) {
        $where[] = "(resident like '%$searchtxt%' or returntime like '%$searchtxt%')";
    }
    if ($userrole == "M") {
        $sql = "SELECT * FROM returnlog";
        // 將 SQL 語句轉成準備語句，準備後可以安全地綁定參數。
        $stmt = mysqli_prepare($conn, $sql);
        // 執行(寫進資料庫)
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }else{
        $sql = "SELECT * FROM returnlog WHERE resident = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $useraccount);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
    }
    if (count($where) > 0) {
        // implode 是 PHP 函數，把陣列元素用指定字串連接成一個字串
        // SELECT * FROM returnlog WHERE resident LIKE '%user%' AND returntime > $today
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    if ($order) {
        $sql .= " ORDER BY $order";
    }
    mysqli_stmt_execute($stmt);
    mysqli_close($conn); 
} catch(Exception $e) {
    echo 'Message: ' . $e->getMessage();
}
?>

<!-- 將 padding-top 調整為 90px 避免 header 蓋住內容 -->
<div class="container-fluid position-relative" style="padding-top:90px; padding-bottom:120px;">


    <!-- 搜尋與排序表單 -->
    <form action="doom_back.php" method="post" class="row g-2 align-items-center mb-2">
        <div class="col-auto">
            <select name="order" class="form-select">
                <option value="">選擇排序欄位</option>
                <option value="resident" <?=($order=="resident")?'selected':''?>>使用者</option>
                <option value="returntime" <?=($order=="returntime")?'selected':''?>>簽到時間</option>
            </select>
        </div>
        <div class="col-auto">
            <input type="text" name="searchtxt" class="form-control" placeholder="搜尋使用者/簽到時間" value="<?=htmlspecialchars($searchtxt)?>">
        </div>
        <div class="col-auto">
            <input type="submit" class="btn btn-info" value="搜尋">
        </div>
    </form>

    <!-- 表格 -->
    <table id="jobTable" class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>使用者</th>
                <th>簽到時間</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr> 
                <td><?=htmlspecialchars($row["resident"])?></td>
                <td><?=htmlspecialchars($row["returntime"])?></td>
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

date_default_timezone_set('Asia/Taipei');

// 1️⃣ 取得今天日期與今晚 23:00
$today = date('Y-m-d');
$deadline = $today . " 23:00:00";

// 只在已到當天 23:00 且非管理者時執行寄信檢查，避免中途 terminate 頁面
if (date('Y-m-d H:i:s') >= $deadline && $userrole !== "M") {
    // 查詢今天是否有任何簽到紀錄(只要第一筆資料)
    $sql = "
        SELECT 1 
        FROM returnlog 
        WHERE DATE(returntime) = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // 如果沒有任何紀錄 → 啟動寄信
    if (mysqli_num_rows($result) === 0) {
        require_once("sendemail.php");
    }
}


?>


<?php

require_once 'footer.php';
?>
