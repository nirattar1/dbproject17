<?php
/* The function initialize a coonection to the DB */
function connect(){
    $servername = "mysqlsrv.cs.tau.ac.il";
    $username = "DbMysql07";
    $password = "DbMysql07";
    $dbname = "DbMysql07";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>