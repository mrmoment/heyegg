<?php
session_start();
include_once("lib/head.php");
if( !isset($_SESSION['admin']) || !isset($_POST['u']) || !isset($_POST['p']) || !isset($_POST['n']) ){
	return;
}

$uname=strtolower($_POST['u']);
$passwd=$_POST['p'];
if( strcmp($uname,"maxadmin")!=0 || strcmp($passwd,"$ADMIN_PASSWD")!=0 ){
	sleep(10);
	return;
}
print "<p>Start...</p>";
$num=$_POST['n'];
$CODE_LEN=12;
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
mysql_select_db('eggsys',$conn);
for($i=0; $i<$num; $i++){
	$code=randomkeys($CODE_LEN);
	$sql="select * from reginvite where code='$code' limit 1";
	$query=mysql_query($sql);
	if($myrow=mysql_fetch_array($query)){
		$i--;//duplicate code doesn't count
	}else{
		print "New Code:&nbsp;";
		$nsql="insert into reginvite (code, used) values ('$code', 0)" or die(mysql_error());
		$query=mysql_query($nsql);
		print $code."<br />";
	}
}
odbc_close($conn);
print "<p>Done...</p>";
?>
