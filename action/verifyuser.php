<?php
session_start();
include_once("lib/head.php");
if( !isset($_POST['p']) || !isset($_POST['u']) ){
	return;
}
$email=$_POST['u'];
$enpasswd=$_POST['p'];//encrypted

$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("<code>connect_err</code>".mysql_error());
mysql_select_db('eggsys',$conn);
$sql="select * from uauth where email='$email' limit 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query))
{
	if(strcasecmp($myrow['passwd'], $enpasswd)==0){
		print '<code>verify_ok</code><data>'.$myrow['nickname'].'</data>';
		$_SESSION['email']=$email;
		$_SESSION['nickname']=$myrow['nickname'];
		if($myrow['group']==0){//0 admin; 1 user			
			$_SESSION['admin']=true;
		}
		return;
	}	
}
print '<code>verify_error</code>';
?>