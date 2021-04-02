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