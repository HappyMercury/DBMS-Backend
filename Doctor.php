<?php
	include 'Database.php';
	
	//making connection to database
	$db = new Database();
	//representing connection variable to database
	$conn = $db->connect();

	//variables to get from application using POST
	$doc_id = $_POST["doc_id"];

	//making prepared query
	$stmt = $conn->prepare("SELECT name,phone,email,password,auth,dnum,hnum,image,hname,street,area,dep_name FROM doctor,hospital,department WHERE doc_id=? AND hnum=hid AND dep_id=dnum");

	//binding parameters for placeholder markers
	$stmt->bind_param("i",$doc_id);

	//variables for json response
	$error = null;
	$message = null;
	$result = null;

    $doctor=null;

	//executing query for doctor insertion
	if($stmt->execute())
	{
        $stmt->store_result();
        $stmt->bind_result($name,$phone,$email,$password,$auth,$dnum,$hnum,$image,$hname,$street,$area,$dep_name);
        $stmt->fetch();

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
        
        $slots = $db->sort_slots($slots);

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
                    "department" => array("dep_id" => $dnum,"dep_name" => $dep_name));

        if($doctor==null)
        {
            $doctor = (object) [];
        }
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

	$conn->close();
	$stmt->close();
?>