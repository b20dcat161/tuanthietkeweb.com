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
    echo "Không tìm thấy tin nhắn hoặc bạn không có quyền xóa tin nhắn này";
    exit;
}


$sql = "DELETE FROM messages WHERE id = ?";
$params = array($message_id);
execute($sql, $params);

header("Location: chitiet.php?id={$message['receiver_id']}");
exit();
?>
