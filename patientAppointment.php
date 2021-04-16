<?php

include 'Database.php';

$db = new Database();
$conn = $db->connect();

//json params
$error=  false;
$message = "success";
$result;

date_default_timezone_set('Asia/Kolkata');

//post params
$pid = $_POST['pid'];

//comparing with half hour back
$time = time();//timestamp value of current time minus half an hour
$time = $time-1800;

$previous = null;

//previous appointments

$query = "SELECT pid,doc_id,timestamp FROM appointment WHERE pid=? AND timestamp<? ORDER BY timestamp DESC";

$stmt = $conn->prepare($query);

$stmt->bind_param("ii",$pid,$time);

if($stmt->execute())//query successful
{
    $stmt->store_result();
    $stmt->bind_result($p,$doc_id,$timestamp);

    //converting timestamp to date time
    date_default_timezone_set('Asia/Kolkata');

    $date = date("d-m-Y h:i a");

    $i = 0;

    $index = 0;

    while($stmt->fetch())
    {
        //getting doctor details for doctor id

        ////doctor details
        $docQuery = "SELECT doc_id,phone,name,email,auth,dnum,hnum,image,hid,hname,street,area,dep_id,dep_name FROM doctor,hospital,department WHERE doc_id=? AND hnum=hid AND dep_id=dnum";
        $docStmt = $conn->prepare($docQuery);
        $docStmt->bind_param("i",$doc_id);
        $docStmt->execute();
        $docStmt->store_result();
        $docStmt->bind_result($did,$phone,$name,$email,$auth,$dnum,$hnum,$image,$hnum,$hname,$street,$area,$dnum,$depName);
        $docStmt->fetch();


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

        $slots = $db->sort_slots($slots);

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

$upcoming = null;

$time = time();

$upcomingQuery = "SELECT pid,doc_id,timestamp FROM appointment WHERE pid=? AND timestamp>=? ORDER BY timestamp";

$upcomingStmt = $conn->prepare($upcomingQuery);
$upcomingStmt->bind_param("ii",$pid,$time);

if($upcomingStmt->execute())//query successful
{
    $upcomingStmt->store_result();

    $upcomingStmt->bind_result($p,$doc_id,$timestamp);

    //converting timestamp to date time

    $date = date("d-m-Y h:i a");

    $i = 0;
    $index = 0;

    while($upcomingStmt->fetch())
    {
        //getting doctor details for doctor id

        ////doctor details
        $docQuery = "SELECT doc_id,phone,name,email,auth,dnum,hnum,image,hname,street,area,dep_name FROM doctor,hospital,department WHERE doc_id=? AND hnum=hid AND dep_id=dnum";
        $docStmt = $conn->prepare($docQuery);
        $docStmt->bind_param("i",$doc_id);
        $docStmt->execute();
        $docStmt->store_result();
        $docStmt->bind_result($did,$phone,$name,$email,$auth,$dnum,$hnum,$image,$hname,$street,$area,$depName);
        $docStmt->fetch();

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

        $slots = $db->sort_slots($slots);

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

        //making the upcoming array
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




//json response

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