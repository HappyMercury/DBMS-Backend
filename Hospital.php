<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$query = 'SELECT hname FROM hospital';

$stmt = $conn->prepare($query);

$stmt->execute();

$stmt->bind_result($hname);

$data = array();
$index = 0;

//stores the list of hospitals in array
while($stmt->fetch())
{
	$data[$index++] = $hname;
}

//returns list of hospitals
$json = json_encode($data);

echo $json;

?>

