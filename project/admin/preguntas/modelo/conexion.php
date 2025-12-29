<?php
function conexion(){
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'lead_capture';
    $conn = mysqli_connect($host, $user, $password, $database);
    return $conn;
}
?>
