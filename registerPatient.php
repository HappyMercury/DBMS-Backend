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

	//variables for json response
	$error = null;
	$message = null;
	$result = null;

	//if it is successfull
	if($stmt->execute())
	{
		$stmt->store_result();
		$stmt->bind_result($pid,$name,$phone,$age,$address,$email,$auth);

		$stmt->fetch();

		$error = false;

		$message = 'success';

		$patient = array('pid' => $pid, 
						'name' => $name,
						'phone' => $phone,
						'age' => $age,
						'address' => $address,
						'email' => $email,
						'auth' => $auth);
						
		$result['patient'] = $patient;

		http_response_code(200);
	}
	//on failure
	else{
		
		$error = true;

		$message = 'failure';

		$result = null;

		http_response_code(200);
	}

	//sending json data back to user
	$data = array(
		'error' => $error,
		'message' => $message,
		'result' => $result
	);

	$json = json_encode($data);

	echo $json;
	

	$conn->close();
	$stmt->close();

?>