<?php
include 'db.php';

if($_SERVER['REQUEST_METHOD']=="POST"){
    $role = $_POST['role'];
    $name = $_POST['name'];
    $detail = $_POST['detail'];
    $pin = $_POST['pin'];
    if($pin === "4242"){
        $stmt = $conn->prepare("INSERT INTO contacts(role,name,detail) VALUES(?,?,?)");
        $stmt->bind_param("sss",$role,$name,$detail);
        $stmt->execute();
        echo "success";
    } else { echo "wrong pin"; }
}

if(isset($_GET['delete_id'])){
    $pin = $_GET['pin'];
    if($pin !== "4242"){ echo "Only CR can delete!"; exit;}
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM contacts WHERE id=$id");
    echo "deleted";
}

if(isset($_GET['action']) && $_GET['action']=="fetch"){
    $res = $conn->query("SELECT * FROM contacts");
    $data=[];
    while($row=$res->fetch_assoc()) $data[]=$row;
    echo json_encode($data);
}
?>
