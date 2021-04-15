<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();
$hours24 = $db->hours;

//json params
$error=  false;
$message = "success";
$result;

//post params
$pid = $_POST['pid'];

//comparing with half hour back
$time = time();//timestamp value of current time minus half an hour
$time = $time-1800;

//previous appointments
$query = "SELECT pid,doc_id,timestamp FROM appointment WHERE pid=? AND timestamp<? ORDER BY timestamp DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii",$pid,$time);
if($stmt->execute())//query successful
{
    
    $stmt->store_result();
    $stmt->bind_result($pid,$doc_id,$timestamp);
    
    //converting timestamp to date time
    date_default_timezone_set('Asia/Kolkata');
    $date = date("d-m-Y h:i a");

    $i = 0;
    $index = 0;
    while($stmt->fetch())
    {
        //getting doctor details for doctor id
        ////doctor details
        $docQuery = "SELECT doc_id,phone,name,email,auth,dnum,hnum,image FROM doctor WHERE doc_id=?";
        $docStmt = $conn->prepare($docQuery);
        $docStmt->bind_param("i",$doc_id);
        $docStmt->execute();
        $docStmt->store_result();
        $docStmt->bind_result($did,$phone,$name,$email,$auth,$dnum,$hnum,$image);
        $docStmt->fetch();

        /////hospital details
        $hnumQuery = "SELECT hid,hname,street,area FROM hospital WHERE hid=?";
        $hospStmt = $conn->prepare($hnumQuery);
        $hospStmt->bind_param("i",$hnum);
        $hospStmt->execute();
        $hospStmt->store_result();
        $hospStmt->bind_result($hid,$hname,$street,$area);
        $hospStmt->fetch();

        ////department details
        $dnumQuery = "SELECT dep_id,dep_name FROM department WHERE dep_id=?";
		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("i",$dnum);
		$deptStmt->execute();
		$deptStmt->store_result();
		$deptStmt->bind_result($dep_id,$depName);
		$deptStmt->fetch();

        /////slots
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
        //
        
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

        //formatting the doctor array
        $doctor = array(
            'doc_id' => $did,
            'name' => $name,
            'slots' => $slots,
            'phone' => $phone,
            'email' => $email,
            'auth' => $auth,
            'image' => $image,
            "hospital" => array("hid" => $hid,"hname" => $hname,"street" => $street,"area" => $area),
            "department" => array("dep_id" => $dep_id,"dep_name" => $depName));

        //getting timestamp for the particular appointment    
        $dateTime = date('d-m-Y h:i a',$timestamp);


        //making the previous array
        $previous[$index++] = array("doctor" => $doctor, "time" => $dateTime);
    }
    
}
else
{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

if($previous==null)
{
    $previous = array();
}

$result['previous'] = $previous;

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////

//upcoming appointments

$time = time();

$query = "SELECT pid,doc_id,timestamp FROM appointment WHERE pid=? AND timestamp>=? ORDER BY timestamp";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii",$pid,$time);
if($stmt->execute())//query successful
{
    $stmt->store_result();
    $stmt->bind_result($pid,$doc_id,$timestamp);

    //converting timestamp to date time
    date_default_timezone_set('Asia/Kolkata');
    $date = date("d-m-Y h:i a");

    $i = 0;
    $index = 0;
    while($stmt->fetch())
    {

        //getting doctor details for doctor id

        ////doctor details
        $docQuery = "SELECT doc_id,phone,name,email,auth,dnum,hnum,image FROM doctor WHERE doc_id=?";
        $docStmt = $conn->prepare($docQuery);
        $docStmt->bind_param("i",$doc_id);
        $docStmt->execute();
        $docStmt->store_result();
        $docStmt->bind_result($did,$phone,$name,$email,$auth,$dnum,$hnum,$image);
        $docStmt->fetch();
        
        /////hospital details
        $hnumQuery = "SELECT hid,hname,street,area FROM hospital WHERE hid=?";
        $hospStmt = $conn->prepare($hnumQuery);
        $hospStmt->bind_param("i",$hnum);
        $hospStmt->execute();
        $hospStmt->store_result();
        $hospStmt->bind_result($hnum,$hname,$street,$area);
        $hospStmt->fetch();

        ////department details
        $dnumQuery = "SELECT dep_id,dep_name FROM department WHERE dep_id=?";
		$deptStmt = $conn->prepare($dnumQuery);
		$deptStmt->bind_param("i",$dnum);
		$deptStmt->execute();
		$deptStmt->store_result();
		$deptStmt->bind_result($dnum,$depName);
		$deptStmt->fetch();

        /////slots
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
        //

        //formatting the doctor array
        $doctor = array(
            'doc_id' => $did,
            'name' => $name,
            'slots' => $slots,
            'phone' => $phone,
            'email' => $email,
            'auth' => $auth,
            'image' => $image,
            "hospital" => array("hid" => $hnum,"hname" => $hname,"street" => $street,"area" => $area),
            "department" => array("dep_id" => $dnum,"dep_name" => $depName));

        //getting timestamp for the particular appointment    
        $dateTime = date('d-m-Y h:i a',$timestamp);


        //making the previous array

        $upcoming[$index++] = array("doctor" => $doctor, "time" => $dateTime);
    }
}
else
{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

if($upcoming==null)
{
    $upcoming = array();
}

$result['upcoming'] = $upcoming;

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