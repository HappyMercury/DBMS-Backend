<?php
	include 'Database.php';

	//making connection to database
	$db = new Database();
	//representing connection variable to database
	$conn = $db->connect();

	//variables to get from application using POST
	$pid = $_POST["pid"];

	$detailsQuery = 'SELECT pid,name,phone,age,address,email,auth,image FROM Patient WHERE pid=?';

	$stmt = $conn->prepare($detailsQuery);
	$stmt->bind_param("i",$pid);

	//variables for json response
	$error = null;
	$message = null;
	$result = null;

    $patient = null;

	//if it is successfull
	if($stmt->execute())
	{
		$stmt->store_result();
		$stmt->bind_result($pid,$name,$phone,$age,$address,$email,$auth,$image);

		$stmt->fetch();

		$error = false;

		$message = 'success';

		$patient = array('pid' => $pid, 
						'name' => $name,
						'phone' => $phone,
						'age' => $age,
						'address' => $address,
						'email' => $email,
						'auth' => $auth,
						'image' => $image);
		
        if($patient==null)
        {
            $patient = (object) [];
        }

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