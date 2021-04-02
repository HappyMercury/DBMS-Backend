<?php
	include 'Database.php';

	//making connection to database
	$db = new Database();
	//representing connection variable to database
	$conn = $db->connect();

	//variables to get from application using POST
	$name = $_POST["name"];
	$phone = $_POST["phone"];
	$age = $_POST["age"];
	$address = $_POST["address"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	$password = md5($password);
	$auth = $_POST["auth"];
	$hospital = $_POST['hospital'];
	$department = $_POST['department'];

	//first registering hospital
	// $query = "INSERT INTO hospital VALUES "

	//making prepared query
	$stmt = $conn->prepare("INSERT INTO doctor (name,phone,age,address,email,password,auth,dnum,hnum) VALUES(?,?,?,?,?,?,?,?,?)");

	//binding parameters for placeholder markers
	$stmt->bind_param("ssisssiii",$name,$phone,$age,$address,$email,$password,$auth,$dnum,$hnum);

	//executing query
	$stmt->execute();

	$conn->close();
	$stmt->close();

?>