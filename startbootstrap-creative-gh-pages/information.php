<?php
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $room_number = $_POST['room_number'];
    $contact = $_POST['contact'];

    $stmt = $pdo->prepare("INSERT INTO Residents (student_id, name, room_number, contact) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$student_id, $name, $room_number, $contact])) {
        $message = "住民新增成功！";
    } else {
        $message = "新增失敗";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>新增住民</title>
</head>
<body>
    <h1>新增住民資料</h1>

    <?php if($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        學號: <input type="text" name="student_id" required><br><br>
        姓名: <input type="text" name="name" required><br><br>
        房號: <input type="text" name="room_number"><br><br>
        聯繫方式: <input type="text" name="contact"><br><br>
        <button type="submit">新增住民</button>
    </form>
</body>
</html>
