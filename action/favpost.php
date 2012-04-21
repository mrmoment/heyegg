<?php 
session_start();
if( !isset($_SESSION['email']) || !isset($_GET['id']) || !isset($_GET['f']) ){
	return;
}
include_once("lib/head.php");
$email=$_SESSION['email'];
$id=$_GET['id'];
$like=$_GET['f'];
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("<code>connect_err</code>".mysql_error());
mysql_select_db('eggsys',$conn);
$sql="select count(*) from postlike where post_id=$id limit 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){
	if($myrow[0]==0){
		if($like==1){
			$sql="insert into postlike values ($id, 1, ',$email,')";
			$query=mysql_query($sql);
		}
	}else{
		$sql="select * from postlike where post_id=$id limit 1";
		$query=mysql_query($sql);
		$myrow=mysql_fetch_array($query);
		$users=$myrow['like_users'];
		if(strpos($users,','.$email.',')!==false){
			if($like==-1){//cancel favourate
				$users=str_replace(",".$email.",",",",$users);
				$liked=$myrow['liked']-1;
				$sql="update postlike set like_users='$users', liked=$liked where post_id=$id";
				$query=mysql_query($sql);				
			}
		}else{
			if($like==1){
				$users=$users.$email.",";
				$liked=$myrow['liked']+1;
				$sql="update postlike set like_users='$users', liked=$liked where post_id=$id";
				$query=mysql_query($sql);
			}			
		}
	}
}
//update user's like list
$sql="select count(*) from userlike where user_email='$email' limit 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){
	if($myrow[0]==0){//first time for this user to like a post
		if($like==1){
			$sql="insert into userlike values ('$email', 1, ',$id,')";
			$query=mysql_query($sql);
			print "<code>fav_ok</code>";
		}
	}else{
		$sql="select * from userlike where user_email='$email' limit 1";
		$query=mysql_query($sql);
		$myrow=mysql_fetch_array($query);
		$posts=$myrow['like_posts'];
		if(strpos($posts,','.$id.',')!==false){
			if($like==-1){//cancel favourate
				$posts=str_replace(",".$id.",", ",", $posts);
				$liked=$myrow['liked']-1;
				$sql="update userlike set like_posts='$posts', liked=$liked where user_email='$email'";
				$query=mysql_query($sql);	
				print "<code>unfav_ok</code>";			
			}
		}else{
			if($like==1){
				$posts=$posts.$id.",";
				$liked=$myrow['liked']+1;
				$sql="update userlike set like_posts='$posts', liked=$liked where user_email='$email'";
				$query=mysql_query($sql);
				print "<code>fav_ok</code>";
			}			
		}
	}
}
?>
