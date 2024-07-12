<?php
session_start();
require_once('dbhelp.php');

if (!isset($_SESSION['user_id'])) { 
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$isUpdating = isset($_GET['id']) && $_GET['id'] != '';

$uploadDir = 'upload/bt/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    chmod($uploadDir, 0777);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($role == 0 && isset($_POST['upload_homework'])) {
        $fileName = basename($_FILES['homework_file']['name']);
        // Thêm đuôi .txt nếu chưa có đuôi mở rộng
        if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'txt') {
            $fileName .= '.txt';
        }
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['homework_file']['tmp_name'], $targetPath)) {
            $student_id = $_POST['student_id'];
            $sql = "INSERT INTO homework_files (teacher_id, student_id, file_name, file_path) VALUES (?, ?, ?, ?)";
            $params = array($user_id, $student_id, $fileName, $targetPath);
            execute($sql, $params);
            echo "Bài tập đã được tải lên thành công.";
        } else {
            echo "Có lỗi xảy ra khi tải lên bài tập.<br>";
            print_r($_FILES['homework_file']);
            echo "Đường dẫn lưu file: " . $targetPath . "<br>";
            echo "Quyền ghi của thư mục: " . (is_writable($uploadDir) ? "Có" : "Không") . "<br>";
        }
    }

    if ($role == 1 && isset($_POST['upload_submission'])) {
        $homework_id = $_POST['homework_id'];
        $fileName = basename($_FILES['submission_file']['name']);
        // Thêm đuôi .txt nếu chưa có đuôi mở rộng
        if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'txt') {
            $fileName .= '.txt';
        }
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $targetPath)) {
            $sql = "INSERT INTO submission_files (homework_id, student_id, file_name, file_path) VALUES (?, ?, ?, ?)";
            $params = array($homework_id, $user_id, $fileName, $targetPath);
            execute($sql, $params);
            echo "Bài làm đã được tải lên thành công.";
        } else {
            echo "Có lỗi xảy ra khi tải lên bài làm.<br>";
            print_r($_FILES['submission_file']);
            echo "Đường dẫn lưu file: " . $targetPath . "<br>";
            echo "Quyền ghi của thư mục: " . (is_writable($uploadDir) ? "Có" : "Không") . "<br>";
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Quản lý bài tập</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Quản lý bài tập</h2>
        
        <?php if ($role == 0) { // Giáo viên ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                <a href="qlds.php" class="btn btn-secondary">Back</a>
                    <h3>Giao bài tập</h3>
                </div>
                <div class="panel-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="student_id">Chọn sinh viên:</label>
                            <select class="form-control" id="student_id" name="student_id">
                                <?php
                                $sql = "SELECT * FROM user WHERE role = 1"; 
                                $students = executeResult($sql);
                                foreach ($students as $student) {
                                    echo "<option value='" . $student['id'] . "'>" . $student['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="homework_file">Chọn file bài tập:</label>
                            <input type="file" class="form-control-file" id="homework_file" name="homework_file">
                        </div>
                        <input type="submit" class="btn btn-primary" name="upload_homework" value="Upload">
                    </form>
                </div>
            </div>
            <br>

            <h3>Danh sách bài làm của sinh viên</h3>
            <?php
            $sql = "SELECT s.*, u.name as student_name FROM submission_files s JOIN user u ON s.student_id = u.id";
            $submissions = executeResult($sql);
            foreach ($submissions as $submission) {
                echo "<p>Sinh viên: " . $submission['student_name'] . " - <a href='" . $submission['file_path'] . "'>Tải bài làm</a> - <button class='btn btn-danger' onclick='deleteFile(" . $submission['id'] . ")'>Xóa</button></p>";
            }
            ?>
        <?php } ?>

        <?php if ($role == 1) { // Sinh viên ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                <a href="qlds.php" class="btn btn-secondary">Back</a>
                    <h3>Nộp bài tập</h3>
                </div>
                <div class="panel-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            
                            <label for="homework_id">Chọn bài tập:</label>
                            <select class="form-control" id="homework_id" name="homework_id">
                                <?php
                                $sql = "SELECT * FROM homework_files WHERE student_id = $user_id"; 
                                $homeworks = executeResult($sql);
                                foreach ($homeworks as $homework) {
                                    echo "<option value='" . $homework['id'] . "'>" . $homework['file_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="submission_file">Chọn file bài làm:</label>
                            <input type="file" class="form-control-file" id="submission_file" name="submission_file">
                        </div>
                        <input type="submit" class="btn btn-primary" name="upload_submission" value="Upload">
                    </form>
                </div>
            </div>
            <br>

            <h3>Danh sách bài tập của bạn</h3>
            <?php
            $sql = "SELECT * FROM homework_files WHERE student_id = $user_id"; 
            $homeworks = executeResult($sql);
            foreach ($homeworks as $homework) {
                echo "<p>Bài tập: " . $homework['file_name'] . " - <a href='" . $homework['file_path'] . "'>Tải về</a></p>";
            }
            ?>
        <?php } ?>
    </div>

    <script type="text/javascript">
        function deleteFile(fileId) {
            if (confirm('Bạn có chắc chắn muốn xóa file này không?')) {
                $.post('delete_file.php', {
                    'file_id': fileId
                }, function(data) {
                    alert(data);
                    location.reload();
                });
            }
        }
    </script>
</body>
</html>
