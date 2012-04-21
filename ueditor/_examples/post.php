<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>黑蛋 | 投递新稿件</title>
<link rel="stylesheet" type="text/css" href="../../jsMessage/codebase/themes/message_growl_dark.css">
<link href="../../res/style.css" rel="stylesheet" type="text/css" />
<link href="../../res/effect.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../themes/default/ueditor.css"/>
<script language="javascript" src="../../res/jquery-1.6.2.min.js"></script>
<script language="javascript" src="../../res/extool.js"></script>
<script language="javascript" src="../../res/md5-min.js"></script>
<script language="javascript" src="../../res/hey.js?v=0.2"></script>
<script type="text/javascript" src='../../jsMessage/codebase/message.js?v=0.2'></script>
<script type="text/javascript" charset="utf-8" src="../editor_config.js"></script>    
<script type="text/javascript" charset="utf-8" src="../editor_all_min.js"></script>
<style>
#view{
	font-size:14px;
	font-family:'Microsoft YaHei';
	padding:40px 10px;
}
#ptitle{
	width:100%;
}
label{
	display:block;
	margin:25px 0px 2px 0;
}
</style>
<script language="javascript">
$(document).ready(function(){
	initUser();
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
			$.post("../../action/verifyuser.php", {u: tmpemail, p: tmppassword}, function(resp){handleUAuth(resp);});
			//todo: show a loading circle nearby "login" span			
		}
	}else{
		email=tmpemail;
		nickname=tmpnickname;
		showTopbar(nickname);
	}
}
function doPost(){
	var type=$('#type')[0].value;
	var title=$('#ptitle')[0].value;
	var content=editor_a.getContent();	
	if(email){
		if(title==""){
			dhtmlx.alert("请输入文章标题!");
		}else if(!editor_a.hasContents()){
			dhtmlx.alert("请输入文章内容!");
		}else{
			editor_a.sync();
			$.post("../../action/addpost.php", { tp: type, tl: title, ct: content}, function(resp){handlePost(resp);});
		}
	}else{		
		dhtmlx.alert("请先登录!");
	}
}
function handlePost(resp){
	var code=getRespCode(resp);
	if(code=="post_ok"){
		dhtmlx.message("投递成功!");
		$('#ptitle')[0].value="";
	}else if(code=="identity_error"){
		dhtmlx.alert("登录信息错误,请重新登录!");
	}else{
		dhtmlx.alert("抱歉,出现未知错误!");
	}
}
</script>
</head>

<body>
<div id="topbar">
	<div id="btm_border"></div>
	<div id="left_panel"></div>
	<div id="right_panel">
		<div id="anony"><span onclick="login()">登录</span><span onclick="window.open('register.html');">注册</span></div>
		<div id="identity" style="display:none"><span>欢迎,&nbsp;<a id="nickname"></a>!</span><span onclick="logout('../../')">退出</span></div>
	</div>
	<div id="logo" onclick="window.open('../../index.php');"></div>
</div>
<div id="main">
	<div id="view">
		<div>
			<label>文章标题</label>
			<input type="text" id="ptitle" maxlength="50" />
		</div>
		<div>
			<label>类别:</label>
			<select id="type">
				<option value="tech">科技</option>
				<option value="news">社会</option>
				<option value="fun">奇趣</option>
				<option value="ent">娱乐</option>
			</select>
		</div>
		<div>
			<label>正文内容</label>
			<script type="text/plain" id="myEditor" class="myEditor"></script>
			<script type="text/javascript">
				var editor_a = new baidu.editor.ui.Editor();
				editor_a.render('myEditor');
				//remove unused tools
				var unused=Array(3,6,7,8,15,16,18,19,20,21,50,57,88,89,90,122,133,138,143,144,149,166,167,168,169,170,171,172,173,174,175,176,177,184,186);
				//alert(unused+";"+unused.length);
				var i=0;
				for(; i<unused.length; i++){
					hideTool('edui'+unused[i]);
				}
				
				function hideTool(tid){
					var tobj=document.getElementById(tid);
					if(tobj){
						/*var tparent=tobj.parentNode;
						tparent.removeChild(tobj);
						*///we don't remove it as warnings in console, so just hide it
						tobj.style.display="none !important";    		
					}
				}
			</script>
		</div>
		<div>
			<button id="submit" style="top:10px" onclick="doPost()" style="top:90px; right:-270px;">投递</button>
		</div>
	</div>
</div>
<div id="login_wrapper" style="display:none">
	<div id="login">
		<div id="close_login" onClick="$('#login_wrapper').hide();"></div>
		<input type="text" value="Email" id="email" onFocus="this.className='selected';this.style.border='none';if(this.value=='Email') this.value='';" onBlur="this.className='';if(this.value.trim()=='') this.value='Email';" />
		<input type="text" value="密码" id="password" onFocus="this.className='selected'; this.type='password';if(this.value=='密码') this.value='';" onBlur="this.className='';if(this.value=='') {this.value='密码';this.type='text';};"/>
		<button id="submit" onclick="doLogin('../../');" style="top:80px;left:-37px;float:right;">登录</button>
	</div>
</div>
</body>
</html>
