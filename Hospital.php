<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

class Hospital
{
	private $hospital;

	$query = "INSERT INTO hospital VALUES (?)"

	public function __construct($hospitalName)
	{
		$this->hospital = $hospitalName;

	}
}

$hospital = $_POST['hospital'];
$street = null;
$area = null;
$landmark = null;

$query = "SELECT * FROM hospital WHERE hname=?";

$stmt =  $conn->prepare($query);
$stmt->bind_param("s",$hospital);
$stmt->execute();

$stmt->store_result();

if($stmt->num_rows>0)
{
	//hospital already exists
	//can insert value of doctor without asking for address
	
	
}
else
{
	//ask for location which includes street,area,landmark
	$myObj->ask = "yes";
	$json = json_encode($myObj);
	echo $json;
}
?>
