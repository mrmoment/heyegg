<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>黑蛋 | 新用户注册邀请码</title>
<link href="res/style.css" rel="stylesheet" type="text/css" />
<style>
.codeline{
	padding:6px 0;
	font-size:1.1em;
	font-weight:bold;
	margin:auto 10px;
}
.codeline:hover{
	background: #fff;
}
#view h4{
	margin:5px;
}
</style>
</head>

<body>	
<div id="topbar">
	<div id="btm_border"></div>
	<div id="logo" onclick="window.open('index.php');"></div>
</div>
<div id="main">
	<div id="view" style="background:#eaeaea; font-size:14px;">
		<div style="font-family:'Microsoft YaHei'; background:rgba(255,255,255,0.8); padding:10px; margin:10px;">
		<h4>为什么要邀请码</h4>黑蛋"&nbsp;目前处于测试阶段，用户注册需要通过邀请码进行。<br />
		<h4>如何获取邀请码</h4><li>每天我们会定时更新一批邀请码（见下方）。</li><li>活跃用户还将不定期得到发送邀请码的机会，可以分享给您的好友。</li>
		<h4>需要注意什么</h4><li>每天定期更新的邀请码数量有限，如果列表为空请您稍后再试。</li><li>对于已注册用户发送给您的邀请码，请注意保管，不要随意透露给他人。</li><li>当然，您应当及时使用该邀请码进行注册。</li>
		</div>
		<div style="font-family:'Courier New', Courier, monospace; min-height:400px;" align="center" id="codelist">
			<div onclick="document.location=document.location;" style="cursor:pointer; background:#00CCFF; border:1px solid #333; width:60px;">刷新</div>
			<?php
			sleep(1);
			include_once("action/lib/head.php");
			$conn= mysql_connect("localhost", "root", "$mysqlpw") or die("Could not connect: " . mysql_error());
			mysql_select_db('eggsys',$conn);
			$sql="select * from reginvite where used=0 limit 30";
			$query=mysql_query($sql);
			while($myrow=mysql_fetch_array($query)){
				$code=$myrow['code'];
				print "<div class='codeline'>".$code."</div>";
			}
			?>
		</div>
		<script language="javascript">
			if(document.getElementById('codelist').childNodes.length<4){
				var d=document.createElement('div');
				d.className="codeline";
				d.innerHTML="暂无更多邀请码";
				document.getElementById('codelist').appendChild(d);
			}
		</script>
	</div>
</div>
</body>
</html>
