<?php
require_once "header.php";
require_once "db.php";
$error = "";
$msg = "";
$filename = "";
if($_POST){
  // 定義錯誤訊息陣列
  $phpFileUploadErrors = [
    0 => '上傳成功',
    1 => '檔案大小超過伺服器設定',
    2 => '檔案大小超過表單限制',
    3 => '上傳檔案不完整，請重新上傳',
    4 => '未上傳檔案',
    6 => '伺服器臨時目錄不存在',
    7 => '無法寫入檔案，請檢查權限設定',
    8 => 'PHP擴充導致檔案無法上傳',
  ];
  
  // 檢查是否有檔案被上傳
  if(!isset($_FILES["fileToUpload"])) {
    $error = "上傳錯誤";
    $msg = "檔案上傳失敗，請檢查表單設定";
  } elseif ($_FILES["fileToUpload"]["error"] != 0) {
    // 處理上傳錯誤
    $error_code = $_FILES["fileToUpload"]["error"];
    $error = "上傳失敗";
    $msg = $phpFileUploadErrors[$error_code] ?? "未知的上傳錯誤";
    
    // 特別處理檔案大小相關的錯誤
    if ($error_code == 1 || $error_code == 2) {
      $error = "檔案過大";
    } elseif ($error_code == 4) {
      $error = "未選擇檔案";
    }
  } else {
    $target_dir = "uploads/";
    
    // 檢查目錄是否存在
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0755, true);
    }
    
    // 清理檔案名稱，防止路徑遍歷攻擊
    $original_filename = basename($_FILES["fileToUpload"]["name"]);
    $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    
    // 允許的檔案類型
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
      $error = "檔案類型不允許";
      $msg = "只允許上傳 JPG, JPEG, PNG, GIF 檔案";
    } else {
      // 檢查檔案內容是否為有效的圖片
      $image_info = @getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if ($image_info === false) {
        $error = "檔案類型不允許";
        $msg = "上傳的檔案不是有效的圖片檔案";
      } elseif (!in_array($image_info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF])) {
        $error = "檔案類型不允許";
        $msg = "只允許上傳 JPG, JPEG, PNG, GIF 圖片檔案";
      } else {
        // 檢查檔案大小 (限制 2MB)
        $max_file_size = 2 * 1024 * 1024; // 2MB
        if ($_FILES["fileToUpload"]["size"] > $max_file_size) {
          $error = "檔案過大";
          $msg = "檔案大小不能超過 2MB";
        } else {
        // 產生安全的檔案名稱
        $safe_filename = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $original_filename);
        $filename = $target_dir . $safe_filename;
        
        // 移動檔案
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename)) {
          $msg = "檔案上傳成功！";
        } else {
          $error = "上傳失敗";
          $msg = "檔案上傳失敗，請檢查權限設定";
          $filename = ""; // 清空檔案名稱
        }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>檔案上傳</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .upload-form { margin: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="upload-form">
        <h2>檔案上傳</h2>
        <form action="fileupload.php" method="post" enctype="multipart/form-data">
            <label for="fileToUpload">選擇圖片 (只允許 JPG, JPEG, PNG, GIF，最大 2MB):</label><br>
            <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" required><br><br>
            <input type="submit" value="上傳檔案" name="submit">
        </form>
        
        <?php if ($msg): ?>
            <p class="<?= $error ? 'error' : 'success' ?>">
                <?= htmlspecialchars($msg) ?>
            </p>
        <?php endif; ?>
        
        <?php if ($filename && file_exists($filename)): ?>
            <div style="margin-top: 20px;">
                <h3>上傳的檔案：</h3>
                <img src="<?= htmlspecialchars($filename) ?>" alt="上傳的圖片" style="max-width: 30%; height: auto; border: 1px solid #ccc;">
                <p><strong>檔案名稱：</strong> <?= htmlspecialchars(basename($filename)) ?></p>
                <p><strong>檔案大小：</strong> <?= number_format(filesize($filename)) ?> bytes</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
require_once 'footer.php';
?>
