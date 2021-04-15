<?php
	include 'Database.php';
	
	//making connection to database
	$db = new Database();
	$hours24 = $db->hours;

	//representing connection variable to database
	$conn = $db->connect();

	//variables to get from application using POST
	$name = $_POST["name"];
	$phone = $_POST["phone"];
	$email = $_POST["email"];
	$slots = $_POST['slots'];
	$password = $_POST["password"];
	$password = md5($password);
	$auth = $_POST["auth"];
	$hospName = $_POST["hname"];
	$depName = $_POST["dep_name"];
	$image = $db->defaultImage;

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
	$hnumQuery = "SELECT hid,hname,street,area FROM hospital WHERE hname=?";

	$hospStmt = $conn->prepare($hnumQuery);
	$hospStmt->bind_param("s",$hospName);
	$hospStmt->execute();
	$hospStmt->store_result();
	$hospStmt->bind_result($hnum,$hname,$street,$area);
	$hospStmt->fetch();

	//making prepared query
	$stmt = $conn->prepare("INSERT INTO doctor (name,phone,email,password,auth,dnum,hnum,image) VALUES(?,?,?,?,?,?,?,?)");

	//binding parameters for placeholder markers
	$stmt->bind_param("ssssiiis",$name,$phone,$email,$password,$auth,$dnum,$hnum,$image);

	//variables for json response
	$error = null;
	$message = null;
	$result = null;

	//executing query for doctor insertion
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

			$slotStmt = $conn->prepare($docSlotsQuery);
			$slotStmt->bind_param("ii",$doc_id,$s);
			$slotStmt->execute();
		}

		$hospDeptQuery = "INSERT INTO hosp_dept(hnum,dnum) VALUES(?,?)";

		$hospDept = $conn->prepare($hospDeptQuery);
		$hospDept->bind_param("ii",$hnum,$dnum);
		$hospDept->execute();
		
		$slots = array();
        $slotQuery = "SELECT slot FROM doc_slots WHERE doc_id=?";
        $slotStmt = $conn->prepare($slotQuery);
        $slotStmt->bind_param("i",$doc_id);
        $slotStmt->execute();
        $slotStmt->store_result();
        $slotStmt->bind_result($timing);
        $i = 0;
        while($slotStmt->fetch())
        {
            $slots[$i++] = $timing;
        }
        
        //////////////////////////
    
	    $i=0;
	    foreach($slots as $s)
	    {
	        $dummy[$i++] = $hours24[$s];//converting times to 24 hour clock
	    }
	    sort($dummy);
	    $i=0;
	    foreach($dummy as $d)
	    {
	        if($d>12)
	        {
	            $slots[$i++] = $d-12;
	        }
	        else
	        {
	            $slots[$i++] = $d;
	        }
	    }
    
    ///////////////////////////////

		$error = false;
		$message = "success";

		$doctor = array(
					'doc_id' => $doc_id,
					'name' => $name,
					'slots' => $slots,
	                'phone' => $phone,
	                'email' => $email,
	                'auth' => $auth,
					'image' => $image,
					"hospital" => array("hid" => $hnum,"hname" => $hname,"street" => $street,"area" => $area),
                    "department" => array("dep_id" => $dnum,"dep_name" => $depName));

		$result['doctor'] = $doctor;
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