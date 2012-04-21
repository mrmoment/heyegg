<?php
session_start();
include_once("lib/head.php");
////TODO: update post view stat
if( !isset($_GET['id']) || !isset($_GET['tp']) || !isset($_GET['m']) ){
	return;
}

$idx=$_GET['id'];
$type=$_GET['tp'];
$mode=$_GET['m'];
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
mysql_select_db('eggsys',$conn);

$pid=0;
$sql="select * from posts where type='$type' and public='$mode' order by id desc limit $idx, 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){
	$pid=$myrow['id'];
	print $pid.'<>'.$myrow['title']."<>".$myrow['create_time']."<>";
	$dir=$URL_PREFIX."/".$myrow['author_email'];
	$timestr=str_replace(":","",$myrow['create_time']);
	$timestr=str_replace("-","",$timestr);
	$timestr=str_replace(" ","",$timestr);
	$postFile=$dir."/".$timestr.$URL_SUFFIX;
	if(file_exists($postFile)){
		$handler= fopen($postFile, "r");
		$fileContent=fread($handler,fileSize($postFile));
	}else{
		$fileContent="原文已丢失";///TODO: add a link for user to report this
	}
	print $fileContent."<>";
	$email=$myrow['author_email'];
}else{
	print "<code>no_post</code>";
	return;
}
//get current user nickname
$sql="select * from uauth where email='$email'";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){
	print $myrow['nickname']."<>".$myrow['id'];
}
//get fav status
if($mode==1){//1:view mode
	if( isset($_SESSION['email']) ){
		$myemail=$_SESSION['email'];
		$sql="select * from userlike where user_email='$myemail' limit 1";
		$query=mysql_query($sql);
		if($myrow=mysql_fetch_array($query)){
			$likes=$myrow['liked_posts'];
			if(strpos($likes,",".$pid.",")!==false){
				print "<>1";
			}else{
				print "<>0";
			}
		}
	}else{
		print "<>0";
	}
}else{
	print "<>0";
}
?>