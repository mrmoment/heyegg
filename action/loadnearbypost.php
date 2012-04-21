<?php
include_once("lib/head.php");
////TODO: update post view stat
if(!isset($_GET['id']) || !isset($_GET['tp']) || !isset($_GET['m']) || !isset($_GET['o']) ){
	return;
}
$id=$_GET['id'];
$type=$_GET['tp'];
$mode=$_GET['m'];
$orient=$_GET['o'];
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
mysql_select_db('eggsys',$conn);
if($orient<0){
	$sql="select * from posts where id<$id and type='$type' and public='$mode' order by id desc limit 1";
}else{
	$sql="select * from posts where id>$id and type='$type' and public='$mode' order by id limit 1";
}
$query=mysql_query($sql);
////TODO: this is same segment to loadpost.php, change it to function
if($myrow=mysql_fetch_array($query)){
	print $myrow['id'].'<>'.$myrow['title']."<>".$myrow['create_time']."<>";
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
	print $myrow['nickname'];
}else{
	print "未知";
}
?>