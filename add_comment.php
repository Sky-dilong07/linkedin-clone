<?php
session_start();
require 'config/db.php';
if(!isset($_SESSION['user_id'])) exit("Not logged in");
$user_id=$_SESSION['user_id'];
$post_id=$_POST['post_id']??0;
$comment=trim($_POST['comment']??'');
if($comment==='') exit("Empty comment");
$stmt=$conn->prepare("INSERT INTO comments (user_id,post_id,comment) VALUES (?,?,?)");
$stmt->bind_param("iis",$user_id,$post_id,$comment);
$stmt->execute();
echo "ok";
?>
