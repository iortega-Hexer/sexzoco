<?php
class htmlbox extends Module {
	function __construct(){
		$this->name = 'htmlbox';
		$this->tab = 'front_office_features';
        $this->author = 'MyPresta.eu';
		$this->version = '1.5.3.1';
        $this->dir = '/modules/htmlbox/';
		parent::__construct();
        $this->trusted();        
		$this->displayName = $this->l('HTMLbox');
		$this->description = $this->l('With this module you can put the HTML/JavaScript/CSS code anywhere you want');
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
                    $actual_version = htmlboxUpdate::verify($this->name,$this->mkey,$this->version);
                }
                if (htmlboxUpdate::version($this->version)<htmlboxUpdate::version(Configuration::get('updatev_'.$this->name))){
                    $this->warning=$this->l('New version available, check MyPresta.eu for more informations');
                }
            }
        }    
    
        function trusted(){
            if (_PS_VERSION_ >= "1.6.0.8"){
                if (isset($_GET['controller'])){
                    if ($_GET['controller']=="AdminModules"){
                        if (defined('self::CACHE_FILE_TRUSTED_MODULES_LIST')==true){
                            $context = Context::getContext();
                    		$theme = new Theme($context->shop->id_theme);
                    		// Save the 2 arrays into XML files
                    		$trusted_xml = new SimpleXMLElement('<modules_list/>');
                    		$trusted_xml->addAttribute('theme', $theme->name);
                    		$modules = $trusted_xml->addChild('modules');
                    		$modules->addAttribute('type', 'trusted');
                    		$module = $modules->addChild('module');
                    		$module->addAttribute('name', $this->name);
                            $success = file_put_contents( _PS_ROOT_DIR_.self::CACHE_FILE_TRUSTED_MODULES_LIST, $trusted_xml->asXML());
                            if (defined('self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST')==true){
                                file_put_contents(_PS_ROOT_DIR_.self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST,'<name><![CDATA['.$this->name.']]></name>');
                            }
                        }
                    }
                }
            }
        }     

    static function remove_doublewhitespace($s = null){
           return  $ret = preg_replace('/([\s])\1+/', ' ', $s);
    }

    static function remove_whitespace($s = null){
           $ret = preg_replace('/[\s]+/', '', $s );
           $ret = mysql_escape_string($ret);
           return $ret;
    }

    static function remove_whitespace_feed( $s = null){
           return $ret = preg_replace('/[\t\n\r\0\x0B]/', ' ', $s);
    }

    static function smart_clean($s = null){
           return $ret = trim( self::remove_doublewhitespace( self::remove_whitespace_feed($s) ) );
    }


    
	function install(){
	   
        if ($this->psversion()==6){
           $this->registerHook('displayNav');
           $this->registerHook('displayTopColumn'); 
        }
       
        if (parent::install() == false 
	    OR $this->registerHook('header') == false
	    OR $this->registerHook('top') == false
	    OR $this->registerHook('leftColumn') == false
	    OR $this->registerHook('rightColumn') == false
	    OR $this->registerHook('footer') == false
	    OR $this->registerHook('home') == false
        OR !Configuration::updateValue('update_'.$this->name,'0')
        OR Configuration::updateValue('htmlbox_header', '0') == false
        OR Configuration::updateValue('htmlbox_top', '0') == false
        OR Configuration::updateValue('htmlbox_leftcol', '0') == false
        OR Configuration::updateValue('htmlbox_rightcol', '1') == false
        OR Configuration::updateValue('htmlbox_footercol', '0') == false
        OR Configuration::updateValue('htmlbox_homecol', '0') == false
        OR Configuration::updateValue('htmlbox_body', 'enter the code here') == false
        OR Configuration::updateValue('htmlbox_ssl', '0') == false
        ){
            return false;
        }
        if ($this->psversion()==6){
            $this->registerHook('displayTopColumn');
        }
        return true;
	}
    
	public function getContent(){
	   	$output="";
		if (Tools::isSubmit('submit_settings')){
			$v=trim($_POST['htmlbox_body']);
            $v=self::smart_clean($v);
            $body=$v; 
            		
			Configuration::updateValue('htmlbox_body', $body, true);
            Configuration::updateValue('htmlbox_ssl', Tools::getValue('htmlbox_ssl'));
            Configuration::updateValue('htmlbox_home', Tools::getValue('htmlbox_home'));
            $output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
 			
			$hooks=$this->getsettingsofhtmlbox();
			foreach ($hooks['hook'] AS $hook=>$value){
					if ($hook==$_POST['htmlbox_hook']){
						$hookval=1;
					} else {
						$hookval=0;
					}
					
					Configuration::updateValue($hook,$hookval);
			}                                    
        }	   
        $output.="";
        return $output.$this->displayForm();
	}
    
    public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
    
	public function displayForm(){
			global $cookie;
            $iso = Language::getIsoById((int)($cookie->id_lang));
            $isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
            $ad = dirname($_SERVER["PHP_SELF"]);
            $form='';
            
              if ($this->psversion()==6){
                    $form.='
                    <script type="text/javascript" src="../modules/htmlbox/tinymce16.inc.js"></script>
                    <script>
        	        function addClass(id){
        				tinyMCE.execCommand("mceToggleEditor", false, id);
        			}
        			
        			function removeClass(id){
        				tinyMCE.execCommand("mceToggleEditor", false, id);
        			}	
                    </script>
                    ';
                }
            
            if ($this->psversion()==5 || $this->psversion()==6){
			$form.='
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>';
            } 
            
            if ($this->psversion()==5){
                $form.='<script>
                	$(document).ready(function(){
            			tinySetup({
            				editor_selector :"rte",
                    		theme_advanced_buttons1 : "code, save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,fontselect,fontsizeselect",
                    		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,codemagic,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                    		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                    		theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
                    		theme_advanced_toolbar_location : "top",
                    		theme_advanced_toolbar_align : "left",
                    		theme_advanced_statusbar_location : "bottom",
                    		theme_advanced_resizing : false,
                            extended_valid_elements: \'pre[*],script[*],style[*]\',
                            valid_children: "+body[meta|style|script],pre[script|div|p|br|span|img|style|h1|h2|h3|h4|h5],*[*]",
                            valid_elements : \'*[*]\',
                            force_p_newlines : false,
                            cleanup: false,
                            forced_root_block : false,
                            force_br_newlines : true
            				});
            			});
                        </script>';
                $form.='<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
            }
            
            if ($this->psversion()==5 || $this->psversion()==4 || $this->psversion()==3){
                $form.='
              <script>  
              function addClass(id){                    
				tinyMCE.execCommand(\'mceAddControl\', true, \'htmlbox_body\');
              }
			
			  function removeClass(id){    
				tinyMCE.execCommand(\'mceRemoveControl\', false, \'htmlbox_body\');
			  }	
              </script>
            ';
            }
            
            $form.="<script>";
            if ($this->psversion()==6){
                $form.='
                $(document).ready(function(){
                function tinySetup(config){
                    if(!config)
    		          config = {};
                    if (typeof config[\'editor_selector\'] != \'undefined\')
                		config[\'selector\'] = \'.\'+config[\'editor_selector\'];
                
                    default_config = {
                		selector: ".rte" ,
                		plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor",
                        toolbar1 : "code,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,cleanup,|,media,image",
                        toolbar2: "",
                		theme_advanced_toolbar_location : "top",
                		theme_advanced_toolbar_align : "left",
                		theme_advanced_statusbar_location : "bottom",
                		theme_advanced_resizing : false,
                        extended_valid_elements: \'pre[*],script[*],style[*]\',
                        valid_children: "+body[style|script],pre[script|div|p|br|span|img|style|h1|h2|h3|h4|h5],*[*]",
                		external_filemanager_path: ad+"/filemanager/",
                		filemanager_title: "File manager" ,
                		external_plugins: { "filemanager" : ad+"/filemanager/plugin.min.js"},
                		language: iso,
                		skin: "prestashop",
                		statusbar: false,
                		relative_urls : false,
                		menu: {
                			edit: {title: \'Edit\', items: \'undo redo | cut copy paste | selectall\'},
                			insert: {title: \'Insert\', items: \'media image link | pagebreak\'},
                			view: {title: \'View\', items: \'visualaid\'},
                			format: {title: \'Format\', items: \'bold italic underline strikethrough superscript subscript | formats | removeformat\'},
                			table: {title: \'Table\', items: \'inserttable tableprops deletetable | cell row column\'},
                			tools: {title: \'Tools\', items: \'code\'}
                		}
                    }
                 
                    $.each(default_config, function(index, el){
                		if (config[index] === undefined )
                			config[index] = el;
                	});
                	tinyMCE.init(config);
                }
                
                });
                ';
                
            }
             $form.="</script>";
            
            if ($this->psversion()==4) {
             $form='
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			
			$(document).ready(function(){
			tinyMCE.init({
			 mode : "specific_textareas",
    		 theme : "advanced",
    		 skin:"cirkuit",
    		 editor_selector :"rte",
        		theme_advanced_buttons1 : "code, save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,fontselect,fontsizeselect",
        		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,codemagic,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        		theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
        		theme_advanced_toolbar_location : "top",
        		theme_advanced_toolbar_align : "left",
        		theme_advanced_statusbar_location : "bottom",
        		theme_advanced_resizing : false,
                extended_valid_elements: \'pre[*],script[*],style[*]\',
                valid_children: "+body[style|script],pre[script|div|p|br|span|img|style|h1|h2|h3|h4|h5]",
                force_p_newlines : false,
                cleanup: false,
                forced_root_block : false,
                force_br_newlines : true
				});
			});
			
			function addClass(id){                    
				tinyMCE.execCommand(\'mceAddControl\', true, \'htmlbox_body\');
			}
			
			function removeClass(id){
				tinyMCE.execCommand(\'mceRemoveControl\', false, \'htmlbox_body\');
			}			
			
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
			';   
            }
            
            
            
            if ($this->psversion()==3) {
             $form='
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		    <script type="text/javascript">
            function addClass(id){                    
				tinyMCE.execCommand(\'mceAddControl\', true, \'htmlbox_body\');
			}
			
			function removeClass(id){
				tinyMCE.execCommand(\'mceRemoveControl\', false, \'htmlbox_body\');
			}
            $(document).ready(function(){
            tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
						// Theme options
						theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
						theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
						theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
						theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "bottom",
						theme_advanced_resizing : false,
						width: "600",
						height: "400",
						font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
						// Drop lists for link/image/media/template dialogs
						template_external_list_url : "lists/template_list.js",
						external_link_list_url : "lists/link_list.js",
						external_image_list_url : "lists/image_list.js",
						media_external_list_url : "lists/media_list.js",
						entity_encoding: "raw",
						convert_urls : false,
						language : "en"
					});
                    });
		</script>';
        }
					
		$array=$this->getsettingsofhtmlbox();
		$radio="";   
		foreach ($array['hook'] as $key=>$value){
			if ($key=="htmlbox_leftcol"){$place=$this->l("left column");}
			if ($key=="htmlbox_rightcol"){$place=$this->l("right column");}
			if ($key=="htmlbox_top"){$place=$this->l("top");}
            if ($this->psversion()==6){if ($key=="htmlbox_top_column"){$place=$this->l("top Column");}}
            if ($this->psversion()==6){if ($key=="htmlbox_nav"){$place=$this->l("display Nav");}}
			if ($key=="htmlbox_header"){$place=$this->l("header");}
			if ($key=="htmlbox_footercol"){$place=$this->l("footer");}
			if ($key=="htmlbox_homecol"){$place=$this->l("home");}
			
			$selected=0;
			if ($value==1){
				$selected="checked";
			}
			$radio.="
				<tr>
					<td style=\"border-left:1px solid #c0c0c0; width:150px; padding:10px; background:#f2f2f2; border-bottom:1px solid #FFF; border-top:1px solid #c0c0c0; margin-right:10px;\">$place:</td>
					<td style=\"padding:10px; background:#fff; border-right:1px solid #c0c0c0;  border-bottom:1px solid #FFF; border-top:1px solid #c0c0c0; margin-right:10px;\"><input type=\"radio\" name=\"htmlbox_hook\" value=\"$key\" $selected style=\"cursor:pointer;\"/></td>
				</tr>
			";
		}
		return $form.'		
		<div style="diplay:block; clear:both; margin-bottom:20px;">
		<iframe src="//apps.facepages.eu/somestuff/onlyexample.html" width="100%" height="150" border="0" style="border:none;"></iframe>
		</div>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                    <div style="clear:both; display:block; vertical-align:top;">
						<fieldset style="height:412px; display:inline-block; width:200px; margin-right:10px; vertical-align:top;">
							<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('HTMLbox configuration').'</legend>   
          					<h3 style="margin-bottom:0px; padding-bottom:0px; margin-bottom:20px;">'.$this->l('Where to display HTMLbox ?').'</h3>
							<table style="border-bottom:1px solid #c0c0c0;  clear:both; vertical-align:top;" cellspacing="0" cellpadding="0">
							'.$radio.'
							</table>
                            <br/>
                            '.$this->l('Only on SSL page').' <input type="checkbox" name="htmlbox_ssl" value="1" '.(Configuration::get('htmlbox_ssl')==1 ? 'checked="checked"':'').'/><br/>
                            '.$this->l('Only on homepage').' <input type="checkbox" name="htmlbox_home" value="1" '.(Configuration::get('htmlbox_home')==1 ? 'checked="checked"':'').'/>
						</fieldset>             
						
                        <fieldset style="display:inline-block; width:648px;  vertical-align:top;">
            				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('HTMLbox configuration').'</legend>
                            <h3 style="border:none; display:inline-block; float:left; margin-right:10px; margin-top:5px; margin-bottom:15px; padding-bottom:0px;">'.$this->l('Editor').'</h3>
                            
                            <span onclick="$(\'#htmlbox_body\').addClass(\'rte\'); addClass(\'htmlbox_body\');" style="cursor:pointer; 
display: inline-block;
zoom: 1;
padding: 6px 20px;
margin: 0;
cursor: pointer;
border: 1px solid #bbb;
overflow: visible;
font: bold 13px arial, helvetica, sans-serif;
text-decoration: none;
white-space: nowrap;
color: #555;
background-color: #ddd;
background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(255,255,255,1)), to(rgba(255,255,255,0))),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -webkit-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -moz-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -ms-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -o-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
-webkit-transition: background-color .2s ease-out;
-moz-transition: background-color .2s ease-out;
-ms-transition: background-color .2s ease-out;
-o-transition: background-color .2s ease-out;
transition: background-color .2s ease-out;
background-clip: padding-box;
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
border-radius: 3px;
-moz-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
-webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
text-shadow: 0 1px 0 rgba(255,255,255, .9);">'.$this->l('On').'</span>
                            <span onclick="$(\'#htmlbox_body\').removeClass(\'rte\'); removeClass(\'htmlbox_body\');" style="cursor:pointer; 
display: inline-block;
zoom: 1;
padding: 6px 20px;
margin: 0;
cursor: pointer;
border: 1px solid #bbb;
overflow: visible;
font: bold 13px arial, helvetica, sans-serif;
text-decoration: none;
white-space: nowrap;
color: #555;
background-color: #ddd;
background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(255,255,255,1)), to(rgba(255,255,255,0))),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -webkit-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -moz-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -ms-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: -o-linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
background-image: linear-gradient(top, rgba(255,255,255,1), rgba(255,255,255,0)),
 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAABkCAMAAAD0WI85AAADAFBMV…6ZSA4WijEhOLKEwBER+dPUK/hI/p/kVrA+eSV/QsCCz07/F6W5tIcqVQ/EAAAAAElFTkSuQmCC);
-webkit-transition: background-color .2s ease-out;
-moz-transition: background-color .2s ease-out;
-ms-transition: background-color .2s ease-out;
-o-transition: background-color .2s ease-out;
transition: background-color .2s ease-out;
background-clip: padding-box;
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
border-radius: 3px;
-moz-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
-webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
text-shadow: 0 1px 0 rgba(255,255,255, .9);">'.$this->l('Off').'</span>
       						<textarea class="rte rtepro" type="text" style="margin-bottom:10px; width:99%; height:300px;" id="htmlbox_body" name="htmlbox_body">'.$array['body'].'</textarea>                                                                                                                                         
                            <div align="center">
       				        <input type="submit" name="submit_settings" value="'.$this->l('Save Settings').'" class="button" onclick="addClass(\'htmlbox_body\');" />
                            </div>
                        </fieldset>                    
                    </div>
            </div>
		</form>
        '.'<div style="diplay:block; clear:both; margin-bottom:10px;">
		</div>'.$this->l('like us on Facebook').'</br><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fmypresta&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=true&amp;font=verdana&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=276212249177933" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px; margin-top:10px;" allowtransparency="true"></iframe>
        '.'<div style="float:right; text-align:right; display:inline-block; margin-top:10px; font-size:10px;">
        '.$this->l('Proudly developed by').' <a href="http://mypresta.eu" style="font-weight:bold; color:#B73737">MyPresta<font style="color:black;">.eu</font></a>
        </div>';
	}   
   
    public function getsettingsofhtmlbox(){
        $array['hook']['htmlbox_header']= Configuration::get('htmlbox_header');
		$array['hook']['htmlbox_top']= Configuration::get('htmlbox_top');
        if ($this->psversion()==6){$array['hook']['htmlbox_top_column']= Configuration::get('htmlbox_top_column');}
        if ($this->psversion()==6){$array['hook']['htmlbox_nav']= Configuration::get('htmlbox_nav');}
		$array['hook']['htmlbox_leftcol']= Configuration::get('htmlbox_leftcol');
		$array['hook']['htmlbox_rightcol']= Configuration::get('htmlbox_rightcol');
		$array['hook']['htmlbox_footercol']= Configuration::get('htmlbox_footercol');
		$array['hook']['htmlbox_homecol']= Configuration::get('htmlbox_homecol'); 
		$array['body'] = str_replace(array("\rn", "\r", "\n"), array(' ',' ',' '),Configuration::get('htmlbox_body'));
        $array['body'] = str_replace("// <![CDATA[","",$array['body']);
        $array['body'] = str_replace("// ]]>","",$array['body']);
		return $array;
    }
   
	function hookrightColumn($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_rightcol']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array( 'is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');	
		}
	}	 
    
	function hookleftColumn($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_leftcol']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array( 'is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');
		}
	}
    
	function hookhome($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_homecol']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array( 'is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');	
		}
	}
    
	function hookfooter($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_footercol']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array( 'is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');
		}
	} 
	 
	function hookheader($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_header']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array( 'is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');
		}
	}

	function hooktop($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_top']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array( 'is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');
		}
	}
	function hookdisplayTopColumn($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_top_column']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array( 'is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');
		}
	}
	function hookdisplayNav($params){
		$array=$this->getsettingsofhtmlbox();
		if ($array['hook']['htmlbox_nav']=="1"){
	        global $smarty;
	        $smarty->assign(array('htmlboxbody' => nl2br(stripslashes($array['body'])))); 
            $smarty->assign(array('is_https_htmlbox' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on"?1:0)));
            $smarty->assign(array('htmlbox_ssl' => Configuration::get('htmlbox_ssl')));
            $smarty->assign(array('htmlbox_home' => Configuration::get('htmlbox_home')));
			return $this->display(__FILE__, 'html.tpl');
		}
	}      	     	          
}

class htmlboxUpdate extends htmlbox {  
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