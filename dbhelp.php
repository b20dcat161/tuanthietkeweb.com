<?php 
require_once('config.php');

function execute($sql, $params = []) {
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);

   
    $stmt = mysqli_prepare($conn, $sql);

    if ($params) {

        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

   
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

function executeResult($sql, $params = [], $isSingleRecord = false) {
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);

    
    $stmt = mysqli_prepare($conn, $sql);

  
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $list = [];

    if ($isSingleRecord) {
        $list = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $list[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $list;
}
?>
