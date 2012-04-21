<?php
session_start();

if( !isset($_SESSION['email']) || !isset($_POST['tp']) || !isset($_POST['tl']) || !isset($_POST['ct']) ){
	return;
}

include_once('lib/head.php');
$email=$_SESSION['email'];
$type=$_POST['tp'];
$title=$_POST['tl'];
$content=$_POST['ct'];
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
mysql_select_db('eggsys',$conn);

date_default_timezone_set("PRC");
$createtime=date("YmdHis");
$title=urlencode($title);
$sql="insert into posts (type, title, author_email, create_time) values ('$type', '$title', '$email', '$createtime')";
$query=mysql_query($sql) or die ("<code>insert_error</code>".mysql_error());
$dir=$URL_PREFIX."/".$email;
$postFile=$dir."/".$createtime.$URL_SUFFIX;
mkdirs($dir);
$handle=fopen($postFile,"w");
fwrite($handle, $content);
/////TODO: write a single html file (for seo?)
fclose($handle);
$cmtDir=$dir."/".$createtime."-cmt";
mkdirs($cmtDir);
/*$commentFile=$cmtDir."/".$createtime."-cmt".$CMT_SUFFIX;
$handle=fopen($commentFile,"w");
fclose($handle);*/
addScore($email, 5);

print "<code>post_ok</code>";
?>