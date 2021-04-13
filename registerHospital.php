<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

//variables for hospital
$hname = $_POST['hname'];
$street = $_POST['street'];
$area = $_POST['area'];

//response params
$error = false;
$message = "success";
$result = null;

$query = "INSERT INTO hospital (hname,street,area) VALUES (?,?,?)";

if($stmt =  $conn->prepare($query)) {

	$stmt->bind_param("sss",$hname,$street,$area);
	$stmt->execute();
	
	$query = "SELECT hid,hname,street,area FROM hospital WHERE hname=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("s",$hname);
	$stmt->execute();
	$stmt->stor_result();
	$stmt->bind_result($hid,$hname,$street,$area);
	
	$result = array(
	    "hid" => $hid,
	    "hname" => $hname,
	    "street" => $street,
	    "area" => $area
	    );
    $error = false;
    $message = "success";
    http_response_code(200);
	
}
else
{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

$data = array(
    "error" => $error,
    "message" => $message,
    "result" => $result
    );
    
    $json = json_encode($data);
    
    echo $json;
    
    $stmt->close();
    $conn->close();

?>