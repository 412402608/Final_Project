<?php
require_once 'header.php';
require_once 'db1.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 從 $_POST 取出表單欄位 account,password 的值。若沒有此欄位或為 null，就用空字串 "" 當預設
    $account = $_POST["useraccount"] ?? "";
    $password = $_POST["userpassword"] ?? "";
    // 建立 SQL 查詢字串，使用一個 ? 作為參數佔位，準備防止 SQL Injection（會搭配準備語句使用）
    $sql = "SELECT * FROM systemuser WHERE useraccount=?";
    // 使用 mysqli 的準備語句（prepared statement）來準備 SQL。若成功，$stmt 回傳一個 statement 物件
    $stmt = mysqli_prepare($conn, $sql);
    // 將 $account 綁在準備語句的第一個參數（?）上。"s" 表示參數型別為字串（string）。這可防止 SQL Injection，因為資料會被當作參數而非直接拼接到 SQL。
    mysqli_stmt_bind_param($stmt, "s", $account);
    // 執行已綁好參數的準備語句，向資料庫送出查詢。
    mysqli_stmt_execute($stmt);
    // 取得執行後的查詢結果（Result set）
    $result = mysqli_stmt_get_result($stmt);
    // 從結果集中取出第一列（帳號唯一的情況下應只有一列）。mysqli_fetch_assoc 回傳關聯陣列
    if ($row = mysqli_fetch_assoc($result)) {

        if ($password === $row["userpassword"]) {
    // 登入成功的話記住帳密和身份
            $_SESSION["useraccount"] = $account;
            $_SESSION["userdoomnm"] = $row["userdoomnm"];
            $_SESSION["userrole"] = $row["userrole"];
            // 決定登入成功後要導向哪個頁面(有redirect_to就導向redirect_to，否則導向doom_back.php)
            $redirect = $_SESSION["redirect_to"] ?? "doom_back.php";
            header("Location:" . $redirect);
            exit;
        } else {
            header("Location: log_in.php?msg=帳密錯誤");
            exit;
        }
    } else {
        header("Location: log_in.php?msg=帳號不存在");
        exit;
    }

    mysqli_close($conn);

} else {
    // 顯示登入表單
    echo '
    <form method="POST" action="log_in.php">
     <section class="page-section" id="contact">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6 text-center">
                        <h2 class="mt-0">登入</h2>
                        <hr class="divider" />
                        <p class="text-muted mb-5">請輸入資料以進入網頁</p>
                    </div>
                </div>
                <form method="POST" action="log_in.php">
                    <div class="form-floating mb-3">
                        <input class="form-control" name="useraccount" id="account" type="text" placeholder="Account" required>
                       <label for="account">Account</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" name="userpassword" id="password" type="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary btn-xl" type="submit">Submit</button>
                    </div>
                </form>

                    </div>
                </div>
                    </div>
                </div>
            </div>
        </section>
    ';
}
?>
    </body>
<!-- Footer-->
<?php require_once 'footer.php';?>
</html>

