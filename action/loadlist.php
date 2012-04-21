<?php
include_once("lib/head.php");
if(!isset($_GET['n']) || !isset($_GET['tp']) || !isset($_GET['m']) ){	
	return;
}
$idx=$_GET['n'];
$type=$_GET['tp'];
$mode=$_GET['m'];
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
mysql_select_db('eggsys',$conn);
$offset=$idx*$LIST_SIZE;
$sql="select * from posts where type='$type' and public=$mode order by id desc limit $offset, $LIST_SIZE";
$query=mysql_query($sql);
while($myrow=mysql_fetch_array($query)){
	$id=$myrow['id'];
	$title=$myrow['title'];
	$time=$myrow['create_time'];
	//print_r($myrow);
	$author=$myrow['author_email'];
	print $id."<>".$title."<>".$time."<>".$author;
	print "<|>";
}
$sql="select count(*) from posts where type='$type' and public=$mode";
$query=mysql_query($sql);
$myrow=mysql_fetch_array($query);
print $myrow[0];//total posts in this type
?>