<?php
class Database
{
	//Database parameters
	private $host = 'localhost';
	private $username = 'id14974971_group60';
	private $password = 'Bm?{dIi5xt{StTux';
	private $dbName = 'id14974971_dbms_project';

	private $conn;

	public function connect()
	{
		$this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbName);

		if($this->conn->connect_error)
		{
			die ("Connection error".$this->conn->connect_error);
		}

		return $this->conn;
	}

}