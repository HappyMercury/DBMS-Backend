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
	
	public $defaultImage = "";//default profile photo
	
	public $hours = array(0,13,14,15,16,17,18,0,0,9,10,11,12);
	
	
	public function sort_slots($slots) {
        $hours24 = array(0,13,14,15,16,17,18,0,0,9,10,11,12);
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
	    return $slots;
    }
	
}