<?php
require_once('dbhelp.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];


if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>QLSV</title>
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
                Quản lý thông tin sinh viên
        
                <form method="post" style="float: right;">
                    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                </form>
                <form method="get">
                    <input type="text" name="s" class="form-control" style="margin-top: 15px; margin-bottom: 15px;" placeholder="Tìm kiếm theo tên">
                </form>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Họ & Tên</th>
                            <th>Role</th>
						
							<th width="80px"></th>
                            <th width="90px"></th>
                            <th width="80px"></th>
							<th width="80px" >Exam</th>
							
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = 'SELECT * FROM user';
                    $studentList = executeResult($sql);
                    $index = 1;
					
                    foreach ($studentList as $std) {
                        echo '<tr>
                                <td>'.($index++).'</td>
                                <td>'.$std['name'].'</td>
                                <td>'.($std['role'] == 0 ? 'Giáo viên' : 'Sinh viên').'</td>';
								if ($role == 0) { 
									echo '<td><button class="btn btn-success" onclick=\'window.open("chitiet.php?id='.$std['id'].'","_self")\'>Detail</button></td>
										  <td><button class="btn btn-warning" onclick=\'window.open("input.php?id='.$std['id'].'","_self")\'>Edit</button></td>';
									       
									echo  '<td><button class="btn btn-danger" onclick="deleteStudent('.$std['id'].')">Delete</button></td>';
									if ($std['role'] == 1) { 
										echo '<td><button class="btn btn btn-secondary" onclick=\'window.open("bt.php?id='.$std['id'].'","_self")\'>Giao</button></td>';}
								} elseif ($std['id'] == $user_id) { 
									echo '<td colspan="2"><button class="btn btn-success" onclick=\'window.open("chitiet.php?id='.$std['id'].'","_self")\'>Detail</button></td>
										  <td><button class="btn btn-warning" onclick=\'window.open("input.php?id='.$std['id'].'","_self")\'>Edit</button></td>
										 <td><button class="btn btn btn-secondary" onclick=\'window.open("bt.php?id='.$std['id'].'","_self")\'>BT</button></td>';
								} else { 
									echo '<td colspan="3"><button class="btn btn-success" onclick=\'window.open("chitiet.php?id='.$std['id'].'","_self")\'>Detail</button></td>';								}
								echo '</tr>';
							
						}
                    ?>
                    </tbody>
                </table>
                <?php if ($role == 0) {  ?>
                <button class='btn btn-success' onclick="window.open('input.php','_self')">Add</button>
				<button class='btn btn-info' onclick="window.open('create_challenge.php','_self')">add challenge</button>
                <?php } 
				
				
				?>
				   <?php if ($role == 1) {  ?>
                
				<button class='btn btn-info' onclick="window.open('view_challenge.php','_self')">challenge</button>
                <?php } 
				
				
				?>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function deleteStudent(id) {
            option = confirm('Bạn có chắc chắn muốn xóa sinh viên này không?')
            if(!option) {
                return;
            }
            $.post('delete_student.php', {
                'id': id
            }, function(data) {
                alert(data);
                location.reload();
            });
        }
    </script>
</body>
</html>
