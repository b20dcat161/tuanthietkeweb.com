<?php
session_start();
require_once('dbhelp.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "ID không hợp lệ";
    exit;
}


$sql = "SELECT * FROM user WHERE id = ?";
$params = array($id);
$user = executeResult($sql, $params, true);

if ($user == null) {
    echo "Không tìm thấy người dùng";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES);


    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $params = array($user_id, $id, $message);
    execute($sql, $params);
}

$sql = "SELECT messages.*, user.name as sender_name FROM messages 
        JOIN user ON messages.sender_id = user.id 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY sent_at DESC";
$params = array($user_id, $id, $id, $user_id);
$messages = executeResult($sql, $params);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Chi tiết người dùng</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
        <a href="qlds.php" class="btn btn-secondary">Back</a>
            <h2 class="text-center">Chi tiết người dùng</h2> 
        </div>
         
        <div class="panel-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td><?= $user['id'] ?></td>
                </tr>
                <tr>
                    <th>Họ & Tên</th>
                    <td><?= $user['name'] ?></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td><?= $user['username'] ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= $user['email'] ?></td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td><?= $user['sdt'] ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?= $user['role'] == 0 ? 'Giáo viên' : 'Sinh viên' ?></td>
                </tr>
              
            </table>

            <h3>Nhắn tin</h3>
            <form method="post">
                <div class="form-group">
                    <label for="message">Tin nhắn:</label>
                    <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gửi</button>
            </form>

            <h3>Danh sách tin nhắn</h3>
            <ul class="list-group">
            <?php foreach ($messages as $msg) { ?>
    <li class="list-group-item">
        <strong><?= $msg['sender_name'] ?>:</strong> <?= $msg['message'] ?>
        <span class="float-right"><?= $msg['sent_at'] ?></span>
       
        <?php if ($msg['sender_id'] == $user_id) { ?>
            <a href="edit_message.php?id=<?= $msg['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
            <a href="delete_message.php?id=<?= $msg['id'] ?>" class="btn btn-sm btn-danger">Xóa</a>
        <?php } ?>
    </li>
<?php } ?>

            </ul>
        </div>
    </div>
</div>
</body>
</html>
