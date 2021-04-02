<?php
	include 'Database.php';

	//making connection to database
	$db = new Database();
	//representing connection variable to database
	$conn = $db->connect();

	//variables to get from application using POST
	$name = $_POST["name"];
	$phone = $_POST["phone"];
	$specialization = $_POST["age"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	$password = md5($password);
	$auth = $_POST["auth"];
	$hospital = $_POST['hospital'];
	$department = $_POST['department'];

	//for department
	$dnumQuery = "SELECT dep_id FROM department WHERE dep_name=?";

	$deptStmt = $conn->prepare($dnumQuery);
	$deptStmt->bind_param("s",$department);
	$deptStmt->execute();
	$deptStmt->store_result();


	if($deptStmt->num_rows>0){
		$deptStmt->bind_result($dno);
		$deptStmt->fetch();
		$dnum = $dno;
	}
	else{
		$dnumQuery = "INSERT INTO department (dep_name) VALUES(?);";

		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("s",$department);
		$deptStmt->execute();

		$dnumQuery = "SELECT dep_id FROM department WHERE dep_name=?";

		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("s",$department);
		$deptStmt->execute();
		$deptStmt->store_result();
		$deptStmt->bind_result($dno);
		$deptStmt->fetch();
		$dnum = $dno;
	}

	//for hospital
	$hnumQuery = "SELECT hid FROM hospital WHERE hname=?";

	$deptStmt = $conn->prepare($hnumQuery);
	$deptStmt->bind_param("s",$hospital);
	$deptStmt->execute();
	$deptStmt->store_result();
	$deptStmt->bind_result($hno);
	$deptStmt->fetch();
	$hnum = $hno;

	//making prepared query
	$stmt = $conn->prepare("INSERT INTO doctor (name,specialization,phone,email,password,auth,dnum,hnum) VALUES(?,?,?,?,?,?,?,?)");

	//binding parameters for placeholder markers
	$stmt->bind_param("sssssiii",$name,$specialization,$phone,$email,$password,$auth,$dnum,$hnum);

	//executing query
	$stmt->execute();
	
	//sending back details after registration
	$data = array('name' => $name,
	                'specialization' => $specialization,
	                'phone' => $phone,
	                'email' => $email,
	                'auth' => $auth,
	                'hospital' => $hospital,
	                'department' => $department);
	                
	 $json = json_encode($data);
	 echo $json;
	
	$deptStmt->close();
	$conn->close();
	$stmt->close();

?>
