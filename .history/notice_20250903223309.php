<?php
include 'db.php';

if($_SERVER['REQUEST_METHOD']=="POST"){
    $text = $_POST['text'];
    $pin = $_POST['pin'];
    if($pin === "4242"){
        $stmt = $conn->prepare("INSERT INTO notice(text) VALUES(?)");
        $stmt->bind_param("s",$text);
        $stmt->execute();
        echo "success";
    } else { echo "wrong pin"; }
}

if(isset($_GET['delete_id'])){
    $pin = $_GET['pin'];
    if($pin !== "4242"){ echo "Only CR can delete!"; exit;}
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM notice WHERE id=$id");
    echo "deleted";
}

if(isset($_GET['action']) && $_GET['action']=="fetch"){
    $res = $conn->query("SELECT * FROM notice ORDER BY created_at DESC");
    $data=[];
    while($row=$res->fetch_assoc()) $data[]=$row;
    echo json_encode($data);
}
?>
