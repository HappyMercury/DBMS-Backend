
<?php
class Database
{
    private $servername = 'localhost';
    private $username = 'id14974971_group60';
    private $password = 'Bm?{dIi5xt{StTux';

    public $conn;

    function connect()
    {
        $this->$conn = new mysqli($this->servername,$this->username,$this->password);
        $this->$conn->select_db('id14974971_dbms_project');
        // Check connection
        if (mysqli_connect_error()) 
        {
            echo("Connection failed: " . mysqli_connect_error());
        }
        echo "Connected successfully";
    }
  
}

?>
