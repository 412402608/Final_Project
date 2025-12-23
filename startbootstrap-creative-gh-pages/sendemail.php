<?php
// PHPMailer 套件的命名空間，若不寫mail的部分就要寫$mail = new PHPMailer\PHPMailer\PHPMailer();
// 不寫命名空間不夠好看
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 載入 dotenv 套件
// 載入 autoload.php
require_once __DIR__ . '../vendor/autoload.php';
// 建立 dotenv 物件並讀取 .env 檔案
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// 交給 try...catch 處理(如錯誤直接跳catch)
$mail = new PHPMailer(true);

// 開始寄信流程
try {
    // SMTP 設定
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['M365_USER'];
    $mail->Password   = $_ENV['M365_PASS'];
    // 使用 STARTTLS 加密，Office365 要求使用
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // 收件人
    $mail->setFrom($_ENV['M365_USER'], '測試寄件者');
    $mail->addAddress('412402608@m365.fju.edu.tw');

    // 郵件內容
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(false);
    $mail->Subject = '{宵禁時間提醒}';
    $mail->Body    = '請注意宵禁時間00:00-06:00，未簽到者將視為缺席。';

    // 寄出郵件
    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}
?>
