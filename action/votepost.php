<?php
session_start();
if( !isset($_GET['id']) || !isset($_GET['s']) || !isset($_SESSION['email']) ){
	return;
}

include_once("lib/head.php");
$MIN_VOTE_USERS=5;
$MIN_VOTE_SCORE=3;
$MIN_REJECT_SCORE=2;
$id=$_GET['id'];
$score=$_GET['s'];
$email=$_SESSION['email'];
$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
mysql_select_db('eggsys',$conn);

$sql="select count(*) from postvote where post_id=$id limit 1";
$query=mysql_query($sql);
if($myrow=mysql_fetch_array($query)){//TODO: this count process can be rewritten, see loadpostbyid.php; also change favpost.php
	if($myrow[0]==0){
		//add a new record
		$sql="insert into postvote (post_id, voted, score, vote_users) values ($id, 1, $score, ',$email,')";
		$query=mysql_query($sql);
		print '<code>vote_ok</code>';
	}else{
		$sql="select * from postvote where post_id=$id limit 1";
		$query=mysql_query($sql);
		$myrow=mysql_fetch_array($query);
		$users=$myrow['vote_users'];
		if(strpos($users,','.$email.',')!==false){
			print "<code>already_voted</code>";
			return;
		}else{
			$curScore=$myrow['score'];
			$curPeople=$myrow['voted'];
			$newScore=($curScore*$curPeople+$score)/($curPeople+1);
			$newUsers=$users.$email.',';
			$sql="update postvote set voted=($curPeople+1), score=$newScore, vote_users='$newUsers' where post_id=$id";
			$query=mysql_query($sql);
			print '<code>vote_ok</code>';
		}		
		//check if publicize this post		
		if( ($curPeople+1)>=$MIN_VOTE_USERS && $newScore>=$MIN_VOTE_SCORE ){
			$sql="update posts set public=1 where id=$id";	
			$query=mysql_query($sql);		
		}else if( ($curPeople+1)>=$MIN_VOTE_USERS && $newScore<=$MIN_REFJECT_SCORE ){//check if reject this post
			$sql="update posts set public=-1 where id=$id";	
			$query=mysql_query($sql);
		}
	}
	//check if publicize this post by admin
	if( isset($_SESSION['admin']) && $_SESSION['admin']==true ){
		if( $score>=$MIN_VOTE_SCORE ){
			$sql="update posts set public=1 where id=$id";
			$query=mysql_query($sql);
		}else if($score<=$MIN_REJECT_SCORE){
			$sql="update posts set public=-1 where id=$id";
			$query=mysql_query($sql);
		}
	}
}
?>
