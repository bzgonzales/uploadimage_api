<?php 
include_once ("config.php");
include ("api_request.php");

if(isset($_POST['name'])){

    $filename   = $_FILES['imagefile']['name'];
    $tempname   = $_FILES['imagefile']['tmp_name'];
    $folder     = "./image/".$filename;

    $description    = $_POST['description'];
    $type           = $_POST['type'];
    $url            = $_POST['url'];
    $title          = $_POST['title'];


    $sql = "INSERT INTO image (image,description,type,url,title) VALUES ($filename,$description,$type,$url,$title)";

    mysqli_query($mysqli,$sql);

    $result = '';

    if ((($_FILES["imagefile"]["type"] == "image/gif")
    || ($_FILES["imagefile"]["type"] == "image/jpeg")
    || ($_FILES["imagefile"]["type"] == "image/jpg")
    || ($_FILES["imagefile"]["type"] == "image/pjpeg")
    || ($_FILES["imagefile"]["type"] == "image/x-png")
    || ($_FILES["imagefile"]["type"] == "image/png"))
    && ($_FILES["imagefile"]["size"] < 100000) // Must be smaller than 10mb
    ) {

        if(move_uploaded_file($tempname,$folder)){
            


        }else{

        }

    }else{


    }

    header('Content-Type: application/json');
	echo json_encode($result);

}