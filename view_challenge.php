<?php
session_start();
require_once('dbhelp.php');

if (!isset($_SESSION['user_id'])) { 
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_answer'])) {
    $challenge_id = $_POST['challenge_id'];
    $answer = preg_replace('/\s+/', '_', $_POST['answer']);
    $answer = preg_replace('/[^A-Za-z0-9_\-]/', '', $answer); 

    $sql = "SELECT * FROM challenges WHERE id = ?";
    $params = array($challenge_id);
    $challenge = executeResult($sql, $params,true);
    echo($answer);
    echo($params[0]);
    echo (pathinfo($challenge['file_name'], PATHINFO_FILENAME));
    if ($challenge && $answer == pathinfo($challenge['file_name'], PATHINFO_FILENAME)) {
        $filePath = 'upload/challenges/' . $challenge['file_name'];
        if (file_exists($filePath)) {
     
            $content = file_get_contents($filePath);
            $success = true;
        }
    } else {
        $success = false;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>View Challenge</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">View Challenge</h2>
        <div class="panel panel-primary">
            <div class="panel-heading">
            <a href="qlds.php" class="btn btn-secondary">Back</a>
                <h3>Chọn Challenge</h3>
            </div>
            <div class="panel-body">
                <form method="post">
                    <div class="form-group">
                        <label for="challenge_id">Chọn challenge:</label>
                        <select class="form-control" id="challenge_id" name="challenge_id" required>
                            <?php
                            $sql = "SELECT * FROM challenges";
                            $challenges = executeResult($sql);
                            $index = 1;
                            foreach ($challenges as $challenge) {
                                echo "<option value='" . $challenge['id'] . "'>Challenge " . $index . ": " . $challenge['hint'] . "</option>";
                                $index++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="answer">Nhập đáp án:</label>
                        <input type="text" class="form-control" id="answer" name="answer" required>
                    </div>
                    <input type="submit" class="btn btn-primary" name="submit_answer" value="Submit">
                </form>
                <?php if (isset($success) && $success) { ?>
                    <div class="panel panel-success" style="margin-top: 20px;">
                        <div class="panel-heading">
                            <h3>Nội dung bài tập</h3>
                        </div>
                        <div class="panel-body">
                            <pre><?= htmlspecialchars($content) ?></pre>
                        </div>
                    </div>
                <?php } elseif (isset($success) && !$success) { ?>
                    <div class="alert alert-danger" style="margin-top: 20px;">
                        Đáp án không đúng. Vui lòng thử lại.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
