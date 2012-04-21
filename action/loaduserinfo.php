<?php
if( !isset($_GET['uid']) ){
	return;
}
include_once("lib/head.php");
$userid=$_GET['uid'];
print $userid;
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("<code>connect_err</code>".mysql_error());
mysql_select_db('eggsys',$conn);
$sql="select * from uauth where id='$userid' limit 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){
	print "<data>";
	$email=$myrow['email'];//TODO: now no email info to display
	$group=$myrow['group'];
	if($group==0){
		$group='管理员';
	}else{
		$group='用户';
	}
	$nick=$myrow['nickname'];
	$score=0;//TODO: add score table in db...
	$sql="select * from userscores where user_email='$email' limit 1";
	$query=mysql_query($sql);
	if($scorerow=mysql_fetch_array($query)){
		$score=$scorerow['score'];
	}
	print "<user><nick>".$nick."</nick><score>".$score."</score><group>".$group."</group></user>";
	
	$sql="select * from posts where author_email='$email' order by id desc limit 30";//30 posts at most to display at once
	if( isset($_SESSION['email']) && strncmp($_SESSION['email'], $email)==0 ){
		$sql="select * from posts where author_email='$email' order by id desc";//fetch all for himself
	}
	
	$query=mysql_query($sql);
	print "<post>";
	while($newrow=mysql_fetch_array($query)){
		$id=$newrow['id'];
		$title=$newrow['title'];
		$time=$newrow['create_time'];
		$public=$newrow['public'];
		print $id."<>".$title."<>".$time."<>".$public;
		print "<|>";
	}
	print "</post>";
	
	$sql="select * from userlike where user_email='$email' limit 1";
	$query=mysql_query($sql);
	print "<fav>";
	if($newrow=mysql_fetch_array($query)){
		$str=$newrow['like_posts'];
		$pcs=explode(",",$str);
		for($idx=1; $idx<count($pcs)-1; $idx++){
			$id=$pcs[$idx];
			$sql="select * from posts where id=$id limit 1";
			$query=mysql_query($sql);
			if($xrow=mysql_fetch_array($query)){
				print $id."<>".$xrow['title']."<>".$xrow['create_time'];
			}
			print "<|>";
		}		
	}
	print "</fav>";
	
	print "</data>";
}else{
	print "<code>user_id_error</code>";
}
print "<code>user_info_ok</code>";
?>
