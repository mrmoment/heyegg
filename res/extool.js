/*firefox's event*/
function __firefox(){
    HTMLElement.prototype.__defineGetter__("runtimeStyle", __element_style);
    window.constructor.prototype.__defineGetter__("event", __window_event);
    Event.prototype.__defineGetter__("srcElement", __event_srcElement);
}
function __element_style(){
    return this.style;
}
function __window_event(){
    return __window_event_constructor();
}
function __event_srcElement(){
    return this.target;
}
function __window_event_constructor(){
    if(document.all){
        return window.event;
    }
    var _caller = __window_event_constructor.caller;
    while(_caller!=null){
        var _argument = _caller.arguments[0];
        if(_argument){
            var _temp = _argument.constructor;
            if(_temp.toString().indexOf("Event")!=-1){
                return _argument;
            }
        }
        _caller = _caller.caller;
    }
    return null;
}
if(window.addEventListener){
    __firefox();
}

/*Browser detection*/
var Browser = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent) || this.searchVersion(navigator.appVersion) || "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			versionSearch: "Version"
		},{
			prop: window.opera,
			identity: "Opera"
		},{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "IE",
			versionSearch: "MSIE"
		},{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod"
	    },{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};

/*Cookie management*/
function setCookie(name,value){
    var days = 30;
    var exp  = new Date();
    exp.setTime(exp.getTime() + days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name){
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
    if(arr != null) return unescape(arr[2]); return null;
}
function delCookie(name){
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

/*String prototype*/
String.prototype.trim= function(){   
    return this.replace(/(^\s*)|(\s*$)/g, "");  
}

/*Misc*/
function my_substr(origin, tag){//parse xml like strings
	var fromstr="<"+tag+">";
	var tostr="</"+tag+">";
	if(origin.indexOf(fromstr)>=0 && origin.indexOf(tostr)>=0){
		return origin.substring(origin.indexOf(fromstr)+fromstr.length, origin.indexOf(tostr));
	}else{
		return null;
	}
}

function getSelectedText() 
{ 
	if (window.getSelection) 
		{ // This technique is the most likely to be standardized. 
		// getSelection() returns a Selection object, which we do not document. 
		return window.getSelection().toString(); 
	} 
	else if (document.getSelection) 
	{ 
		// This is an older, simpler technique that returns a string 
		return document.getSelection(); 
	} 
	else if (document.selection) 
	{ 
		// This is the IE-specific technique. 
		// We do not document the IE selection property or TextRange objects. 
		return document.selection.createRange().text; 
	} 
}
