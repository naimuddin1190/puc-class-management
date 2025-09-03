<?php
include 'db.php';

if($_SERVER['REQUEST_METHOD']=="POST"){
    $day = $_POST['day'];
    $time = $_POST['time'];
    $course = $_POST['course'];
    $room = $_POST['room'];
    $pin = $_POST['pin'];

    if($pin === "4242"){
        $stmt = $conn->prepare("INSERT INTO routine(day,time,course,room) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss",$day,$time,$course,$room);
        $stmt->execute();
        echo "success";
    } else { echo "wrong pin"; }
}

// DELETE routine
if(isset($_GET['delete_id'])){
    $pin = $_GET['pin'];
    if($pin !== "4242"){ echo "Only CR can delete!"; exit;}
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM routine WHERE id=$id");
    echo "deleted";
}

// FETCH routine
if(isset($_GET['action']) && $_GET['action']=="fetch"){
    $res = $conn->query("SELECT * FROM routine ORDER BY FIELD(day,'Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'),time");
    $data = [];
    while($row = $res->fetch_assoc()) $data[]=$row;
    echo json_encode($data);
}
?>
