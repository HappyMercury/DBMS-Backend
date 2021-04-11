<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

//variables for hospital
$hospital = $_POST['hospital'];
$street = $_POST['street'];
$area = $_POST['area'];
$landmark = $_POST['landmark'];

$query = "INSERT INTO hospital (hname,street,area,landmark) VALUES (?,?,?,?)";

if($stmt =  $conn->prepare($query)) {

	$stmt->bind_param("ssss",$hospital,$street,$area,$landmark);
	$stmt->execute();

}

?>
