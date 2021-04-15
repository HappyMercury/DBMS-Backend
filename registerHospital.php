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
$result;

$query = "INSERT INTO hospital (hname,street,area) VALUES (?,?,?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss",$hname,$street,$area);

if($stmt->execute()) {

	$hospQuery = "SELECT hid,hname,street,area FROM hospital WHERE hname=?";
	$hospStmt = $conn->prepare($hospQuery);
	$hospStmt->bind_param("s",$hname);

	$hospStmt->execute();
	$hospStmt->store_result();
	$hospStmt->bind_result($hid,$hname,$street,$area);
    $hospStmt->fetch();
	
	$hospital = array(
	    "hid" => $hid,
	    "hname" => $hname,
	    "street" => $street,
	    "area" => $area
	    );
	    
    $result["hospital"] = $hospital;
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