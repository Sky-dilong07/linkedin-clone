<?php
require 'config/db.php';
$post_id=$_GET['post_id']??0;
$sql="SELECT comments.comment,comments.created_at,users.name 
      FROM comments JOIN users ON comments.user_id=users.id 
      WHERE post_id=$post_id ORDER BY comments.created_at ASC";
$res=$conn->query($sql);
if($res->num_rows>0){
  while($c=$res->fetch_assoc()){
    echo "<div class='comment'><strong>".htmlspecialchars($c['name'])."</strong>: "
        .htmlspecialchars($c['comment'])
        ."<br><small>".$c['created_at']."</small></div>";
  }
}
?>
