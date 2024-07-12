<?php
session_start();
require_once('dbhelp.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) { 
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_challenge'])) {
    $uploadDir = 'upload/challenges/';
    $fileName = preg_replace('/\s+/', '_', basename($_FILES['challenge_file']['name']));
    $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '', $fileName); 
    $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.txt'; // Chuyển đổi mặc định về đuôi .txt
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['challenge_file']['tmp_name'], $targetPath)) {
        $hint = $_POST['hint'];
        $sql = "INSERT INTO challenges (file_name, hint) VALUES (?, ?)";
        $params = array($fileName, $hint);
        execute($sql, $params);
        echo "Challenge đã được tạo thành công.";
    } else {
        echo "Có lỗi xảy ra khi tạo challenge.";
    }
   
} elseif (isset($_POST['delete_challenge'])) {
    $challenge_id = $_POST['challenge_id'];
    $sql = "SELECT file_name FROM challenges WHERE id = ?";
    $params = array($challenge_id);


    $challenge = executeResult($sql,  $params,true);
    var_dump($challenge);
    if ($challenge) {
        $filePath = 'upload/challenges/' . $challenge['file_name'];
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
        $sql = "DELETE FROM challenges WHERE id = ?";
        execute($sql, $params);
        echo "Challenge đã được xóa thành công.";
    } else {
        echo "Challenge không tồn tại.";
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Create Challenge</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
    <a href="qlds.php" class="btn btn-secondary">Back</a>
        <h2 class="text-center">Create Challenge</h2>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3>Tạo Challenge</h3>
            </div>
            <div class="panel-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="challenge_file">Chọn file challenge (txt):</label>
                        <input type="file" class="form-control-file" id="challenge_file" name="challenge_file" required>
                    </div>
                    <div class="form-group">
                        <label for="hint">Gợi ý:</label>
                        <input type="text" class="form-control" id="hint" name="hint" required>
                    </div>
                    <input type="submit" class="btn btn-primary" name="create_challenge" value="Create Challenge">
                </form>
            </div>
        </div>
        <br>
 <button onclick="history.back()">Back</button>
        <h3>Danh sách Challenge</h3>
        <div class="panel panel-primary">
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên file</th>
                            <th>Gợi ý</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM challenges";
                        $challenges = executeResult($sql);
                        $index = 1;
                        foreach ($challenges as $challenge) {
                            echo '<tr>
                                <td>' . $index++ . '</td>
                                <td>' . $challenge['file_name'] . '</td>
                                <td>' . $challenge['hint'] . '</td>
                                <td>
                                    <form method="post" style="display:inline-block">
                                        <input type="hidden" name="challenge_id" value="' . $challenge['id'] . '">
                                        <input type="submit" class="btn btn-danger" name="delete_challenge" value="Delete">
                                    </form>
                                </td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
