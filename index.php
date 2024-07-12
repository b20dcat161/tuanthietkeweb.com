<?php
session_start();
require_once('dbhelp.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM user WHERE username = ? AND password = ?";
    $params = [$username, $password];
    $user = executeResult($sql, $params, true);


    if ($user != null) {
    
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: qlds.php');
        exit();
    } else {
   
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng';
    }
}


?>



<!DOCTYPE html>
<html>

<head>
    <title>Đăng nhập</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: Roboto, -apple-system, BlinkMacSystemFont, Segoe UI, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 100px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            height: 55px;
            font-weight: 300;
        }

        .container img {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            margin-left: 100px;
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 1px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin: 1px 0;
            border: none;
            border-radius: 1px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        label {
            margin-top: 10px;
            display: block;
        }

        h5 {
            font-weight: 300;
            color: black;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <form action="index.php" method="post">
        <div class="container">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR-Hyds8h5bI6h9Xa4czHnLTa94FTwXP9aHLw&s" alt="icon">
            <h1>Quản lý sinh viên</h1>
            <label for="username"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="username" required>

            <label for="password"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" required>

            <button type="submit">Login</button>
            <?php if (isset($error)) { ?>
                <div class="error"><?= $error ?></div>
            <?php } ?>
        </div>
    </form>
    <h5>Design by ngosytuan ©</h5>
</body>

</html>
