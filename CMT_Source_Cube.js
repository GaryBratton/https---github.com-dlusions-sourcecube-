var domain = "athocdev.com";

//var ajaxpath = "//athocdev.com/plugins/system/CMT_Source_Cube/CMT_AJAX.php";
var ajaxpath = "//svcountrydancer.com/CMT_Source_Cube/CMT_AJAX.php";

// installs through plugin.  all componets in plugin folder, but this is latest version.
// live site hold js in media cmt_pardot
//2017 install js - note that js and CMT_AJAX need to be mapped above this line
// in the plugin the script is loaded twice and called only on the form opening - this is WRONG



var realqueryStr	= window.location.search.toLowerCase();
var referrerStr 	= '';//document.referrer.toLowerCase();
var img 			= new Image(100,100); // width, height values are optional params 
var img2 			= new Image(100,100); // width, height values are optional params 

var queryStr 		= "";
var referrerHost	= "";
var firstTouch		= "";
var lastTouch		= "";
var referrer 		= "";
var referrerType	= "";
var redirStr   		= "";
var glue			= "";
var rhref 			= "";
var formStr   		= "";
var firstTouchCookie= "";
var lastTouchCookie = "";
var randStr			= "";
var d 				= new Date();
var exdays			= 30;
    	d.setTime(d.getTime() + (exdays*24*60*60*1000));
var expires = "expires="+d.toUTCString();
var refsource		= ""; // the referer 

jQuery( document ).ready(function() {
	getReferrer();
});

function getReferrer(){// MAIN ENTRY POINT script to find and set referrer in athoc forms
	firstTouchCookie= "";
	lastTouchCookie	= "";
	randStr 		= function(length) {
    	var text 	= "";
    	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    	for(var i = 0; i < length; i++) {
        	text += possible.charAt(Math.floor(Math.random() * possible.length));
    	}
    	return text;
	}
	makeCorsRequest();
}

function getReferrer2(){
	/* now set from CORS 
	firstTouchCookie
	lastTouchCookie 
	*/
	referrer 		= "";
	referrerType	= "";
	glue			= "";
	firstTouch		= "";
	lastTouch		= "";
	bbSource		= "";
	sourceType		= "";
	sourceSubTYPE	= "";
	
	
	
	if(queryStr == ""){ // if not set in a test
		queryStr = window.location.search.toLowerCase();
		q=queryStr.split("&");
		for (i = 0; i < q.length; i++) {
			if(q[i].indexOf("query=")==0){
				queryStr = q[i].substr(6,200).toLowerCase();
			}
		}
		
	}
	if (referrerStr == ""){ // if not set in a test
		referrerStr = document.referrer.toLowerCase();
		q=window.location.search.toLowerCase().split("&");
		for (i = 0; i < q.length; i++) {
			if(q[i].indexOf("referrer=")==0){
				referrerStr = q[i].substr(9,200).toLowerCase();
			}
		}

	}

//			Source Type			Subtype		BBSource		Referrer			Querystr key			qryPdKey
var sarr = [
            ["Organic Search",	"Google",	"Web",			"www.google.com",	"",						[]],
            ["Organic Search",	"Bing",		"Web",			"www.bing.com/",	"",						[]],
            ["Organic Search",	"Yahoo",	"Web",			"search.yahoo.com",	"",						[]],
            ["Organic Search",	"Ask.Com",	"Web",			"www.ask.com",		"",						[]],
            ["Paid Search",		"Google",	"Paid Media",	"",					"utm_source=google",	[]],
            ["Paid Search",		"Google",	"Paid Media",	"",					"gclid=",				[]],
            ["Paid Search",		"Bing",		"Paid Media",	"",					"utm_source=bing",		[]],
            ["Paid Search",		"Yahoo",	"Paid Media",	"/cbclk2/",			"utm_source=gemini",	[]],
            ["Paid Search",		"Yahoo",	"Paid Media",	"search.yahoo.com",	"_ylt=",				[]],
            ["Organic Social",	"Blog",		"Web",			"",					"",						[]],
            ["Organic Social",	"Facebook",	"Web",			"www.facebook.com",	"",						[]],
            ["Organic Social",	"Twitter",	"Web",			"t.co/",		 	"",						[]],
            ["Organic Social",	"Linkedin",	"Web",			"www.linkedin.com",	"d_flagship3_search_srp_top",[]],
            ["Organic Social",	"Linkedin",	"Web",			"",					"d_flagship3_feed",		[]],
            ["Paid Social",		"Facebook",	"Paid Media",	"l.facebook.com",	"utm_source=facebook",	["utm_medium=ads","utm_medium=paid_social"]],
            ["Paid Social",		"Twitter",	"Paid Media",	"",					"utm_source=twitter.com",[]],
            ["Paid Social",		"Linkedin",	"Paid Media",	"bit.ly/",			"utm_source=LinkedIn",	["utm_medium=display"]],
            ["Referral",		"Other Site","External",	"",					"",						[]],
            ["Referral",		"Partners",	"Partner",		"",					"",						[]],
            ["Misc",			"Press Rel","PR",			"",					"",						[]],
            ["Misc",			"Direct Web","Web",			"",					"",						[]]
            ];
	
	// search the url search string for utm and other authoritive sources

	for(i = 0; i < sarr.length; i++){
		if((sarr[i][3].length > 0 && referrerStr.indexOf(sarr[i][3].toLowerCase()) > -1)
			|| (sarr[i][4].length > 0 && queryStr.indexOf(sarr[i][4].toLowerCase()) > -1)){
			bbSource		= sarr[i][2];
			sourceType		= sarr[i][0];
			sourceSubTYPE	= sarr[i][1];
			referrerType 	= sarr[i][0];
			referrer		= sarr[i][1];
		}
	}

	if((queryStr.indexOf("utm_source") > -1) && referrer == ""){
		referrerType = "Misc";
		var toks  = queryStr.split("utm_source=");
		toks  = toks[1].split("&");
		referrer = toks[0];
	}
	// processing the rest of the utm params here
	var utm_source = utm_medium = utm_campaign = utm_term = utm_content = "";
	if((queryStr.indexOf("utm") > -1)){
		var toks  = queryStr.split("utm");
		for (c=0;c<toks.length;c++){
			if(toks[c].indexOf("_source")>-1){
				t2=toks[c].split("=");
				utm_source = t2[1];
			}
			if(toks[c].indexOf("_medium")>-1){
				t2=toks[c].split("=");
				utm_medium = t2[1];
			}
			if(toks[c].indexOf("_campaign")>-1){
				t2=toks[c].split("=");
				utm_campaign = t2[1];
			}
			if(toks[c].indexOf("_term")>-1){
				t2=toks[c].split("=");
				utm_term = t2[1];
			}
			if(toks[c].indexOf("_content")>-1){
				t2=toks[c].split("=");
				utm_content = t2[1];
			}
		}
	}
	
	referrerHost = getHostFromUrl(referrerStr,"short");

	if(referrerHost > "" && referrerHost != "null" && referrerHost.toLowerCase() != "athoc" && referrerHost.toLowerCase() != "athocdev" && referrer == ""){
		referrer = referrerHost;
		if(referrerHost.toLowerCase().indexOf("bing") > -1 || referrerHost.toLowerCase().indexOf("google") > -1 || referrerHost.toLowerCase().indexOf("yahoo") > -1){
			referrerType = "Organic Search";
		}else {
			referrer = "Referrer "+getHostFromUrl(referrerStr,"long");
			referrerType 	= "Referral";
		}
		bbSource		= "External";
		sourceType		= "Referral";
		sourceSubTYPE	= "Other Site";
		referrer		= referrerHost

	}
	

	if(referrer > "" && referrer.indexOf('athoc') < 0){
		lastTouch = titleCase(sourceSubTYPE+" "+referrerType," ");
	}
	if(lastTouch == "" && lastTouchCookie > ""){
		lastTouch = titleCase(lastTouchCookie," ");
	}
	if(firstTouchCookie == "" && lastTouch > ""){
		firstTouch = lastTouch;
	}
	
	// write out the cookies
		
	if(lastTouch > "" && lastTouch !="No Referrer"){
		queryStr = queryStr.replace("?","");
		queryStr = queryStr.replace("=",":");
		//url then ref setReferCookie=lastTouch^Google paid^https://www.spark.com^https://www.spark.com
		img2.src = ajaxpath+'?setReferCookie=lastTouch^'+lastTouch+ "^" + encodeURI(queryStr) + "^" + encodeURI(referrerStr) + "&rnd=" + randStr(8);
		//refsource =  encodeURI(queryStr) + "^" + encodeURI(referrerStr);
		lastTouchCookie = lastTouch;
	}
	if(firstTouch > "" && firstTouchCookie == ""){
		img.src = ajaxpath+'?setReferCookie=firstTouch^'+firstTouch+'&rnd='+randStr(8);
		firstTouchCookie = firstTouch;
	}
	if(firstTouchCookie > "" && firstTouch == ""){
		firstTouch = firstTouchCookie;
	}
	
	// put in the defaults
	if(lastTouch == "")		{lastTouch		= "No Referrer";}
	if(firstTouch == "")	{firstTouch		= "No Referrer";}
	if(bbSource == "")		{bbSource		= "Web";}
	if(sourceType == "")	{sourceType		= "MISC";}
	if(sourceSubTYPE == "")	{sourceSubTYPE	= "Direct Web";}

	
	//create form fields if needed
	if(!document.getElementsByName("form[lastTouch]")[0]){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[lastTouch]' value=''/>");
	}
	if(!document.getElementsByName("form[firstTouch]")[0]){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[firstTouch]' value=''/>");
	}
	if(!document.getElementsByName("form[bbSource]")[0]){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[bbSource]' value=''/>");
	}
	if(!document.getElementsByName("form[sourceType]")[0]){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[sourceType]' value=''/>");
	}
	if(!document.getElementsByName("form[sourceSubTYPE]")[0]){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[sourceSubTYPE]' value=''/>");
	}
	if(!document.getElementsByName("form[refsource]")[0]){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[refsource]' value=''/>");
	}
	// utm fields - 	var utm_source = utm_medium = utm_campaign = utm_term = utm_content = "";

	if(!document.getElementsByName("form[utm_source]")[0] && utm_source > ""){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[utm_source]' value=''/>");
	}
	if(!document.getElementsByName("form[utm_medium]")[0] && utm_source > ""){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[utm_medium]' value=''/>");
	}
	if(!document.getElementsByName("form[utm_campaign]")[0] && utm_source > ""){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[utm_campaign]' value=''/>");
	}
	if(!document.getElementsByName("form[utm_term]")[0] && utm_source > ""){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[utm_term]' value=''/>");
	}
	if(!document.getElementsByName("form[utm_content]")[0] && utm_source > ""){
		jQuery('.uk-form fieldset').append("<input type='hidden' name='form[utm_content]' value=''/>");
	}

	
	// write out the results
	
	if(lastTouch > "" && document.getElementsByName("lastTouch")[0]){
		document.getElementsByName("lastTouch")[0].value = lastTouch;
	}
	if(lastTouch > "" && document.getElementsByName("form[lastTouch]")[0]){
		document.getElementsByName("form[lastTouch]")[0].value = lastTouch;
	}
	if(lastTouch > "" && document.getElementsByName("form[lastTouch]")[1]){ // for two forms on page
		document.getElementsByName("form[lastTouch]")[1].value = lastTouch;
	}

	if(firstTouch > "" && document.getElementsByName("firstTouch")[0]){
		document.getElementsByName("firstTouch")[0].value = firstTouch;
	}
	if(firstTouch > "" && document.getElementsByName("form[firstTouch]")[0]){
		document.getElementsByName("form[firstTouch]")[0].value = firstTouch;
	}
	if(firstTouch > "" && document.getElementsByName("form[firstTouch]")[1]){// for two forms on page
		document.getElementsByName("form[firstTouch]")[1].value = firstTouch;
	}


	if(bbSource > "" && document.getElementsByName("form[bbSource]")[0]){
		document.getElementsByName("form[bbSource]")[0].value = bbSource;
	}
	if(bbSource > "" && document.getElementsByName("form[bbSource]")[1]){// for two forms on page
		document.getElementsByName("form[bbSource]")[1].value = bbSource;
	}

	if(sourceType > "" && document.getElementsByName("form[sourceType]")[0]){
		document.getElementsByName("form[sourceType]")[0].value = sourceType;
	}
	if(sourceType > "" && document.getElementsByName("form[sourceType]")[1]){// for two forms on page
		document.getElementsByName("form[sourceType]")[1].value = sourceType;
	}
	if(sourceSubTYPE > "" && document.getElementsByName("form[sourceSubTYPE]")[0]){
		document.getElementsByName("form[sourceSubTYPE]")[0].value = sourceSubTYPE;
	}
	if(sourceSubTYPE > "" && document.getElementsByName("form[sourceSubTYPE]")[1]){// for two forms on page
		document.getElementsByName("form[sourceSubTYPE]")[1].value = sourceSubTYPE;
	}
	//GB 9-17-18
	
	if(refsource > "" && document.getElementsByName("form[refsource]")[0]){
		document.getElementsByName("form[refsource]")[0].value = refsource;
	}
	if(refsource > "" && document.getElementsByName("form[refsource]")[1]){// for two forms on page
		document.getElementsByName("form[refsource]")[1].value = refsource;
	}
	//GB 9-17-18
	
// this section works for testharness only
	if(parent.document.getElementsByName("lastTouch")[0]){parent.document.getElementsByName("lastTouch")[0].value='';}
	if(parent.document.getElementsByName("firstTouch")[0]){parent.document.getElementsByName("firstTouch")[0].value='';}
	if(parent.document.getElementsByName("bbSource")[0]){parent.document.getElementsByName("bbSource")[0].value='';}
	if(parent.document.getElementsByName("sourceType")[0]){parent.document.getElementsByName("sourceType")[0].value='';}
	if(parent.document.getElementsByName("sourceSubTYPE")[0]){parent.document.getElementsByName("sourceSubTYPE")[0].value='';}
	
	if(lastTouch > "" && parent.document.getElementsByName("lastTouch")[0]){
		parent.document.getElementsByName("lastTouch")[0].value = lastTouch;
	}
	if(firstTouch > "" && parent.document.getElementsByName("firstTouch")[0]){
		parent.document.getElementsByName("firstTouch")[0].value = firstTouch;
	}
	if(bbSource > "" && parent.document.getElementsByName("bbSource")[0]){
		parent.document.getElementsByName("bbSource")[0].value = bbSource;
	}
	if(sourceType > "" && parent.document.getElementsByName("sourceType")[0]){
		parent.document.getElementsByName("sourceType")[0].value = sourceType;
	}
	if(sourceSubTYPE > "" && parent.document.getElementsByName("sourceSubTYPE")[0]){
		parent.document.getElementsByName("sourceSubTYPE")[0].value = sourceSubTYPE;
	}

	
	
	// special case for pardot - get the field id from the actual form
	
	if(lastTouch > "" && jQuery(".lastTouch input")){
		jQuery(".lastTouch input").val(lastTouch);
	}
	if(firstTouch > "" && jQuery(".firstTouch input")){
		jQuery(".firstTouch input").val(firstTouch);
	}
	if(bbSource > "" && jQuery(".bbSource input")){
		jQuery(".bbSource input").val(bbSource);
	}
	if(sourceType > "" && jQuery(".sourceType input")){
		jQuery(".sourceType input").val(sourceType);
	}
	if(sourceSubTYPE > "" && jQuery(".sourceSubTYPE input")){
		jQuery(".sourceSubTYPE input").val(sourceSubTYPE);
	}
	
	//GB 9-17-18
	
	if(refsource > "" && jQuery(".refsource input")){
		jQuery(".refsource input").val(refsource);
	}
	//GB 9-17-18
	
	return;
} // end of getReferrer function


	function processCookies(cookieText){ // runs after CORS call is complete
		var coks = cookieText.split(";");
		
		for(i=0;i<coks.length;i++){
			var v = coks[i].split('=');
			
			if(v[0]=='firstTouch' && firstTouch == "" && v[1] > ""){
				firstTouchCookie = v[1];
			}
			if(v[0]=='lastTouch' &&  v[1] > ""){
				var lt = v[1].split("^");
				lastTouchCookie = lt[0];
				refsource = lt[1]+"^"+lt[2];
			}
		} 
		getReferrer2();
	}
	


	// Create the XHR object.
	function createCORSRequest(method, url) {
  		var xhr = new XMLHttpRequest();
  		if ("withCredentials" in xhr) {// XHR for Chrome/Firefox/Opera/Safari.
    		xhr.open(method, url, true);
  		} else if (typeof XDomainRequest != "undefined") {// XDomainRequest for IE.
    		xhr = new XDomainRequest();
    		xhr.open(method, url);
  		} else {// CORS not supported.
    		xhr = null;
  		}
  		return xhr;
	}

	// Make the actual CORS request.
	function makeCorsRequest() {
  		// This is a ATHOC server file that supports CORS.
  		var url = ajaxpath+'?fetchReferCookies=firstTouch^lastTouch&rnd='+randStr(8);
  		var xhr = createCORSRequest('GET', url);
  		xhr.withCredentials = true;

  		if (!xhr) {
  			var CORSresp='CORS not supported';
    		alert('CORS not supported');
    		return;
  		}

  		// Response handlers.
  		xhr.onload = function() {
    		var text = xhr.responseText;
    		processCookies(text);
  		};

  		xhr.onerror = function() {
  			var CORSresp='Woops, there was an error making the request.';
  		};

  		xhr.send();
	}

var titleCase = function(str, glue){
    glue = (glue) ? glue : ['of', 'for', 'and'];
    return str.replace(/(\w)(\w*)/g, function(_, i, r){
        var j = i.toUpperCase() + (r != null ? r : "");
        return (glue.indexOf(j.toLowerCase())<0)?j:j.toLowerCase();
    });
};

	function getHostFromUrl(url,numparts){
		var surl = url;
		var toks='';
		
		if(url.indexOf("//") > -1){
			toks = url.split("//");
			surl = toks[1];
		}
		if(surl.indexOf("/") > -1){
			toks = surl.split("/");
			surl = toks[0];
		}
		if(surl.indexOf("&") > -1){
			toks = surl.split("&");
			surl = toks[0];
		}
		if(surl.indexOf("?") > -1){
			toks = surl.split("?");
			surl = toks[0];
		}

		var parts = surl.split(".");
		if(parts.length > 1){
			if(numparts == "long" && parts.length>2 ){
				m=parts.length-2;
				r=parts.length-1;
				parts.splice(m, r);
				var toks = parts;
			}else{
				var toks = parts;
			}
			if(parts.length != "long"){
				var toks = [parts[parts.length-2]];
			}
			return toks.join(".").replace("/", "");
		}else{
			var parts = surl.split(" ");
			if(parts.length > 0){
				return parts[0];
			}else{
				return surl;
			}
		}
	}

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
