<?php
if( !isset($_GET['id']) ){
	return;
}
$COMMENTS_READ_AT_ONCE=10;
$id=$_GET['id'];
if( isset($_GET['all']) ){
	$readall=$_GET['all'];
}
include_once("lib/head.php");
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
	if(!file_exists($cmtDir)){
		print "<code>no_comment</code>";
		return;
	}
	$files=glob($cmtDir."/*".$CMT_SUFFIX);
	if(isset($readall) && $readall=1){
		$count=count($files);
	}else{
		$count=min(count($files),$COMMENTS_READ_AT_ONCE);		
	}
	print $count;
	if($count!=0){
		print "<data>";
	}
	for($idx=count($files)-1; $idx>=(count($files)-$count); $idx--){
		$handle=fopen($files[$idx],"r");
		$cmt=fread($handle, fileSize($files[$idx]));
		$start=strpos($cmt,"<u>")+3;
		$end=strpos($cmt,"</u>");
		$email=substr($cmt,$start,($end-$start));
		$tmpsql="select * from uauth where email='$email' limit 1";
		$tmpquery=mysql_query($tmpsql);
		if($tmprow=mysql_fetch_array($tmpquery)){
			$nick=$tmprow['nickname'];
		}else{
			$nick="未知用户";
		}
		$cmt=str_replace($email,$nick,$cmt);		
		print $cmt."<>";
	}
	if($count!=0){
		print "</data>";
	}
}else{
	print "<code>no_such_post</code>";
	return;
}
print "<code>load_comment_ok</code>";
///TODO: load comment num for users to reference
?>