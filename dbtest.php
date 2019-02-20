<?php

$conn =  new mysqli("localhost","sarotam","int0uch2718","sarotam_db");
$query = "select * from it_users where id = 2";
$result = $conn->query($query);
$obj = $result->fetch_object();
$result->close();
echo $obj->id." ".$obj->username;
//print_r($result);

?>
