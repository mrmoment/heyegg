var scrHgt;
var nickname, email, password, tmpemail, tmppassword, tmpnickname;
var phpDir="./action/";
var postId=0;
var commentToId=0;//which post now commenting to
var viewMode=1;//1 for view, 0 for review
var TYPES=new Array('tech', 'news', 'fun', 'ent');
var heyType=TYPES[0];
var listIdx=0, totalLists=0;
var LIST_SIZE=15;
var MSG_NO_MORE_POST="<b>没有更多文章...</b><br /><li>过会儿更新试试</li><li>点击\"审稿\"审批新文章</li><li>点击\"投递\"发表一篇</li><button style=\"border:none; font-size:11px; margin-left:120px;\" onclick=\"closeHint('no_more_post')\">关闭建议</button>";
function setToolTop(){
	$('#page_tool').css('top',Math.max((scrHgt*0.5-$('#tool')[0].offsetHeight), 240)+"px");
}
function lightStars(idx){
	var box=document.getElementById('rate_box').children;
	for(i=0; i<idx; i++){
		box[i].className="star active";
	}
	for(i=idx;i<5; i++){
		box[i].className="star";
	}
}
function checkFav(){
	if(email){
		if(postId==0){
			dhtmlx.message({type:"error", text:"当前没有可收藏的文章" });
			return;
		}
		$('#tl_fav')[0].onclick=null;
		var check=$('#check_fav')[0];
		var like;
		if(check.className=="unchecked"){
			check.className="checked";
			like=1;
		}else{
			check.className="unchecked";
			like=-1;
		}
		$.get(phpDir+"favpost.php", { id: postId, f: like }, function(resp){handleFavPost(resp);} );
	}else{
		dhtmlx.message({type:"error", text:"请先登录" });
	}
}
function handleFavPost(resp){
	var code=getRespCode(resp);
	if(code=="fav_ok"){
		dhtmlx.message("收藏成功!");
	}else if(code=="unfav_ok"){
		dhtmlx.message("取消收藏!");
	}else{
		dhtmlx.alert("抱歉,出现未知错误!");
	}
	$('#tl_fav')[0].onclick=checkFav;
}
function triggerComment(force){//force=force_to_close
	if(!$('#comment_view')[0]){//this is null in review.php
		return;
	}
	if(postId==0){
		return;
	}
	var postHgt=$('#post_view')[0].offsetHeight;
	var commentHgt=$('#comment_view')[0].offsetHeight;
	if(commentHgt==0){
		if(!force){
			$('#post_view').hide();
			$('#comment_view').fadeIn();
			$('#comment_to').html("关于&nbsp;\""+$('#post_title').html()+"\"&nbsp;的评论");
			if(commentToId!=postId && postId!=0){
				$.get(phpDir+"loadcomment.php", { id: postId }, function(resp){handleLoadComment(resp);});
				commentToId=postId;
			}
		}
	}else{
		$('#comment_view').hide();
		$('#post_view').fadeIn();
	}	
}
function handleLoadComment(resp){
	var code=getRespCode(resp);
	var list=$('#cmt_list')[0];
	list.innerHTML="";
	if(code=="no_comment"){
		dhtmlx.message("本文暂无评论,赶紧抢沙发啦~");
	}else if(code=="load_comment_ok"){
		var data=getRespData(resp);
		var pcs=data.split("<>");		
		for(i=0; i<pcs.length-1; i++){			
			var user=my_substr(pcs[i],"u");//later use it to load avartar
			var time=my_substr(pcs[i],"t");
			var comment=my_substr(pcs[i],"c");
			addAComment(list, user, time, comment);
		}
	}else{
		dhtmlx.alert("抱歉,出现未知错误!");
	}
}
function addAComment(list, user, time, comment, head){
	var avtDiv=document.createElement('div');
	avtDiv.className="cmt_avatar";
	var cmtDiv=document.createElement('div');
	cmtDiv.className="cmt_text";
	cmtDiv.innerHTML="<i><b>"+user+"</b></i>&nbsp;("+time+")&nbsp;说:<br />"+comment;
	var pDiv=document.createElement('div');
	pDiv.className="cmtbox";
	pDiv.appendChild(avtDiv);
	pDiv.appendChild(cmtDiv);
	var hr=document.createElement('hr');
	hr.className="clear";
	if(head==1){//insert to head
		list.insertBefore(hr,list.childNodes[0]);
		list.insertBefore(pDiv,list.childNodes[0]);
	}else{
		list.appendChild(pDiv);
		list.appendChild(hr);
	}
}
function doComment(){
	if(!email){
		dhtmlx.alert("您尚未登录,无法评论。请先登录或注册后再试!");
		return;
	}
	var text=$('#cmt_content')[0].value;
	if(text==null || text==""){
		dhtmlx.message({ type:"error", text:"请输入评论内容!" });
		return;
	}
	$.post(phpDir+"postcomment.php", { id: commentToId, c:text }, function(resp){handleComment(resp);});
	//immediately add new comment locally
	addAComment($('#cmt_list')[0],nickname,"",text,1);
	$('#cmt_content')[0].value="";
}
function handleComment(resp){
	var code=getRespCode(resp);
	if(code=="no_such_post"){
		dhtmlx.message({ type:"error", text:"原帖已删除,无法评论,请更新网页" });
	}else if(code=="comment_ok"){
		dhtmlx.message("成功添加评论!");
	}else{
		dhtmlx.message({ type:"error", text:"抱歉,出现未知错误!" });
	}
}
function login(){
	$('#login_wrapper').show();	
}
function showTopbar(nick){
	if(nick){		
		$('#anony').hide()
		$('#identity').fadeIn();		
		$('#nickname')[0].innerHTML=nick;
	}else{
		$('#identity').hide()
		$('#anony').fadeIn();
		$('#nickname')[0].innerHTML="";
	}
}
function handleUAuth(resp){
	if(getRespCode(resp)=="verify_ok"){
		email=tmpemail;
		password=tmppassword;
		$('#login_wrapper').hide();
		showTopbar(getRespData(resp));		
	}else{
		dhtmlx.alert("抱歉,出现未知错误!");
	}
}
function getRespCode(resp){
	return resp.substring(resp.indexOf("<code>")+6, resp.indexOf("</code>"));
}
function getRespData(resp){
	return resp.substring(resp.indexOf("<data>")+6, resp.indexOf("</data>"));
}
function logout(path){
	delCookie("email");
	delCookie("password");
	if(path==undefined){
		path="";
	}
	$.post(path+phpDir+"userquit.php", {}, function(resp){handleUserQuit(resp);});	
}
function handleUserQuit(resp){
	email=null;
	password=null;
	showTopbar();
}
function doLogin(path){
	tmpemail=$('#email')[0].value;
	tmppassword=$('#password')[0].value;
	if(tmpemail!="test"||tmppassword!="test"){
		tmppassword=hex_md5(tmppassword);
	}
	if(path==undefined){
		path="";
	}
	$.post(path+phpDir+"verifyuser.php", {u: tmpemail, p: tmppassword}, function(resp){handleUAuth(resp);});
	//todo: hint circle
}
function quickEgg(id,type){
	if(!id){
		id=0;
	}
	if(!type){
		type=heyType;
	}
	$.get(phpDir+"loadpost.php", { tp: type, id: id, m: viewMode }, function(resp){handleLoadEgg(resp);});
}
function handleLoadEgg(resp){
	var code=getRespCode(resp);
	if(code=="no_post"){		
		dhtmlx.message(MSG_NO_MORE_POST);
		var title=$('#post_title')[0];
		if(title.innerHTML=="加载中..." && postId==0){
			title.innerHTML="暂无文章";
			$('#post_author_name').html("-");
			$('#post_time').html("时间:&nbsp;-");
			$('#post_text').html("");
		}
	}else if(code=="no_such_post"){
		dhtmlx.alert("文章找不到了,看看别的吧" );
	}else{
		resp=decodeURIComponent(resp);
		var pcs=resp.split("<>");
		postId=Number(pcs[0]);
		$('#post_title').html(pcs[1]);
		$('#post_time').html("时间:&nbsp;"+pcs[2]);
		$('#post_text').html(pcs[3]);
		$('#post_author_name').html(pcs[4]);
		$('#post_author_name')[0].href="user.php?uid="+pcs[5];
		//check other components
		if($('#tl_fav')[0]){//in review.php, it's null
			$('#tl_fav')[0].onclick=checkFav;
			if(pcs[6]==0){
				$('#check_fav')[0].className="unchecked";
			}else{
				$('#check_fav')[0].className="checked";
			}
		}
		closeList();
		triggerComment(true);
	}
}
function nearbyEgg(orient,id){
	if(!id){
		id=postId;
	}
	$.get(phpDir+"loadnearbypost.php", { tp: heyType, id: id, m: viewMode, o: orient }, function(resp){handleLoadEgg(resp);});
	triggerComment(true);
}
function openList(){
	$('#list').show();
	$('#list').animate({
		width:"420px"
	},300,function(){
		if(!$('#list_rows')[0].childNodes[0] || $('#list_rows')[0].childNodes[0].className!="rows"){
			loadList();
		}		
	}
	);
}
function closeList(){
	$('#list').animate({
		width:"0"
	},300,function(){
		$('#list').hide();
	}
	);
}
function prevList(){
	if(listIdx==0){
		dhtmlx.message(MSG_NO_MORE_POST);
		return;
	}
	listIdx--;
	loadList();
}
function nextList(){
	if(listIdx==totalLists-1 || totalLists==0){		
		dhtmlx.message(MSG_NO_MORE_POST);
		return;
	}
	listIdx++;
	loadList();
}
function loadList(){
	$.get(phpDir+'loadlist.php', { n: listIdx, tp: heyType, m: viewMode }, function(resp){handleLoadList(resp);});
}
function handleLoadList(resp){
	$('#list_rows').html('');//reset
	resp=decodeURIComponent(resp.trim());
	var mbrs=resp.split("<|>");
	var list=$('#list_rows')[0];
	for(i=0; i<mbrs.length-1; i++){
		var blks=mbrs[i].split("<>");
		var pDiv=document.createElement('div');
		pDiv.className="row";
		pDiv.innerHTML=blks[1];//title
		pDiv.title=blks[1];
		pDiv.onclick=function(v){return function(){loadEggById(v);}}(blks[0])//blks[0]=id, note the grammar
		list.appendChild(pDiv);
	}
	postsInType=Number(mbrs[mbrs.length-1]);//last piece is total lists
	totalLists=Math.ceil(postsInType/LIST_SIZE);
	if(isNaN(totalLists)){
		totalLists=0;
	}
	showListNow();
}
function showListNow(){
	$('#list_now').html((listIdx+1)+"&nbsp;/&nbsp;"+totalLists+"页");
}
function gotoList(){
	var target=$('#jumpNum')[0].value;
	if(target!=null && !isNaN(target) && target.indexOf(".")<0){
		target=Number(target);
		if(target>0 && target<=totalLists){
			listIdx=target-1;
			loadList();
		}else{
			dhtmlx.message( {type:"error", text:"页码超过范围"} );
		}
	}else{		
		dhtmlx.message( {type:"error", text:"请输入要跳转的页码数字"} );
	}
}
function votePost(score){
	if(postId==0){
		dhtmlx.message({type:"error", text:"当前没有可打分的文章"});
		return;
	}
	if(email){
		$.get(phpDir+"votepost.php", { id: postId, s: score }, function(resp){handleVotePost(resp);} );
	}
}
function handleVotePost(resp){
	//alert(resp);
	var code=getRespCode(resp);
	if(code=="vote_ok"){
		dhtmlx.message("打分成功!");
	}else if(code=="already_voted"){
		dhtmlx.message("已经打过分了!");
	}else{
		dhtmlx.alert("抱歉,出现未知错误!");
	}
}
function loadEggById(id){
	if(!id){
		id=postId;
	}
	$.get(phpDir+"loadpostbyid.php", { id: id }, function(resp){handleLoadEgg(resp);});	
}
function checkEmail(){
	var r_email=document.getElementById('mail').value;
	if(r_email==""){
		dhtmlx.alert("邮箱地址必须填写!");
		return false;
	}else{
		var at=r_email.indexOf("@");
		var dot=r_email.lastIndexOf(".");
		if(at<1 || dot<3 || dot<at || at!=r_email.lastIndexOf('@') || dot==r_email.length-1){
			dhtmlx.alert("邮箱格式不正确,请检查!");
			return false;
		}
	}
	return true;
}
function checkNick(){
	var nick=document.getElementById('nick').value;
	if(nick.length<2){
		dhtmlx.alert("昵称不能少于2个字符");
		return false;
	}
	return true;
}
function checkPass(){
	var pw=document.getElementById('passwd').value;
	if(pw.length<6 || pw.length>18){
		dhtmlx.alert("密码由6至18个字符组成");
		return false;
	}
	var pw2=document.getElementById('passwd2').value;
	if(pw!=pw2){
		dhtmlx.alert("确认密码不一致,请检查");
		return false;
	}
	return true;
}
function checkCode(){
	var code=document.getElementById('regcode').value;
	if(code.length!=12){
		dhtmlx.alert("邀请码不正确");
		return false;
	}
	return true;
}
function doReg(){
	var email=document.getElementById('mail').value;
	var pw=document.getElementById('passwd').value;
	var nick=document.getElementById('nick').value;
	var code=document.getElementById('regcode').value;
	if( checkEmail() &&	checkPass() && checkNick() && checkCode() ){
		dhtmlx.message("注册中,请稍候...");
		//document.getElementById('submit').onclick="";
		$.post(phpDir+"adduser.php", { m: email, p: hex_md5(pw), n: nick, c: code}, function(resp){handleReg(resp);});
	}
}
function handleReg(resp){
	var code=getRespCode(resp);
	if(code=="register_ok"){
		dhtmlx.message("恭喜,注册成功!");
	}else if(code=="existing_email"){
		dhtmlx.alert("该邮箱已被注册!请直接登录或用其它邮箱注册");
	}else if(code=="existing_nick"){
		dhtmlx.alert("该昵称已经存在了,请尝试其它名字吧");
	}else if(code=="invite_code_error"){
		dhtmlx.alert("邀请码不OK哦");
	}else if(code=="bad_nick"){
		dhtmlx.alert("不适当的昵称,保护黑蛋,人人有责");
	}else{
		dhtmlx.alert("抱歉,出现未知错误!");
	}
	$('#submit')[0].onclick=doReg;
}
function browseType(idx){
	if(heyType!=TYPES[idx]){
		heyType=TYPES[idx];
		postId=0;
		commentToId=0;
		listIdx=0; 
		totalLists=0;
		closeList();
		triggerComment(true);
		quickEgg();
	}
}
function closeHint(cmd){
	if(cmd=="no_more_post"){
		MSG_NO_MORE_POST="<b>暂时没有更多文章.</b>";
	}
}
function doSelect(){
	//TODO
}