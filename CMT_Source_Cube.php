<?php
// no direct access
defined( '_JEXEC' ) or die;
 
class plgSystemCMT_Source_Cube extends JPlugin
{
	/**
	 * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
	 * If you want to support 3.0 series you must override the constructor
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;
 
	function canRun(){
        if (class_exists('RSFormProHelper')) return true;
        $helper = JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
        if (file_exists($helper)){
            require_once($helper);
            RSFormProHelper::readConfig();
            return true;
        }
        return false;
    }
    public function rsfp_onFormSave($form){
    
    	$formId = JRequest::getInt('formId');
    	##GB set the frmlist of forms that use this plugin
    	$frmlist = explode(",",$this->params->get('FormIDs', ""));
    	$key = array_search($formId, $frmlist);
    	if($_POST['source_published'] == 1){
    		if($key > -1){						// good move on
    		}else{
    			$frmlist[] = $formId;	// add it
    		}
    	}else{
    		if($key > -1){
    			unset($frmlist[$key]);			// remove it
    		}else{								// good move on
    		}
    	}
    
    	$table = new JTableExtension(JFactory::getDbo());
    	$table->load(array('element' => 'CMT_Source_Cube'));
    
    	$this->params->set('FormIDs', implode(",",$frmlist));
    	$this->params->set('CookieDomain',$_POST['CookieDomain']);
    	$this->params->set('AJAXPath',$_POST['AJAXPath']);
    	$table->set('params', $this->params->toString());
    
    	$table->store();
    	## GB end
    }
    public function rsfp_bk_onAfterShowFormEditTabsTab(){
    	$lang = JFactory::getLanguage();
    	$lang->load('plg_system_rsfpsalesforce');
    
    	echo '<li><a href="javascript: void(0);" id="source">
				<span class="rsficon rsficon-cloud"></span>
				<span class="inner-text">CMT_Source_Cube Integration</span></a></li>';
    }
    
    public function rsfp_bk_onAfterShowFormEditTabs(){
    	$formId = JRequest::getInt('formId');
    	$frmlist = explode(",",$this->params->get('FormIDs', ""));
    	$key = array_search($formId, $frmlist);
    	$CookieDomain = $this->params->get('CookieDomain', "");
    	$AJAXPath = 	$this->params->get('AJAXPath', "http://athocdev.com/plugins/system/CMT_Source_Cube/CMT_Source_Cube.php");//"http://www2.athoc.com";
    	   
    	$html = '<div id="sourcediv">
				<table>
				<tr>
					<td class="key" nowrap="nowrap" align="right" width="80">Use CMT_Source_Cube Integration ?</td>
					<td nowrap="nowrap">
					<fieldset id="source_published" class="btn-group radio">';
    	if($key === false){
    		$html.='<input id="source_published0" name="source_published" value="0" class="inputbox" type="radio" checked="checked">
					<label for="source_published0" class="inputbox btn btn-danger"> No </label>
					<input id="source_published1" name="source_published" value="1"  class="inputbox" type="radio">
							<label for="source_published1" class="inputbox btn"> Yes </label>';
    	}else{
    		$html.='<input id="source_published0" name="source_published" value="0" class="inputbox" type="radio">
					<label for="source_published0" class="inputbox btn"> No </label>
					<input id="source_published1" name="source_published" value="1"  class="inputbox" type="radio" checked = "checked">
							<label for="source_published1" class="inputbox btn btn-success"> Yes </label>';
    	}
    	$html.='</fieldset>
					</td>
				</tr>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key">Cookie Domain</td>
					<td>
						<input name="CookieDomain" id="CookieDomain" style="width:80%" value="'.$CookieDomain.'"/>
					</td>
				</tr>	
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key">AJAX Path</td>
					<td>
						<input name="AJAXPath" id="AJAXPath" style="width:80%" value="'.$AJAXPath.'"/>
					</td>
				</tr>	
								
			</table>
		</div>';
    
    	echo $html;
    }
    
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
		//JFactory::getDocument()->addScript(JURI::root(true).'/plugins/system/CMT_Source_Cube/CMT_Source_Cube.js');
		JHtml::_('jquery.framework');
		JHtml::_('script', 'plugins/system/CMT_Source_Cube/CMT_Source_Cube.js');
		// build the array of sources from the params
		
		$ar = $this->params->get('sourceFields', "");
		$sc[]='var sarr = [';
		$glue='';
		foreach($ar as $itm){
			$sc[]=$glue."['".$itm->SourceType."','".$itm->Subtype."','".$itm->BBSource."','".$itm->Referrer."','".$itm->Querystrkey."',[]]";
			$glue=",";
		}
		$sc[]="];\n";
				/// save the sarr variable to a js file
		$file = 'plugins/system/CMT_Source_Cube/sarr.js';
		file_put_contents($file, implode("\n",$sc));
				///

		$document = JFactory::getDocument();
		//$document->addScriptDeclaration(implode("\n",$sc));
		$document->addScript($file);

		$cors_list = $this->params->get('cors_list', "");

		$cookiePath = $this->params->get('cookiepath', "");
		// Pardot source referrer AJAX CORS code
		if(isset($_GET['fetchReferCookies'])){
			ob_clean();
			$_SERVER['HTTP_ORIGIN'] = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "athocdev.com";
			
			$http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "none";
			if(stripos($cors_list,$http_origin)>-1){
				header("Access-Control-Allow-Origin: $http_origin");
			}
			header("Access-Control-Allow-Credentials: true");
			header("Access-Control-Expose-Headers: testharness");
			header("Content-Type: text/html; charset=utf-8");
			$glue="";
			if(isset($_COOKIE['firstTouch'])){
				echo "firstTouch=".$_COOKIE['firstTouch'];
				$glue=";";
			}
			if(isset($_COOKIE['lastTouch'])){
				echo $glue."lastTouch=".$_COOKIE['lastTouch'];
				$glue=";";
			}
			ob_flush();
			die;
		}
		// Pardot source referrer AJAX CORS code
	    if(isset($_GET['setReferCookie'])){
			ob_clean();
			$cooks = $_GET['setReferCookie'];
			$toks = explode("^",$cooks);
			if($toks[1] == "delete"){
				setcookie($toks[0], urldecode($toks[1]), time() - 36000,"/",$cookiePath);
			}else{
				if($toks[0]=='firstTouch'){
					$timer =time()+36000;
				}else{ // last touch, add the url and ref to the cookie
					$timer =time()+3600;
					$toks[1] = isset($toks[2]) ? $toks[1]."^".$toks[2] : $toks[1];
					$toks[1] = isset($toks[3]) ? $toks[1]."^".$toks[3] : $toks[1];

				}
				//$timer=time()+36000;
				$success = setcookie($toks[0],urldecode($toks[1]),$timer, "/",$cookiePath);
				$_COOKIE[$toks[0]]=urldecode($toks[1]);
			}
			echo $_COOKIE[$toks[0]];
			ob_flush();
			die;
		}
    }
    
	function rsfp_f_onBeforeFormDisplay($args){	

			/*no longer used - removesetcookie("refersource", $vendorval, time()+36000, "/",".athoc.com");// set the cookie for other places to use*/
		$formid = isset($args['formId']) ? $args['formId'] : 0;
		$frmlist = explode(",",$this->params->get('FormIDs', ""));
		$key = array_search($formid, $frmlist);
		
		if($key > -1){
			JHtml::_('jquery.framework');
			//JFactory::getDocument()->addScript('/plugins/system/CMT_Source_Cube/CMT_Source_Cube.js');
			JHtml::_('script', 'plugins/system/CMT_Source_Cube/CMT_Source_Cube.js');
				
			$args['formLayout'] = str_replace('</fieldset>','
			<input id="firstTouch" type="hidden" name="form[firstTouch]" value="">
			<input id="lastTouch" type="hidden" name="form[lastTouch]" value="">
			</fieldset>
			',$args['formLayout']);

		}//if key

	}//function
}//class
?>