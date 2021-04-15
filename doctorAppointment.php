<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hours24 = $db->hours;

//post params
$doc_id = $_POST['doc_id'];

//json params
$error = false;
$message = "success";
$result = array();

date_default_timezone_set('Asia/Kolkata');

$hour24 = 86400;

$currentHour = (int) date('h');
$currentMinute = (int) date('i');
$currentSecond = (int) date('s');
$currentDay = (int) date('d');
$currentYear = (int) date('Y');
$currentMonth = (int) date('m');

$currentTime = time();

$day0 = mktime(0, 0, 0, $currentMonth, $currentDay,$currentYear);

$day1 = $day0 + $hour24;
$day2 = $day1 + $hour24;
$day3 = $day2 + $hour24;
$day4 = $day3 + $hour24;

$result['doc_id'] = $doc_id;

$docSlotsQuery = "SELECT slot FROM doc_slots WHERE doc_id=?";

//getting all slots
$docSlotsStmt = $conn->prepare($docSlotsQuery);
$docSlotsStmt->bind_param("i",$doc_id);

if($docSlotsStmt->execute())
{
    $docSlotsStmt->store_result();
    $docSlotsStmt->bind_result($docSlot);
    
    $i=0;

    while($docSlotsStmt->fetch())
    {
        $docSlots[$i++] = $docSlot;//storing in 24 hour format
    }
}

///////////////////////////////////////
//today
$todayQuery = "SELECT slot FROM doc_slots WHERE doc_id=? AND slot NOT IN(SELECT slot FROM appointment WHERE doc_id=? AND timestamp BETWEEN ? AND ?)";

$todayStmt = $conn->prepare($todayQuery);
$todayStmt->bind_param("iiii",$doc_id,$doc_id,$currentTime,$day1);

if($todayStmt->execute())
{
    $todayStmt->store_result();
    $todayStmt->bind_result($slotToday);

    $i=0;
    while($todayStmt->fetch())
    {
        $makeTime = mkTime($hours24[$slotToday],0,0,$currentMonth,$currentday,$currentYear);
        if($makeTime>$currentTime)
        $slotsToday[$i++] = $slotToday;
    }
    
    /////////////////////////////////
	    $i=0;
	    foreach($slotsToday as $s)
	    {
	        $dummy[$i++] = $hours24[$s];//converting times to 24 hour clock
	    }
	    sort($dummy);
	    $i=0;
	    foreach($dummy as $d)
	    {
	        if($d>12)
	        {
	            $slotsToday[$i++] = $d-12;
	        }
	        else
	        {
	            $slotsToday[$i++] = $d;
	        }
	    }
    
    ///////////////////////////////
    
    if($slotsToday==null)
    $slotsToday = array();
    
    $result['today'] = $slotsToday;
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}


//////////////////////////////////
//date1
$date1Query = "SELECT slot FROM doc_slots WHERE doc_id=? AND slot NOT IN(SELECT slot FROM appointment WHERE doc_id=? AND timestamp BETWEEN ? AND ?)";
$date1Stmt = $conn->prepare($date1Query);
$date1Stmt->bind_param("iiii",$doc_id,$doc_id,$day1,$day2);

if($date1Stmt->execute())
{
    $date1Stmt->store_result();
    $date1Stmt->bind_result($slotDate1);

    $i=0;
    while($date1Stmt->fetch())
    {
        $slotsDate1[$i++] = $slotDate1;
    }
    //////////////////////////
    
	    $i=0;
	    foreach($slotsDate1 as $s)
	    {
	        $dummy[$i++] = $hours24[$s];//converting times to 24 hour clock
	    }
	    sort($dummy);
	    $i=0;
	    foreach($dummy as $d)
	    {
	        if($d>12)
	        {
	            $slotsDate1[$i++] = $d-12;
	        }
	        else
	        {
	            $slotsDate1[$i++] = $d;
	        }
	    }
    
    ///////////////////////////////
    
    $result['date1'] = $slotsDate1;
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}


////////////////////////////////////////
//date2
$date2Query = "SELECT slot FROM doc_slots WHERE doc_id=? AND slot NOT IN(SELECT slot FROM appointment WHERE doc_id=? AND timestamp BETWEEN ? AND ?)";
$date2Stmt = $conn->prepare($date2Query);
$date2Stmt->bind_param("iiii",$doc_id,$doc_id,$day2,$day3);

if($date2Stmt->execute())
{
    $date2Stmt->store_result();
    $date2Stmt->bind_result($slotDate2);

    $i=0;
    while($date2Stmt->fetch())
    {
        $slotsDate2[$i++] = $slotDate2;
    }

    
    //////////////////////////
    
	    $i=0;
	    foreach($slotsDate2 as $s)
	    {
	        $dummy[$i++] = $hours24[$s];//converting times to 24 hour clock
	    }
	    sort($dummy);
	    $i=0;
	    foreach($dummy as $d)
	    {
	        if($d>12)
	        {
	            $slotsDate2[$i++] = $d-12;
	        }
	        else
	        {
	            $slotsDate2[$i++] = $d;
	        }
	    }
    
    ///////////////////////////////
    
    $result['date2'] = $slotsDate2;
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}


////////////////////////////
//date3
$date3Query = "SELECT slot FROM doc_slots WHERE doc_id=? AND slot NOT IN(SELECT slot FROM appointment WHERE doc_id=? AND timestamp BETWEEN ? AND ?)";
$date3Stmt = $conn->prepare($date3Query);
$date3Stmt->bind_param("iiii",$doc_id,$doc_id,$day3,$day4);

if($date3Stmt->execute())
{
    $date3Stmt->store_result();
    $date3Stmt->bind_result($slotDate3);

    $i=0;
    while($date3Stmt->fetch())
    {
        $slotsDate3[$i++] = $slotDate3;
    }

    //////////////////////////
    
	    $i=0;
	    foreach($slotsDate3 as $s)
	    {
	        $dummy[$i++] = $hours24[$s];//converting times to 24 hour clock
	    }
	    sort($dummy);
	    $i=0;
	    foreach($dummy as $d)
	    {
	        if($d>12)
	        {
	            $slotsDate3[$i++] = $d-12;
	        }
	        else
	        {
	            $slotsDate3[$i++] = $d;
	        }
	    }
    
    ///////////////////////////////
    
    $result['date3'] = $slotsDate3;
    http_response_code(200);
}
else{
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

$conn->close();

?>