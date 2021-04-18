<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hours24 = $db->hours;

$doc_id = $_POST['doc_id'];

$currentTime = time();

$currentHour = (int) date('h');
$currentMinute = (int) date('i');
$currentSecond = (int) date('s');
$currentDay = (int) date('d');
$currentYear = (int) date('Y');
$currentMonth = (int) date('m');

$day0 = mktime(0, 0, 0, $currentMonth, $currentDay,$currentYear);//same day morning

$day1 = $day0 + 86400;//next day

date_default_timezone_set('Asia/Kolkata');

//previous
$previousQuery = "SELECT pid,doc_id,timestamp from appointment where doc_id=? and timestamp<? order by timestamp desc";

$dateStmt = $conn->prepare($previousQuery);
$dateStmt->bind_param("ii",$doc_id,$currentTime);

if($dateStmt->execute())
{
    
    $dateStmt->store_result();
    $dateStmt->bind_result($p0,$d0,$timestamp0);
    
    $patientStmt = $conn->prepare('SELECT pid,name,phone,age,address,email,auth,image FROM Patient WHERE pid=?');
    $patientStmt->bind_param("i",$p0);//for every pid received

    $i=0;
    while($dateStmt->fetch())
    {
        $patientStmt->execute();
        $patientStmt->store_result();
        $patientStmt->bind_result($pi0,$name,$phone,$age,$address,$email,$auth,$image);
        $patientStmt->fetch();
        
        if($timestamp<$currentTime)
            $previous[$i++] = array('time'=>date('d-m-Y h:i a',$timestamp0),
                                    'patient'=> array('pid' => $pi0, 
                                                    'name' => $name,
                                                    'phone' => $phone,
                                                    'age' => $age,
                                                    'address' => $address,
                                                    'email' => $email,
                                                    'auth' => $auth,
                                                    'image' => $image));
    }
    
    $result['previous'] = $previous;
    $error = false;
    $message = "success";
    http_response_code(200);
}
else
{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

//upcoming
$upcomingQuery = "SELECT pid,doc_id,timestamp from appointment where doc_id=? and timestamp>? order by timestamp";
$dateStmt = $conn->prepare($upcomingQuery);
    $dateStmt->bind_param("ii",$doc_id,$day1);
    
    if($dateStmt->execute())
    {
        $dateStmt->store_result();
        $dateStmt->bind_result($p1,$d1,$timestamp1);

        $patientStmt = $conn->prepare('SELECT pid,name,phone,age,address,email,auth,image FROM Patient WHERE pid=?');
        $patientStmt->bind_param("i",$p1);//for every pid received
        $i=0;
        while($dateStmt->fetch())
        {
            $patientStmt->execute();
            $patientStmt->store_result();
            $patientStmt->bind_result($pi1,$name,$phone,$age,$address,$email,$auth,$image);
            $patientStmt->fetch();
        
            $upcoming[$i++] = array('time'=>date('d-m-Y h:i a',$timestamp1),
                                        'patient'=> array('pid' => $pi1, 
                                                    'name' => $name,
                                                    'phone' => $phone,
                                                    'age' => $age,
                                                    'address' => $address,
                                                    'email' => $email,
                                                    'auth' => $auth,
                                                    'image' => $image));
        }
  
        http_response_code(200);
        $error = false;
    $message = "success";
        $result['upcoming'] = $upcoming;

    }
    else
    {
        $error = true;
        $message = "failure";
        $result = null;
        http_response_code(200);
    }


//today
$todayQuery = "SELECT pid,timestamp,slot from appointment where doc_id=? and timestamp between ? and ?";
$todayStmt = $conn->prepare($todayQuery);
$todayStmt->bind_param("iii",$doc_id,$currentTime,$day1);

if($todayStmt->execute())
{
    $todayStmt->store_result();
    $todayStmt->bind_result($p2,$timestamp2,$slotToday);
    $patientStmt = $conn->prepare('SELECT pid,name,phone,age,address,email,auth,image FROM Patient WHERE pid=?');
    $patientStmt->bind_param("i",$p2);//for every pid received

    $i=0;
    while($todayStmt->fetch())
    {
        $patientStmt->execute();
        $patientStmt->store_result();
        $patientStmt->bind_result($pi2,$name,$phone,$age,$address,$email,$auth,$image);
        $patientStmt->fetch();
        
        $makeTime = mktime($hours24[$slotToday],0,0,$currentMonth,$currentDay,$currentYear);
        if($makeTime>$currentTime)
        {
            $today[$i++] = array('time'=>date('d-m-Y h:i a',$timestamp2),
                                        'patient'=> array('pid' => $pi2, 
                                                        'name' => $name,
                                                        'phone' => $phone,
                                                        'age' => $age,
                                                        'address' => $address,
                                                        'email' => $email,
                                                        'auth' => $auth,
                                                        'image' => $image));
        }
    }
    
    if($today==null)
        $today = array();
    
    $result['todayAppointments'] = $today;
    $error = false;
    $message = "success";
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