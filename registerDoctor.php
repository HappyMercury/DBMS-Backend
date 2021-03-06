<?php
	include 'Database.php';

	//making connection to database
	$db = new Database();
	//representing connection variable to database
	$conn = $db->connect();

	//variables to get from application using POST
	$name = $_POST["name"];
	$phone = $_POST["phone"];
	$email = $_POST["email"];
	$slots = $_POST["slots"];
	$password = $_POST["password"];
	$password = md5($password);
	$auth = $_POST["auth"];
	$hospName = $_POST["hname"];
	$depName = $_POST["dep_name"];

	//for department
	$dnumQuery = "SELECT dep_id FROM department WHERE dep_name=?";

	$deptStmt = $conn->prepare($dnumQuery);
	$deptStmt->bind_param("s",$depName);
	$deptStmt->execute();
	$deptStmt->store_result();


	if($deptStmt->num_rows>0)//if department already exists
	{
		$deptStmt->bind_result($dno);
		$deptStmt->fetch();
		$dnum = $dno;
	}
	else//if department does not exist already
	{
		$dnumQuery = "INSERT INTO department (dep_name) VALUES(?);";

		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("s",$depName);
		$deptStmt->execute();

		$dnumQuery = "SELECT dep_id FROM department WHERE dep_name=?";

		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("s",$depName);
		$deptStmt->execute();
		$deptStmt->store_result();
		$deptStmt->bind_result($dno);
		$deptStmt->fetch();
		$dnum = $dno;
	}

	//for hospital
	$hnumQuery = "SELECT hid FROM hospital WHERE hname=?";

	$deptStmt = $conn->prepare($hnumQuery);
	$deptStmt->bind_param("s",$hospName);
	$deptStmt->execute();
	$deptStmt->store_result();
	$deptStmt->bind_result($hno);
	$deptStmt->fetch();
	$hnum = $hno;

	//making prepared query
	$stmt = $conn->prepare("INSERT INTO doctor (name,phone,email,password,auth,dnum,hnum) VALUES(?,?,?,?,?,?,?,?)");

	//binding parameters for placeholder markers
	$stmt->bind_param("ssssiii",$name,$phone,$email,$password,$auth,$dnum,$hnum);

	//variables for json response
	$error = null;
	$message = null;
	$result = null;

	//executing query
	if($stmt->execute())
	{
		//getting doc_id for inserted doctor
		$docStmt = $conn->prepare("SELECT doc_id from doctor where email=?");
		$docStmt->bind_param("s",$email);
		$docStmt->execute();
		$docStmt->store_result();
		$docStmt->bind_result($doc_id);
		$docStmt->fetch();

		//inserting the slots into doc_slots table
		foreach($slots as $s)
		{
			$docSlotsQuery = "INSERT INTO doc_slots(doc_id,slot) VALUES(?,?)";
			$hospDept = $conn->prepare($docSlotsQuery);
			$docStmt->bind_param("ii",$doc_id,$s);
			$docStmt->execute();
		}

		$hospDeptQuery = "INSERT INTO hosp_dept(hnum,dnum) VALUES(?,?)";
		$hospDept = $conn->prepare($hospDeptQuery);
		$docStmt->bind_param("ii",$hnum,$dnum);
		$docStmt->execute();
		
		$error = false;

		$message = "success";

		$result = array(
			'doc_id' => $doc_id,
			'name' => $name,
			'slots' => $slots,
	                'phone' => $phone,
	                'email' => $email,
	                'auth' => $auth,
			'hid' => $hnum,
			'hname' => $hospName,
			'dep_id' => $dnum,
	                'dep_name' => $depName);

		http_response_code(200);
	}

	else{
		$error = true;
		$message = "failure";
		$result = null;
		http_response_code(200);
	}
	
	//sending back details after registration
	$data = array(
		'error' => $error,
		'message' => $message,
		'result' => $result
	);
	                
	 $json = json_encode($data);
	 echo $json;
	
	$deptStmt->close();
	$conn->close();
	$stmt->close();

?>
