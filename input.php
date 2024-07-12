<?php
require_once('dbhelp.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$isUpdating = isset($_GET['id']) && $_GET['id'] != '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $s_name = $s_email = $s_sdt = $s_username = $s_password =$s_role='';


    if (isset($_POST['email'])) {
        $s_email = $_POST['email'];
    }
    if (isset($_POST['sdt'])) {
        $s_sdt = $_POST['sdt'];
    }
    if (isset($_POST['usr'])) {
        $s_name = $_POST['usr'];
    }
    if (isset($_POST['username'])) {
        $s_username = $_POST['username'];
    }
    if (isset($_POST['password'])) {
        $s_password = $_POST['password'];
    }
	if (isset($_POST['role'])) {
        $s_role = $_POST['role'];
    } 
    if ($role == 0) { // Giáo viên
        if ($isUpdating) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM user WHERE username=? AND id != ?";
            $params = array($s_username, $id);
            $result = executeResult($sql, $params, true);
            if ($result) {
                echo 'Tên người dùng đã tồn tại. Vui lòng chọn tên người dùng khác.';
                exit();
            } else {
                $sql = "UPDATE user SET name=?, email=?, sdt=?, username=?, password=? WHERE id=?";
                $params = array($s_name, $s_email, $s_sdt, $s_username, $s_password, $id);
                execute($sql, $params);
                header('Location: qlds.php');
                exit();
            }
        } else {
            $sql = "SELECT * FROM user WHERE username=?";
            $params = array($s_username);
            $result = executeResult($sql, $params, true);
            if ($result) {
                echo 'Tên người dùng đã tồn tại. Vui lòng chọn tên người dùng khác.';
                exit();
            } else {
                $sql = "INSERT INTO user(name, email, sdt, username, password, role) VALUES (?, ?, ?, ?, ?, ?)";
                $params = array($s_name, $s_email, $s_sdt, $s_username, $s_password, $s_role);
                execute($sql, $params);
                header('Location: qlds.php');
                exit();
            }
        }
    } elseif ($role == 1 && $isUpdating && $_GET['id'] == $user_id) {
        $sql = "UPDATE user SET email=?, sdt=?, name=? WHERE id=?";
        $params = array($s_email, $s_sdt, $s_name, $user_id);
        execute($sql, $params);
        header('Location: qlds.php');
        exit();
    } else {
        echo 'Bạn không có quyền truy cập trang này.';
        exit();
    }
}

$s_name = $s_email = $s_sdt = $s_username = $s_password = '';
if ($isUpdating) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM user WHERE id = ?";
    $params = array($id);
    $user = executeResult($sql, $params, true);
    if ($user != null) {
        $s_name = $user['name'];
        $s_email = $user['email'];
        $s_sdt = $user['sdt'];
        $s_username = $user['username'];
        $s_password = $user['password'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <button onclick="history.back()">Back</button>
                <h2 class="text-center"><?= $isUpdating ? 'Cập nhật thông tin' : 'Thêm sinh viên' ?></h2>
            </div>
            <div class="panel-body">
                <form method="post">
                    <?php if ($role == 0) { // Giáo viên ?>
                        <?php if (!$isUpdating) { ?>
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input required="true" type="text" class="form-control" id="usr" name="usr" value="<?= $s_name ?>">
                            </div>
							<div class="form-group">
                        <label for="email">Email:</label>
                        <input required="true" type="email" class="form-control" id="email" name="email" value="<?= $s_email ?>">
                    </div>
                    <div class="form-group">
                        <label for="sdt">SDT:</label>
                        <input required="true" type="text" class="form-control" id="sdt" name="sdt" value="<?= $s_sdt ?>">
                    </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input required="true" type="text" class="form-control" id="username" name="username" value="<?= $s_username ?>">
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input required="true" type="password" class="form-control" id="password" name="password">
                            </div>
							<div class="form-group">
                        <label for="sdt">Role:</label>
                        <input required="true" type="text" class="form-control" id="role" name="role" >
                    </div>
                        <?php } else { ?>

                            <div class="form-group">
                                <label for="usr">Name:</label>
                                <input required="true" type="text" class="form-control" id="usr" name="usr" value="<?= $s_name ?>">
                            </div>
								<div class="form-group">
                        <label for="email">Email:</label>
                        <input required="true" type="email" class="form-control" id="email" name="email" value="<?= $s_email ?>">
                    </div>
                    <div class="form-group">
                        <label for="sdt">SDT:</label>
                        <input required="true" type="text" class="form-control" id="sdt" name="sdt" value="<?= $s_sdt ?>">
                    </div>
					<div class="form-group">
                                <label for="username">Username:</label>
                                <input required="true" type="text" class="form-control" id="username" name="username" value="<?= $s_username ?>">
                            </div>

							<div class="form-group">
                                <label for="password">Password:</label>
                                <input required="true" type="password" class="form-control" id="password" name="password" value="<?= $s_password ?>">
					        </div>
				</div>
                        <?php } ?>
                    <?php } else { // Sinh viên ?>
                        <div class="form-group">
                            <label for="usr">Name:</label>
                            <input required="true" type="text" class="form-control" id="usr" name="usr" value="<?= $s_name ?>">
                        </div>

						<div class="form-group">
                        <label for="email">Email:</label>
                        <input required="true" type="email" class="form-control" id="email" name="email" value="<?= $s_email ?>">
                    </div>
                    <div class="form-group">
                        <label for="sdt">SDT:</label>
                        <input required="true" type="text" class="form-control" id="sdt" name="sdt" value="<?= $s_sdt ?>">
                    </div>
                    <?php } ?>
                    
                    <button class="btn btn-success"><?= $isUpdating ? 'Update' : 'Add' ?></button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
