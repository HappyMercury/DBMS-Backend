<?php

$email = $_POST["email"];
$password = $_POST["password"];
$password = md5($password);

$conn = mysqli_connect("localhost","id14974971_group60","Bm?{dIi5xt{StTux","id14974971_dbms_project");

if(mysqli_connect_error())
{
    http_response_code(500);
}

$query = ("SELECT pid from Patient WHERE email='{$email}' AND password='{$password}'");

$result = mysqli_query($conn,$query); 

if($row = (mysqli_fetch_array($result))>1) 
{
    http_response_code(200);
}
else
{
    http_response_code(401);
}

?>
