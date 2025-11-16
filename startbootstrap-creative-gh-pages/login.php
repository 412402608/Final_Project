<?php
require_once 'header.php';
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $account = $_POST["account"] ?? "";
    $password = $_POST["password"] ?? "";

    $sql = "SELECT * FROM user WHERE account=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $account);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row["password"]) || $password === $row["password"]) {
    // 登入成功
            $_SESSION["account"] = $account;
            $_SESSION["name"] = $row["name"];
            $_SESSION["role"] = $row["role"];
            $redirect = $_SESSION["redirect_to"] ?? "success.php";
            header("Location:" . $redirect);
            exit;
        } else {
            header("Location: login.php?msg=帳密錯誤");
            exit;
        }
    } else {
        header("Location: login.php?msg=帳號不存在");
        exit;
    }

    mysqli_close($conn);

} else {
    // 顯示登入表單
    echo '
    <form method="POST" action="login.php">
     <section class="page-section" id="contact">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6 text-center">
                        <h2 class="mt-0">登入</h2>
                        <hr class="divider" />
                        <p class="text-muted mb-5">請輸入資料以進入網頁</p>
                    </div>
                </div>
                <form method="POST" action="login.php">
                    <div class="form-floating mb-3">
                        <input class="form-control" name="account" id="account" type="text" placeholder="Account" required>
                       <label for="account">Account</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" name="password" id="password" type="password" placeholder="Password" required>
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

