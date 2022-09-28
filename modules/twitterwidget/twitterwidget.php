<?php 
class twitterwidget extends Module {
	function __construct(){
		$this->name = 'twitterwidget';
		$this->tab = 'social_networks';
		$this->version = '1.2';
        $this->author = 'MyPresta.eu';
        $this->dir = '/modules/twitterwidget/';
		parent::__construct();
		$this->displayName = $this->l('Twitter Widget Free');
		$this->description = $this->l('This module adds a twitter widget to your shop.');
        $this->mkey="freelicense";       
        if (@file_exists('../modules/'.$this->name.'/key.php'))
            @require_once ('../modules/'.$this->name.'/key.php');
        else if (@file_exists(dirname(__FILE__) . $this->name.'/key.php'))
            @require_once (dirname(__FILE__) . $this->name.'/key.php');
        else if (@file_exists('modules/'.$this->name.'/key.php'))
            @require_once ('modules/'.$this->name.'/key.php');                        
        $this->checkforupdates();
	}
        function checkforupdates(){
            if (isset($_GET['controller']) OR isset($_GET['tab'])){
                if (Configuration::get('update_'.$this->name) < (date("U")>86400)){
                    $actual_version = twitterwidgetUpdate::verify($this->name,$this->mkey,$this->version);
                }
                if (twitterwidgetUpdate::version($this->version)<twitterwidgetUpdate::version(Configuration::get('updatev_'.$this->name))){
                    $this->warning=$this->l('New version available, check MyPresta.eu for more informations');
                }
            }
        }       
	function install(){
        if (parent::install() == false 
        OR !Configuration::updateValue('update_'.$this->name,'0')
	    OR $this->registerHook('rightColumn') == false
	    OR $this->registerHook('leftColumn') == false
	    OR $this->registerHook('home') == false
        OR $this->registerHook('footer') == false
	    OR Configuration::updateValue('twitterwidget_position', '2') == false
        OR Configuration::updateValue('twitterwidget_name', 'hrabja') == false
        OR Configuration::updateValue('twitterwidget_wid', '252719104740425728') == false
		OR Configuration::updateValue('twitterwidget_width', '220') == false       
        ){
            return false;
        }
        return true;
	}
    
	public function getContent(){
	   $output="";
		if (Tools::isSubmit('submit_settings')){
            Configuration::updateValue('twitterwidget_position', Tools::getValue('new_twitterwidget_position'), true);
            Configuration::updateValue('twitterwidget_name', Tools::getValue('new_twitterwidget_name'), true);
            Configuration::updateValue('twitterwidget_wid', Tools::getValue('new_twitterwidget_wid'), true);
            Configuration::updateValue('twitterwidget_width', Tools::getValue('new_twitterwidget_width'), true);
			
            $output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';                                    
        }	   
        $output.="";
        return $output.$this->displayForm();
	}
	
	public function getconf(){
		$var = new stdClass();
		$var->twitterwidget_position=Configuration::get('twitterwidget_position');
		$var->twitterwidget_name=Configuration::get('twitterwidget_name');
		$var->twitterwidget_wid=Configuration::get('twitterwidget_wid');
		$var->twitterwidget_width=Configuration::get('twitterwidget_width');
		return $var;
	}

	public function displayForm(){
		$var=$this->getconf();
		$twt_position1=""; $twt_position2=""; $twt_position3=""; $twt_position4="";
		if ($var->twitterwidget_position==1){$twt_position1="checked=\"yes\"";}
		if ($var->twitterwidget_position==2){$twt_position2="checked=\"yes\"";}
		if ($var->twitterwidget_position==3){$twt_position3="checked=\"yes\"";}
        if ($var->twitterwidget_position==4){$twt_position4="checked=\"yes\"";}
		return'
        <iframe src="http://mypresta.eu/content/uploads/2012/10/facebook_advertise.html" width="100%" height="130" border="0" style="border:none;"></iframe>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <div style="display:block; margin:auto; overflow:hidden; ">
                    <div style="clear:both; display:block; ">
                        <fieldset>
            				<legend>'.$this->l('Twitter Widget configuration').'</legend>

			                <div style="clear:both;display:block;">
							    <label>'.$this->l('Left column').':</label>
								<div class="margin-form" valign="middle">
			                        <div style="margin-top:7px;">
									<input type="radio" name="new_twitterwidget_position" value="1" '.$twt_position1.'> '.$this->l('yes').'	
			                        </div>
								</div>
			                </div>
			                <div style="clear:both;display:block;">
							    <label>'.$this->l('Right column').':</label>
								<div class="margin-form" valign="middle">
			                        <div style="margin-top:7px;">
									<input type="radio" name="new_twitterwidget_position" value="2" '.$twt_position2.'> '.$this->l('yes').'									
			                        </div>
								</div>
			                </div>
							<div style="clear:both;display:block;">
							    <label>'.$this->l('Home').':</label>
								<div class="margin-form" valign="middle">
			                        <div style="margin-top:7px;">
									<input type="radio" name="new_twitterwidget_position" value="3" '.$twt_position3.'> '.$this->l('yes').'
									
			                        </div>
								</div>
			                </div>
                            <div style="clear:both;display:block;">
							    <label>'.$this->l('Footer').':</label>
								<div class="margin-form" valign="middle">
			                        <div style="margin-top:7px;">
									<input type="radio" name="new_twitterwidget_position" value="4" '.$twt_position4.'> '.$this->l('yes').'
									
			                        </div>
								</div>
			                </div>
			                
			                <label>'.$this->l('Box width').'</label>
            					<div class="margin-form">
            						<input type="text" style="width:200px;" value="'.$var->twitterwidget_width.'" id="new_twitterwidget_width" name="new_twitterwidget_width" onchange="">
                                    <p class="clear">'.$this->l('The minimum value of the parameter: 220').'</p>
                                </div>
				                            
            				<label>'.$this->l('Twitter name').'</label>
            					<div class="margin-form">
            						<input type="text" style="width:400px;" value="'.$var->twitterwidget_name.'" id="new_twitterwidget_name" name="new_twitterwidget_name" onchange="">
                                    <p class="clear">'.$this->l('Your account on Twitter name').'</p>
                                </div>
                                 
            				<label>'.$this->l('Twitter widget id').'</label>
            					<div class="margin-form">
            						<input type="text" style="width:150px;" value="'.$var->twitterwidget_wid.'" id="new_twitterwidget_wid" name="new_twitterwidget_wid" onchange="">
                                    <p class="clear">'.$this->l('The Twitter Widget id').'</p>
                                </div> 
                                <div align="center">
            				        <input type="submit" name="submit_settings" value="'.$this->l('Save Settings').'" class="button" />
                                </div>
                        </fieldset>                    
                    </div>
            </div>
		</form>
        ';
	}   
   
    
	function hookrightColumn($params){
		if (Configuration::get('twitterwidget_position')==2){
		    $cfg=$this->getconf();
	        global $smarty;
	        $smarty->assign('twt', $cfg);
			return $this->display(__FILE__, 'rightcolumn.tpl');
		}	
	}
	function hookleftColumn($params){
		if (Configuration::get('twitterwidget_position')==1){
		    $cfg=$this->getconf();
	        global $smarty;
	        $smarty->assign('twt', $cfg);
			return $this->display(__FILE__, 'rightcolumn.tpl');
		}	
	}
	
	function hookhome($params){
		if (Configuration::get('twitterwidget_position')==3){
		    $cfg=$this->getconf();
	        global $smarty;
	        $smarty->assign('twt', $cfg);
			return $this->display(__FILE__, 'rightcolumn.tpl');
		}	
	}
    
    function hookFooter($params){
		if (Configuration::get('twitterwidget_position')==4){
		    $cfg=$this->getconf();
	        global $smarty;
	        $smarty->assign('twt', $cfg);
			return $this->display(__FILE__, 'rightcolumn.tpl');
		}	
	}
}

class twitterwidgetUpdate extends twitterwidget {  
    public static function version($version){
        $version=(int)str_replace(".","",$version);
        if (strlen($version)==3){$version=(int)$version."0";}
        if (strlen($version)==2){$version=(int)$version."00";}
        if (strlen($version)==1){$version=(int)$version."000";}
        if (strlen($version)==0){$version=(int)$version."0000";}
        return (int)$version;
    }
    
    public static function encrypt($string){
        return base64_encode($string);
    }
    
    public static function verify($module,$key,$version){
        if (ini_get("allow_url_fopen")) {
             if (function_exists("file_get_contents")){
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module='.$module."&version=".self::encrypt($version)."&lic=$key&u=".self::encrypt(_PS_BASE_URL_.__PS_BASE_URI__));
             }
        }
        Configuration::updateValue("update_".$module,date("U"));
        Configuration::updateValue("updatev_".$module,$actual_version); 
        return $actual_version;
    }
}
?>