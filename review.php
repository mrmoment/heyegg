<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>黑蛋 | 审稿模式开启!</title>
<link rel="stylesheet" type="text/css" href="jsMessage/codebase/themes/message_growl_dark.css">
<link href="res/style.css" rel="stylesheet" type="text/css" />
<link href="res/effect.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="res/jquery-1.6.2.min.js"></script>
<script language="javascript" src="res/extool.js"></script>
<script language="javascript" src="res/md5-min.js"></script>
<script language="javascript" src="res/hey.js"></script>
<script type="text/javascript" src='jsMessage/codebase/message.js'></script>
<script language="javascript">
window.onload=function(){
	var scrHgt=screen.availHeight;
	$('#post_text').css('min-height',(scrHgt-370)+"px");
	$('#comment_view').css('min-height',(scrHgt-216)+"px");
	viewMode=0;//review mode
	initUser();
	if(!email){
		dhtmlx.alert('您尚未登录,可以浏览待审核的文章,但无法给它们评分');
	}else{
		var pid="<?php
			if( isset($_GET['pid']) ){
				print $_GET['pid'];
			}else{
				print "";
			}
		?>";
		if(pid==""){
			quickEgg();	
		}else{
			loadEggById(pid);
		}
	}
}
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
</script>
</head>

<body>
<div id="topbar">
	<div id="btm_border"></div>
	<div id="left_panel"></div>
	<div id="right_panel">
		<div id="anony"><span onclick="login()">登录</span><span onclick="window.open('register.html');">注册</span></div>
		<div id="identity" style="display:none"><span>欢迎,&nbsp;<a id="nickname"></a>!</span><span onclick="logout()">退出</span></div>
	</div>
	<div id="logo" onclick="window.open('index.php');"></div>
</div>
<div id="main">
	<div id="view">
		<div id="post_view">
			<div id="post_title">加载中...</div>
			<div id="post_avatar"><!--img src="img/default_avatar_64.gif" width="32" height="32"/--></div>
			<div id="post_author">作者:&nbsp;<a id="post_author_name" target="_blank">加载中...</a></div>
			<div id="post_time">时间:&nbsp;加载中...</div>
			<div id="post_text">正在载入...</div>
		</div>
	</div>
	<div id="footer">
		<div style="width:330px; margin:auto; font-size:12px; font-family:sans-serif">All rights reserved&copy;2012&nbsp;黑蛋网&nbsp;京ICP备11034032号</div>
	</div>
</div>
<div id="tool">
	<div id="tool_main">		
		<div id="left_tool" class="toolpanel">
			<div class="leftbtn">
				<div class="ltoolname" onclick="browseType(0);">
					<div style="float:left">科技</div>
					<div class="type_icon" style="float:right"></div>
				</div>				
			</div>
			<div class="leftbtn">
				<div class="ltoolname" onclick="browseType(1);">
					<div style="float:left">社会</div>
					<div class="type_icon" style="float:right"></div>
				</div>				
			</div>
			<div class="leftbtn">
				<div class="ltoolname" onclick="browseType(2);">
					<div style="float:left">奇趣</div>
					<div class="type_icon" style="float:right"></div>
				</div>				
			</div>
			<div class="leftbtn">
				<div class="ltoolname" onclick="browseType(3);">
					<div style="float:left">娱乐</div>
					<div class="type_icon" style="float:right"></div>
				</div>				
			</div>
		</div><!--eof left_tool-->
		<div id="right_tool" class="toolpanel">			
			<div id="tl_list" class="rightbtn tran-9" onclick="openList();">
				<div class="rtoolname">
					<div id="list_btn" style="float:left">列表</div>
					<div class="tl_icon" style="float:right"></div>
				</div>
			</div>			
			<div id="tl_score" class="rightbtn tran-9">				
				<div class="rtoolname">
					<div style="float:left">打分</div>
					<div id="rate_box" style="float:right">
						<div class="star active" onMouseMove="lightStars(1)" onMouseOver="lightStars(1)" onClick="votePost(1);"></div>
						<div class="star active" onMouseMove="lightStars(2)" onMouseOver="lightStars(2)" onClick="votePost(2);"></div>
						<div class="star active" onMouseMove="lightStars(3)" onMouseOver="lightStars(3)" onClick="votePost(3);"></div>
						<div class="star" onMouseMove="lightStars(4)" onMouseOver="lightStars(4)" onClick="votePost(4);"></div>
						<div class="star" onMouseMove="lightStars(5)" onMouseOver="lightStars(5)" onClick="votePost(5);"></div>
					</div>
				</div>
			</div>			
		</div><!--eof right_tool-->
		<div id="page_tool">
			<div class="pager" id="prev_post" onclick="nearbyEgg(-1);" title="较早的文章"></div>
			<div class="pager" id="next_post" onclick="nearbyEgg(1);" title="较新的文章"></div>
		</div>
	</div>
</div>
<div id="list">
	<div id="close_list" onclick="closeList()"><div></div></div>
	<div id="list_rows">	</div>
	<div id="list_pager">
		<div id="prev_list" onclick="prevList();"></div>
		<div id="list_now" align="center">1&nbsp;/&nbsp;14&nbsp;页</div>
		<div id="list_goto"><input id="jumpNum" /><a onclick="gotoList()">跳至</a></div>
		<div id="next_list" onclick="nextList();"></div>
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
