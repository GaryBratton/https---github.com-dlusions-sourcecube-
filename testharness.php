<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="/plugins/system/CMT_Source_Cube/CMT_Source_Cube.js"></script>
<script>

	var CORSpath = '//www.athocdev.com/plugins/system/CMT_Source_Cube/CMT_AJAX.php';
	var cookieDomain ='.athocdev.com';
	var frameSource = "//www.athocdev.com/plugins/system/CMT_Source_Cube/runit.php?url=";

</script>
<?php 

$CORSpath = 'www.athocdev.com/plugins/system/CMT_Source_Cube/CMT_AJAX.php';
$cookieDomain = '.athocdev.com';

$xmldocURL = "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$xmldocURL = str_replace(".php",".xml",$xmldocURL);
$xmldocpath = __DIR__."/testharness.xml";

$bpath = $_SERVER["DOCUMENT_ROOT"];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['action']) && $_POST['action'] == 'save'){
	$xmlDoc = new DOMDocument();
	$doc = fetchXML($xmldocURL);
		//$xmlDoc->load($xmldocURL)
	$xmlDoc->loadXML($doc);
	$xmlDoc->formatOutput = true;
	$xmlDoc->preserveWhiteSpace = true;
	$rows = $xmlDoc->getElementsByTagName('row');
	if($rows->length < $_POST['row']-1){//add row to xml
		$root = $xmlDoc->documentElement;
		$element = $xmlDoc->createElement('row');
		$se = $xmlDoc->createElement('desc');
		$element->appendChild($se);
		$se = $xmlDoc->createElement('referrer');
		$element->appendChild($se);
		$se = $xmlDoc->createElement('querystr');
		$element->appendChild($se);
		$se = $xmlDoc->createElement('form2load');
		$element->appendChild($se);
		$root->appendChild($element);
	}
	$i=2;// skip the header row
	foreach ($rows as $row) {
		if($i++ == $_POST['row']){
			$item = $row->getElementsByTagName('desc');
			if(trim($_POST['descStr']) == ""){ //delete the row
				$row->parentNode->removeChild($row);
				break;
			}
			$item->item(0)->nodeValue=$_POST['descStr'];
			$item = $row->getElementsByTagName('referrer');
			$item->item(0)->nodeValue=$_POST['refStr'];
			$item = $row->getElementsByTagName('querystr');
			$item->item(0)->nodeValue=$_POST['queryStr'];
			$item = $row->getElementsByTagName('form2load');
			$item->item(0)->nodeValue='';
			$item->item(0)->appendChild($xmlDoc->createCDataSection($_POST['formStr']));

			break;
		}
	}
    $strxml = $xmlDoc->saveXML();
    $handle = fopen($xmldocpath, "w");
    fwrite($handle, $strxml);
    fclose($handle);
}

?>
<head>
<style>
	td, th {border:1px solid #CCC;padding:2px 5px;}
	table{border-collapse:collapse;}
	input[type="text"]{width:100%;}
	input[type="button"]{background-color:#bbb;}
	.resfld{width:15% !important; color:red;border-color:#CCC;margin:5px;}
</style>



</head>
<body>

bbSource<input type='text' value='' name='bbSource' class='resfld'>
sourceType<input type='text' value='' name='sourceType' class='resfld'>
sourceSubTYPE<input type='text' value='' name='sourceSubTYPE' class='resfld'><br>
firstTouch<input type='text' value='' name='firstTouch' class='resfld'>
lastTouch<input type='text' value='' name='lastTouch' class='resfld'>
<form>
	CORSpath <input type='text' name='CORSpath' value='<?php echo $CORSpath?>' style='width:200px'>
	CookieDomain <input type='text' name='cookieDomain' value='<?php echo $cookieDomain?>' style='width:200px'>
	<input style='float:right;background-color:#AAA;' type='button' id='closeIframe' value='closeIframe' style='width:200px'>
</form>

<iframe id='testframe' src='' height="30px" width="300px"></iframe>

<table>
	<tr>
		<th>Action</th>
		<th>Desc</th>
		<th>Referrer</th>
		<th>Query str</th>
		<th>Form to load</th>
	</tr>
	<tr>
		<td><input type='button' value='run' onclick='runtestclear()'></td>
		<td>Clear all Related Cookies</td>
		<td></td>
		<td></td>
		<td></td>		
	</tr>
<?php 
$xmlDoc = new DOMDocument();
$doc = fetchXML($xmldocURL);


//$xmlDoc->load($xmldocURL);
@$xmlDoc->loadXML($doc);
$rows = $xmlDoc->getElementsByTagName('row');
foreach ($rows as $row) {
	echo "<tr><td><input type='button' value='run' onclick='runtest(this)'><input type='button' value='edit' onclick='editrow(this)'></td>";
	$item = $row->getElementsByTagName('desc');
	echo "<td>".$item->item(0)->nodeValue."</td>";
	$item = $row->getElementsByTagName('referrer');
	echo "<td>".urldecode($item->item(0)->nodeValue)."</td>";
	$item = $row->getElementsByTagName('querystr');
	echo "<td>".urldecode($item->item(0)->nodeValue)."</td>";
	$item = $row->getElementsByTagName('form2load');
	echo "<td>".$item->item(0)->nodeValue."</td></tr>";
}

function fetchXML($url){
	//$url = "http://www.example.org/";
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$username  = "dengel";
	$password = "2vy3nSLp";
	
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
	
	$contents = curl_exec($ch);
	if (curl_errno($ch)) {
		echo curl_error($ch);
		echo "\n<br />";
		$contents = '';
	} else {
		curl_close($ch);
	}
	
	if (!is_string($contents) || !strlen($contents)) {
		echo "Failed to get contents.";
		$contents = '';
	}
	
	return $contents;
}
?>
<tr><td><input type='button' value='add row' onclick='addrow(this)'></td><td></td><td></td><td></td><td></td></tr>
</table>


<script>
	var queryStr 	= '';
	var addingrow 	= false;
	var referrerStr = '';
	var imga 		= new Image(100,100); // width, height values are optional params 
	var imgb 		= new Image(100,100); // width, height values are optional params 
	var d 			= new Date();
	var exdays		= -5;
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires 	= "expires="+d.toUTCString();
	var rString 	= function(length) {
    	var text 	= "";
    	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    	for(var i = 0; i < length; i++) {
        	text += possible.charAt(Math.floor(Math.random() * possible.length));
    	}
    	return text;
	}
jQuery("#closeIframe").on('click',function(){
	jQuery("#testframe").hide().attr('src','');
});

function runtestclear(){
	debugger;
	vendorval="";

	img.src = "CMT_AJAX.php?setReferCookie=firstTouch^delete&rnd=" + rString(8);
	document.cookie = "firstTouch="+vendorval+";" + expires+"; path=/; domain="+cookieDomain+";";

//	img.onerror = step;
		
//}
//function step(){
	img.onerror =null;
	img.src = "CMT_AJAX.php?setReferCookie=lastTouch^delete&rnd="+rString(8);
	
	document.cookie = "lastTouch="+vendorval+";" + expires+"; path=/; domain="+cookieDomain+";";

//	img2.src = "CMT_AJAX.php?setReferCookie=lastTouch^delete&rnd="+rString(8);
	img2.src = ajaxpath+'?setReferCookie=lastTouch^'+lastTouch+ "^" + encodeURI(queryStr) + "^" + encodeURI(referrerStr) + "&rnd=" + randStr(8);
	
	document.cookie = "lastTouch="+vendorval+";" + expires+"; path=/; domain="+cookieDomain+";";

	document.getElementsByName("lastTouch")[0].value=' ';
	document.getElementsByName("firstTouch")[0].value=' ';
	document.getElementsByName("bbSource")[0].value=' ';
	document.getElementsByName("sourceType")[0].value=' ';
	document.getElementsByName("sourceSubTYPE")[0].value=' ';
}

function runtest(thisInput){
	//debugger;
	var tr = thisInput.parentNode.parentNode;
	referrerStr = thisInput.parentNode.parentNode.children[2].textContent.trim();
	queryStr 	= thisInput.parentNode.parentNode.children[3].textContent.trim();
	formStr		= '';
	if(thisInput.parentNode.parentNode.children[4]){
		//formStr		= encodeURIComponent((thisInput.parentNode.parentNode.children[4].textContent).trim());
		formStr		= (thisInput.parentNode.parentNode.children[4].textContent).trim();
		formStr = formStr.replace("&","%26");
		formStr = formStr.replace("&","%26");
		formStr = formStr.replace("&","%26");
		formStr = formStr.replace("&","%26");
	}

	// make a curl call to the form using these inputs
	jQuery('#testframe').attr('src',frameSource+
	formStr+"&referrer="+
	referrerStr+"&query="+
	queryStr);

	jQuery('#testframe').height('1000px').width('1000px').css("position:absolute");
	jQuery('#testframe').show();
}



function editrow(thisInput){
	var tr 	= thisInput.parentNode.parentNode;
	descStr = thisInput.parentNode.parentNode.children[1].textContent;
	thisInput.parentNode.parentNode.children[1].innerHTML = "<input type='text' value='"+descStr+"'></input>";
	refStr 	= thisInput.parentNode.parentNode.children[2].textContent;
	thisInput.parentNode.parentNode.children[2].innerHTML = "<input type='text' value='"+refStr+"'></input>";
	queryStr= thisInput.parentNode.parentNode.children[3].textContent;
	thisInput.parentNode.parentNode.children[3].innerHTML = "<input type='text' value='"+queryStr+"'></input>";
	formStr = thisInput.parentNode.parentNode.children[4].textContent;
	thisInput.parentNode.parentNode.children[4].innerHTML = "<input type='text' value='"+formStr+"'></input>";
	thisInput.parentNode.parentNode.children[0].innerHTML = "<input type='button' value='cancel' onclick='canceledit(this)'></input><input type='button' value='save'onclick='saveedit(this)'></input>";
}

function addrow(thisInput){
	// on exit from save, we need to add a new row
	addingrow = true;
	editrow(thisInput);
}


function saveedit(thisInput){
	var tr 	= thisInput.parentNode.parentNode;
	var poststr="action=save&row="+tr.rowIndex;
	descStr = thisInput.parentNode.parentNode.children[1].children[0].value;
	thisInput.parentNode.parentNode.children[1].innerHTML = descStr;
	poststr += "&descStr= "+encodeURIComponent(descStr);
	
	refStr 	= thisInput.parentNode.parentNode.children[2].children[0].value;
	thisInput.parentNode.parentNode.children[2].innerHTML = refStr;
	poststr += "&refStr= "+encodeURIComponent(refStr);
	queryStr= thisInput.parentNode.parentNode.children[3].children[0].value;
	thisInput.parentNode.parentNode.children[3].innerHTML = queryStr;
	poststr += "&queryStr= "+encodeURIComponent(queryStr);
	formStr = thisInput.parentNode.parentNode.children[4].children[0].value;
	thisInput.parentNode.parentNode.children[4].innerHTML = formStr;
	poststr += "&formStr= "+encodeURIComponent(formStr);
	thisInput.parentNode.parentNode.children[0].innerHTML = "<input type='button' value='run' onclick='runtest(this)'></input><input type='button' value='edit' onclick='editrow(this)'></input>";
	// now ajax post to our own processes
	$.ajax({
		  type: "POST",
		  url: '/plugins/system/CMT_Source_Cube/testharness.php',
		  data: poststr,
		  success: success
		});
	function success(){
		addingrow = false;
		alert('saved');
	}
}

function canceledit(thisInput){
	var tr 	= thisInput.parentNode.parentNode;
	descStr = thisInput.parentNode.parentNode.children[1].children[0].value;
	thisInput.parentNode.parentNode.children[1].innerHTML = descStr;
	refStr 	= thisInput.parentNode.parentNode.children[2].children[0].value;
	thisInput.parentNode.parentNode.children[2].innerHTML = refStr;
	queryStr= thisInput.parentNode.parentNode.children[3].children[0].value;
	thisInput.parentNode.parentNode.children[3].innerHTML = queryStr;
	formStr = thisInput.parentNode.parentNode.children[4].children[0].value;
	thisInput.parentNode.parentNode.children[4].innerHTML = formStr;
	thisInput.parentNode.parentNode.children[0].innerHTML = "<input type='button' value='run' onclick='runtest(this)'></input><input type='button' value='edit' onclick='editrow(this)'></input>";
}

</script>
</body>



