<?php
include 'db.php';

if($_SERVER['REQUEST_METHOD']=="POST"){
    $pin = $_POST['pin'];
    if($pin !== "4242"){ echo "wrong pin"; exit; }

    // Add new notice
    if(isset($_POST['text']) && !isset($_POST['edit_id'])){
        $text = $_POST['text'];
        $stmt = $conn->prepare("INSERT INTO notice(text) VALUES(?)");
        $stmt->bind_param("s",$text);
        $stmt->execute();
        echo "success";
    }

    // Edit existing notice
    if(isset($_POST['edit_id'])){
        $id = $_POST['edit_id'];
        $text = $_POST['text'];
        $stmt = $conn->prepare("UPDATE notice SET text=? WHERE id=?");
        $stmt->bind_param("si",$text,$id);
        $stmt->execute();
        echo "updated";
    }
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
