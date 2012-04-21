<?php
if( !isset($_POST['p']) || !isset($_POST['m']) || !isset($_POST['n']) || !isset($_POST['c']) ){
	return;
}
include_once("lib/head.php");
$email=$_POST['m'];
$nick=$_POST['n'];
$passwd=$_POST['p'];//encrypted
$code=$_POST['c'];

$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("<code>connect_err</code>".mysql_error());
mysql_select_db('eggsys',$conn);
//check invite code first
$sql="select * from reginvite where code='$code' and used=0 limit 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){
	$sql="select * from uauth where email='$email' limit 1";
	$query=mysql_query($sql);
	if($myrow=mysql_fetch_array($query))
	{
		print "<code>existing_email</code>";
		return;
	}else{
		$sql="select * from uauth where nickname='$nick' limit 1";
		$query=mysql_query($sql);
		if($myrow=mysql_fetch_array($query)){
			print "<code>existing_nick</code>";
			return;
		}else{
			//forbid sensitive nicknames
			$postFile=$dir."/".$timestr.$URL_SUFFIX;		
			$kwFile="../cfg/nickkeywords.txt";
			if(file_exists($kwFile)){
				$handler= fopen($kwFile, "r");
				$fileContent=fread($handler,fileSize($kwFile));
				$words=explode(",",$fileContent);
				for($i=0; $i<count($words); $i++){
					if(strpos($nick,$words[$i])!==false){
						print "<code>bad_nick</code>";
						return;
					}
				}
			}
			date_default_timezone_set("PRC");
			$createtime=date("YmdHis");
			$sql="insert into uauth (email, nickname, passwd, create_time) values ('$email','$nick','$passwd','$createtime')";
			$query=mysql_query($sql);
			$sql="update reginvite set used=1 where code='$code'";
			$query=mysql_query($sql);
		}
	}
}else{
	print "<code>invite_code_error</code>";
	return;
}
$sql="insert into userscores (user_email, score, items) values ('$email',200,',0001,')";
$query=mysql_query($sql);
print "<code>register_ok</code>";
?>