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

	//for department
	$dnumQuery = "SELECT dnum FROM department WHERE dname=?";

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
		$dnumQuery = "INSERT INTO department (dname) VALUES(?);";

		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("s",$department);
		$deptStmt->execute();

		$dnumQuery = "SELECT dnum FROM department WHERE dname=?";

		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("s",$department);
		$deptStmt->execute();
		$deptStmt->store_result();
		$deptStmt->bind_result($dno);
		$deptStmt->fetch();
		$dnum = $dno;
	}

	//for hospital
	$hnumQuery = "SELECT hnum FROM hospital WHERE hname=?";

	$deptStmt = $conn->prepare($dnumQuery);
	$deptStmt->bind_param("s",$hospital);
	$deptStmt->execute();
	$deptStmt->store_result();
	$deptStmt->bind_result($hno);
	$deptStmt->fetch();
	$hnum = $hno;

	//making prepared query
	$stmt = $conn->prepare("INSERT INTO doctor (name,phone,age,address,email,password,auth,dnum,hnum) VALUES(?,?,?,?,?,?,?,?,?)");

	//binding parameters for placeholder markers
	$stmt->bind_param("ssisssiii",$name,$phone,$age,$address,$email,$password,$auth,$dnum,$hnum);

	//executing query
	$stmt->execute();

	$conn->close();
	$stmt->close();

?>
