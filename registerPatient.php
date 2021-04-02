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

	//making prepared query
	$stmt = $conn->prepare("INSERT INTO Patient (name,phone,age,address,email,password,auth) VALUES(?,?,?,?,?,?,?)");

	//binding parameters for placeholder markers
	$stmt->bind_param("ssisssi",$name,$phone,$age,$address,$email,$password,$auth);

	//executing query
	$stmt->execute();

	$detailsQuery = 'SELECT pid,name,phone,age,address,email,auth FROM Patient WHERE email=? AND password=?';

	$stmt = $conn->prepare($detailsQuery);
	$stmt->bind_param("ss",$email,$password);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($pid,$name,$phone,$age,$address,$email,$auth);

	$stmt->fetch();

	$data = array('pid' => $pid, 
					'name' => $name,
					'phone' => $phone,
					'age' => $age,
					'address' => $address,
					'email' => $email,
					'auth' => $auth);

	$json = json_encode($data);

	echo $json;

	$conn->close();
	$stmt->close();

?>
