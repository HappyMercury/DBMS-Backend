<?php

//including the connection file for database
// include "Database.php";

//getting values from user
$name = $_POST["name"];
$phone = $_POST["phone"];
$age = $_POST["age"];
$address = $_POST["address"];
$email = $_POST["email"];
$password = $_POST["password"];
$password = md5($password);
$auth = $_POST["auth"];

$conn = mysqli_connect("localhost","id14974971_group60","Bm?{dIi5xt{StTux","id14974971_dbms_project");

// $conn = new Database();
// $conn->connect();

// $query = "INSERT INTO Patient( name, phone, age, address, email, password, auth) VALUES ('$name','$phone',$age,'$address','$email','$password',$auth)";

// if(mysqli_query($conn,$query))
// {
//     echo 'Successful';
// }
// else
// {
//     echo 'unsuccessful';
// }

$stmt = $conn->prepare("INSERT INTO Patient (name,phone,age,address,email,password,auth) VALUES(?,?,?,?,?,?,?)");

//the first string refers to the type of data inputted respectively
$stmt->bind_param("ssisssi",$name,$phone,$age,$address,$email,$password,$auth);

$stmt->execute();

$conn->close();

?>
