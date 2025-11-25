
<?php
require_once "header.php";
session_start();
require_once "db1.php";

// 檢查登入


// 取得登入者資訊
$useraccount = $_SESSION["useraccount"] ?? "";
$userrole = $_SESSION["userrole"] ?? "";
$userdoomnm = $_SESSION["userdoomnm"] ?? "";

// 若未登入 → 退回登入頁
if ($useraccount == "") {
    header("Location: login.php");
    exit;
}

// 當使用者按下「簽到」
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 如果是管理員，可以選擇要登記哪位住民
    if ($userrole == "M") {
        $targetAccount = $_POST["targetAccount"];
        
        // 取得該住民的房號
        $sql = "SELECT userdoomnm FROM systemuser WHERE useraccount=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $targetAccount);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $targetDoom = $row["userdoomnm"] ?? "";
    } 
    // 若是住民本人，直接用自己的資料
    else {
        $targetAccount = $useraccount;
        $targetDoom = $userdoomnm;
    }

    // 新增回報紀錄
    $sql = "INSERT INTO returnlog (useraccount, userdoomnm, returntime)
            VALUES (?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $targetAccount, $targetDoom);
    mysqli_stmt_execute($stmt);

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
                                $sql = "SELECT useraccount FROM systemuser WHERE userrole='S'";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
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
require_once 'footer.php';
?>
