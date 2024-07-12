<?php
require_once('dbhelp.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'Bạn cần đăng nhập để thực hiện chức năng này.';
    exit();
}

if ($_SESSION['role'] != 0) {
    echo 'Bạn không có quyền thực hiện chức năng này.';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        $sql = "DELETE FROM user WHERE id = ?";
        $params = array($id);
        execute($sql, $params);

        echo 'Xóa sinh viên thành công';
    } else {
        echo 'Không nhận được ID sinh viên';
    }
}
?>
