<?php

include 'Database.php';

$db = new Database();
$conn = $db->connect();
$hours24 = $db->hours;

//varibales from user
$email = $_POST["email"];
$password = md5($_POST["password"]);

//result paramters
$error = false;
$message = "success";
$result;

//first checking if present in patient
$query = "SELECT pid,phone,name,age,address,email,auth,image FROM Patient WHERE email=? AND password=?";

//executing the patient query
$stmt = $conn->prepare($query);
$stmt->bind_param("ss",$email,$password);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($pid,$phone,$name,$age,$address,$email,$auth,$image);

if($stmt->num_rows>0)//if exists in patient
{
    $stmt->fetch();

    //sending data to user in json
    $error = false;
    $message = "success";
	$patient = array(
		'pid' => $pid,
		'phone' => $phone,
		'name' => $name,
		'age' => $age,
		'address' => $address,
		'email' => $email,
		'auth' => $auth,
        "image" => $image
	);
    $result = array("profession" => "patient","patient"=>$patient);
}
else
{
    $query = "SELECT doc_id,phone,name,email,auth,dnum,hnum,image FROM doctor WHERE email=? AND password=?";

    //executing doctor query
    $docStmt = $conn->prepare($query);
    $docStmt->bind_param("ss",$email,$password);
    $docStmt->execute();
    $docStmt->store_result();
    $docStmt->bind_result($doc_id,$phone,$name,$email,$auth,$dnum,$hnum,$image);

    if($docStmt->num_rows>0)//if exists in doctor
    {
        $docStmt->fetch();

        //getting hospital and department name
        $query = "SELECT hname,street,area FROM hospital WHERE hid=?";

        $hospStmt = $conn->prepare($query);
        $hospStmt->bind_param("i",$hnum);
        $hospStmt->execute();
        $hospStmt->store_result();
        $hospStmt->bind_result($hospital,$street,$area);
        $hospStmt->fetch();

        $query = "SELECT dep_name FROM department WHERE dep_id=?";
    
        $depStmt = $conn->prepare($query);
        $depStmt->bind_param("i",$dnum);
        
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
        
        if($depStmt->execute())
        {
            $depStmt->store_result();
            $depStmt->bind_result($department);
            $depStmt->fetch();

            //sending data to user in json
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
					"hospital" => array("hid" => $hnum,"hname" => $hospital,"street" => $street,"area" => $area),
                    "department" => array("dep_id" => $dnum,"dep_name" => $department));

                $result['profession'] = "doctor";
                $result['doctor'] = $doctor;
        }
        http_response_code(200);
    }
    else
    {
        $error = true;
        $message = "failure";
        $result = null;
        http_response_code(200);
    }
}

$data = array(
    "error" => $error,
    "message" => $message,
    "result" => $result
    );

$json = json_encode($data);

echo $json;

// $stmt->close();
$conn->close();
?>