<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="jsMessage/codebase/themes/message_growl_dark.css">
<link href="res/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="res/jquery-1.6.2.min.js"></script>
<script language="javascript" src="res/extool.js?v=0.1"></script>
<script language="javascript" src="res/md5-min.js"></script>
<script language="javascript" src="res/hey.js"></script>
<script type="text/javascript" src='jsMessage/codebase/message.js'></script>
<title>黑蛋 | 查看用户</title>
<script language="javascript">
$(document).ready(function(){
	scrHgt=screen.availHeight;	
	$('#user_view').css('min-height',(scrHgt-216)+"px");
	initUser();
	loadUserInfo();	
});
function initUser(){	
	tmpemail="<?php 
		if(isset($_SESSION['email'])) {
			print $_SESSION['email'];
		}else{
			print "";
		}
	?>";
	tmpnickname="<?php
		if(isset($_SESSION['nickname'])) {
			print $_SESSION['nickname'];
		}else{
			print "";
		}
	?>";
	if(tmpemail==""){
		tmpemail=getCookie("email");
		if(tmpemail!=null){	
			tmppassword=getCookie("password");
			$.post("./action/verifyuser.php", {u: tmpemail, p: tmppassword}, function(resp){handleUAuth(resp);});
			//todo: show a loading circle nearby "login" span			
		}
	}else{
		email=tmpemail;
		nickname=tmpnickname;
		showTopbar(nickname);
	}
}
function loadUserInfo(){
	var tmpid="<?php 
		if(isset($_GET['uid'])) {
			print $_GET['uid'];
		}else{
			print "";
		}
	?>";
	if(tmpid==""){
		dhtmlx.alert({
			text:"错误的调用,将跳转到主页", 
			callback: function(){
	       		document.location="index.php";
	    	}
		});
	}else{
		$.get(phpDir+"loaduserinfo.php",{uid: tmpid}, function(resp){handleUserInfo(resp)});
	}
}

function handleUserInfo(resp){
	var code=getRespCode(resp);
	if(code=="user_id_error"){
		dhtmlx.alert({
			text:"被查询的用户信息错误,将跳转到主页", 
			callback: function(){
	       		document.location="index.php";
	    	}
		});
		
		return;
	}else if(code=="user_info_ok"){
		var data=getRespData(resp);
		var userinfo=my_substr(data, "user");
		var nick=my_substr(userinfo, "nick");
		var score=my_substr(userinfo, "score");
		var group=my_substr(userinfo, "group");
		$('#nick').html(nick);
		$('#point').html(score);
		$('#group').html(group);
		
		var posts=my_substr(data, "post");
		var pcs=posts.split("<|>");
		var list=document.getElementById('user_post_list');
		if(pcs.length==1){
			list.childNodes[0].innerHTML="暂无投递";
		}else{
			list.innerHTML="";
			for(i=0; i<pcs.length-1; i++){
				var post=pcs[i];
				var items=post.split("<>");
				var public=items[3];
				var a=document.createElement('a');
				if(public==0){
					a.innerHTML=unescape(decodeURI(items[1]))+"<a class='inreview'>(审核中)</a>";
					a.href="review.php?pid="+items[0];
				}else if(public==-1){
					a.innerHTML=unescape(decodeURI(items[1]))+"<a class='reject'>(未通过)</a>";
				}else{
					a.innerHTML=unescape(decodeURI(items[1]));
					a.href="index.php?pid="+items[0];
				}
				a.title=items[2];			
				a.target="_blank";
				a.className="list_item";
				list.appendChild(a);
			}
		}
		
		var favs=my_substr(data, "fav");
		pcs2=favs.split("<|>");		
		var list2=document.getElementById('user_fav_list');
		if(pcs2.length==1){		
			list2.childNodes[0].innerHTML="暂无收藏";
		}else{
			list2.innerHTML="";
			for(j=0; j<pcs2.length-1; j++){
				var fav=pcs2[j];
				var items2=fav.split("<>");
				var a2=document.createElement('a');
				a2.innerHTML=unescape(decodeURI(items2[1]));
				a2.href="index.php?pid="+items2[0];
				a2.title=items2[2];			
				a2.target="_blank";
				a2.className="list_item";
				list2.appendChild(a2);
			}
		}		
	}else{
		dhtmlx.alert("抱歉,出现未知错误!");
	}
}
</script>
<style>
#user_view{
	margin-top:40px;
	border:0px dashed #ccc;
	padding:0 60px;
	font-size:14px;
}
#user_view i{
	color:#999;
}
#avatar{
	width:64px;
	height:64px;
	float:left;
	background:#fff url(img/default_avatar_64.gif) no-repeat;
	border-radius:4px;
	box-shadow:0 0 4px #ccc;
}
#name{
	width:440px;
	height:64px;
	float:right;
}
#info{
	margin-top:20px;
	margin-bottom:10px;
	border-top: 6px solid #39c;
}
.record{
	width:240px;
	border:1px solid #eee;
	min-height:400px;
	background:#fbfbfb;
	padding:5px;
	word-break:break-all;
}
#post_rec{
	float:left;
}
#fav_rec{	
	float:right;
}
.record span:first-child{
	display:block;
	font-weight:bold;
	margin-bottom:8px;
}
.list_item{
	display:block;
	margin:5px 0;
	cursor:pointer;
}
.list_item:hover{
	background:#fdfdfd;
}
#main a, #main a:visited, #main a:active{
	text-decoration:underlined;
	color:#000;
}
a.inreview{
	color:green !important;
	font-style:italic;
}
a.reject{
	color:blue !important;
	font-style:italic;
}
</style>
</head>

<body>
<div id="topbar">
	<div id="btm_border"></div>
	<div id="left_panel"></div>
	<div id="right_panel">
		<div id="anony"><span onclick="login()">登录</span><span onclick="window.open('register.html');">注册</span></div>
		<div id="identity" style="display:none"><span>欢迎,&nbsp;<a id="nickname"></a>!</span><span onclick="logout()">退出</span></div>
	</div>
	<div id="logo" onclick="window.open('index.php')"></div>
</div>
<div id="main">
	<div id="view">
		<div id="user_view">
			<div id="avatar">
			</div>
			<div id="name">
				<div id="nick" style="margin-bottom:10px; font-size:18px; font-weight:bold;"><i>用户页面正在载入...</i></div>
				<div>身份:&nbsp;<a id="group"><i>加载中...</i></a></div>
				<div>
					<span>积分:&nbsp;<a id="point"><i>加载中...</i></a></span>&nbsp;<!--span>邮箱:&nbsp;<a id="mail"><i>加载中...</i></a></span-->
				</div>
			</div>
			<br style="clear:both" />
			<div id="info">				
			</div>
			<div id="post_rec" class="record">
				<span>投稿列表</span>
				<div id="user_post_list"><i>加载中...</i></div>
			</div>
			<div id="fav_rec" class="record">
				<span>收藏列表</span>
				<div id="user_fav_list"><i>加载中...</i></div>
			</div>
		</div>
	</div>
</div>
<div id="login_wrapper" style="display:none">
	<div id="login">
		<div id="close_login" onClick="$('#login_wrapper').hide();"></div>
		<input type="text" value="Email" id="email" onFocus="this.className='selected';this.style.border='none';if(this.value=='Email') this.value='';" onBlur="this.className='';if(this.value.trim()=='') this.value='Email';" />
		<input type="text" value="密码" id="password" onFocus="this.className='selected'; this.type='password';if(this.value=='密码') this.value='';" onBlur="this.className='';if(this.value=='') {this.value='密码';this.type='text';};"/>
		<button id="submit" onclick="doLogin()" style="top:80px;left:-37px;float:right;">登录</button>
	</div>
</div>
</body>
</html>
