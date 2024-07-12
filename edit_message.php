<?php
session_start();
require_once('dbhelp.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$message_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($message_id <= 0) {
    echo "ID không hợp lệ";
    exit;
}

$sql = "SELECT * FROM messages WHERE id = ? AND sender_id = ?";
$params = array($message_id, $user_id);
$message = executeResult($sql, true, $params);

if ($message == null) {
    echo "Không tìm thấy tin nhắn";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $new_message = htmlspecialchars($_POST['message'], ENT_QUOTES);
    $sql = "UPDATE messages SET message = ? WHERE id = ?";
    $params = array($new_message, $message_id);
    execute($sql, $params);
    header("Location: chitiet.php?id={$message['receiver_id']}");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Sửa tin nhắn</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h2 class="text-center">Sửa tin nhắn</h2>
    <form method="post">
        <div class="form-group">
            <label for="message">Tin nhắn:</label>
            <textarea class="form-control" id="message" name="message" rows="3" required><?= $message['message'] ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
</body>
</html>
