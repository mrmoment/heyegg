<?php
session_start();
if( !isset($_POST['id']) || !isset($_POST['c']) || !isset($_SESSION['email']) ){
	return;
}

include_once("lib/head.php");
$id=$_POST['id'];
$comment=$_POST['c'];
$email=$_SESSION['email'];
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
mysql_select_db('eggsys',$conn);

$sql="select * from posts where id=$id limit 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){
	$dir=$URL_PREFIX."/".$myrow['author_email'];
	$timestr=str_replace(":","",$myrow['create_time']);
	$timestr=str_replace("-","",$timestr);
	$timestr=str_replace(" ","",$timestr);
	$cmtDir=$dir."/".$timestr."-cmt";
	if(!file_exists($cmtDir)){//comment dir is deleted (in development case)
		mkdirs($cmtDir);
	}
	$files=glob($cmtDir."/*".$CMT_SUFFIX);
	//$cmtIdx=count($files)+1;
	$createtime=date("YmdHis");
	$cmtFile=$cmtDir."/".$createtime.$CMT_SUFFIX;
	$handle=fopen($cmtFile,"w");
	date_default_timezone_set("PRC");
	$createtime=date("Y-m-d H:i:s");
	$content="<u>".$email."</u><t>".$createtime."</t><c>".$comment."</c>";
	fwrite($handle, $content);
	fclose($handle);
	
	$newComments=$myrow['comments']+1;
	$sql="update posts set comments=$newComments where id=$id";
	$query=mysql_query($sql);
}else{
	print "<code>no_such_post</code>";
	return;
}
addScore($email, 2);
print "<code>comment_ok</code>";
?>