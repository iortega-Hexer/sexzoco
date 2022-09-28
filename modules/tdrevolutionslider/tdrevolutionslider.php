<?php
if (!defined('_CAN_LOAD_FILES_'))
    exit;

include_once(dirname(__FILE__) . '/tdrevolutionsliderModel.php');
class TDRevolutionSlider extends Module {
    private $_html;
    private $_display;
    
    public function __construct() {
        $this->name = 'tdrevolutionslider';
        $this->tab = 'front_office_features';
        $this->version = '1.1.1';
        $this->author = 'ThemesDeveloper';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('ThemesDeveloper Revolution Slider');
        $this->description = $this->l('Home Page Revolution Slider by ThemeDeveloper');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall your details ?');
        $this->secure_key = Tools::encrypt($this->name);
  
    }

    public function install() {
        /* Adds Module*/
        if (parent::install() && $this->registerHook('home') && $this->registerHook('header')) {
            /* Install tables */
            $respons = tdrevolutionsliderModel::createTables();
            return $respons;
        }
        return false;
    }

    public function uninstall() {
        /* Deletes Module */
        if (parent::uninstall()) {
            /* Deletes tables */
            $respons = tdrevolutionsliderModel::DropTables();
            return $respons;
        }
        return false;
    }

    public function getContent() {
        $this->_html = '';
  $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);
		$iso = $this->context->language->iso_code;
if (version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
			$this->_html .= '
			<script type="text/javascript">	
				var iso = \''.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
				var pathCSS = \''._THEME_CSS_DIR_.'\' ;
				var ad = \''.dirname($_SERVER['PHP_SELF']).'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
			<script language="javascript" type="text/javascript">
				id_language = Number('.$id_lang_default.');
				tinySetup();
			</script>';
		else
		{
			$this->_html .= '
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript">
				tinyMCE.init({
					mode : "textareas",
					theme : "advanced",
					plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
					theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
					theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
					theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
					content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",
					document_base_url : "'.__PS_BASE_URI__.'",
					width: "600",
					height: "auto",
					font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
					template_external_list_url : "lists/template_list.js",
					external_link_list_url : "lists/link_list.js",
					external_image_list_url : "lists/image_list.js",
					media_external_list_url : "lists/media_list.js",
					elements : "nourlconvert",
					entity_encoding: "raw",
					convert_urls : false,
					language : "'.(file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'"
				});
				id_language = Number('.$id_lang_default.');
			</script>';
		}
        if (Tools::isSubmit('TDsupmitvelue') || Tools::isSubmit('deleteSlider')) {
            if ($this->_postValidation())
                $this->_insertSlider();
            $this->_displaySlider();
        }
        elseif (Tools::isSubmit('addNewSlider') || (Tools::isSubmit('id_tdrevolutionslider')))
            $this->_displayForm();
        else
            $this->_displaySlider();

        return $this->_html;
    }

    private function _insertSlider() {
        global $currentIndex;
        $errors = array();
        $moduledir=_PS_MODULE_DIR_.'tdrevolutionslider/banner/';
        $moduleurl='modules/tdrevolutionslider/banner/';
        
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;
        
        if (Tools::isSubmit('TDsupmitvelue')) {
            $languages = Language::getLanguages(false);

            if (Tools::isSubmit('addNewSlider')) {
                $position = Db::getInstance()->getValue('
			SELECT COUNT(*) 
			FROM `' . _DB_PREFIX_ . 'tdrevolutionslider`');
                    
                Db::getInstance()->Execute('
            INSERT INTO `' . _DB_PREFIX_ . 'tdrevolutionslider` (`slider_link`,`active`,`position`,`id_shop`) 
            VALUES("' . Tools::getValue('slider_link') . '",' . (int) Tools::getValue('td_active_slide') . ',' . (int) $position . ',' . (int) $id_shop . ')');

                $id_tdrevolutionslider = Db::getInstance()->Insert_ID();
                foreach ($languages as $language) {
                    
                    $name = $_FILES['td_image_' . $language['id_lang']]['name'];
                    $image_url = $moduleurl . $name;

                    $path = $moduledir . $name;
                    $tmpname = $_FILES['td_image_' . $language['id_lang']]['tmp_name'];
                    move_uploaded_file($tmpname, $path);
            
                    Db::getInstance()->Execute('
                INSERT INTO `' . _DB_PREFIX_ . 'tdrevolutionslider_lang` (`id_tdrevolutionslider`, `id_lang`, `image_title`, `slider_content`,`image_url`) 
                VALUES(' . (int) $id_tdrevolutionslider . ', ' . (int) $language['id_lang'] . ', 
                "' . pSQL(Tools::getValue('td_title_' . $language['id_lang'])) . '", 
                 "' . htmlspecialchars(Tools::getValue('td_content_' . $language['id_lang'])) . '","' . $image_url . '")');
                }
            } elseif (Tools::isSubmit('updateSlider')) {

               $tdsliderid = Tools::getvalue('id_tdrevolutionslider');
               
              // print_r($tdsliderid) ;
                Db::getInstance()->Execute('
                UPDATE `' . _DB_PREFIX_ . 'tdrevolutionslider`
                SET `slider_link`= "' . Tools::getValue('slider_link') . '",
                `active` = ' . (int) Tools::getValue('td_active_slide') . ',
               `id_shop` = "' . $id_shop . '"
                WHERE `id_tdrevolutionslider` = ' . (int) ($tdsliderid));
                //echo $image_url;
              
               
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                       if ($_FILES['td_image_' . $language['id_lang']]['name']):

                        $name = $_FILES['td_image_' . $language['id_lang']]['name'];
                        $image_url = $moduleurl . $name;

                        $path = $moduledir . $name;
                        $tmpname = $_FILES['td_image_' . $language['id_lang']]['tmp_name'];
                        move_uploaded_file($tmpname, $path);

                    else:
                        $image_url = Tools::getvalue('image_old_link_' . $language['id_lang']);
                    endif;
                    
                    
                    Db::getInstance()->Execute('
                            UPDATE `' . _DB_PREFIX_ . 'tdrevolutionslider_lang` 
                            SET `image_title` = "' . pSQL(Tools::getValue('td_title_' . $language['id_lang'])) . '",                    
                            `slider_content` = "' .htmlspecialchars(Tools::getValue('td_content_' . $language['id_lang'])) . '",
                            `image_url` = "' . $image_url . '"
                            WHERE `id_tdrevolutionslider` = ' . (int) $tdsliderid . '  AND `id_lang`= ' . (int) $language['id_lang']);
                }
                 unlink($image_url);
            }
        }
        elseif (Tools::isSubmit('deleteSlider') AND Tools::getValue('id_tdrevolutionslider')) {
            Db::getInstance()->Execute('
                DELETE FROM `' . _DB_PREFIX_ . 'tdrevolutionslider`
                WHERE `id_tdrevolutionslider` = ' . (int) (Tools::getValue('id_tdrevolutionslider')));

            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'tdrevolutionslider_lang` 
				WHERE `id_tdrevolutionslider` = ' . (int) (Tools::getValue('id_tdrevolutionslider')));
        }
        if (count($errors))
            $this->_html .= $this->displayError(implode('<br />', $errors));
        elseif (Tools::isSubmit('TDsupmitvelue') && Tools::getValue('id_tdrevolutionslider'))
            $this->_html .= $this->displayConfirmation($this->l('Advertise Update Successfully'));
        elseif (Tools::isSubmit('TDsupmitvelue'))
            $this->_html .= $this->displayConfirmation($this->l('Advertise added Successfully'));
        elseif (Tools::isSubmit('deleteSlider'))
            $this->_html .= $this->displayConfirmation($this->l('Deletion successful'));
    }

    private function _postValidation() {
        $errors = array();
        if (Tools::isSubmit('TDsupmitvelue')) {
            $languages = Language::getLanguages(false);
        }
        elseif (Tools::isSubmit('deleteSlider') AND !Validate::isInt(Tools::getValue('id_tdrevolutionslider')))
            $errors[] = $this->l('Invalid ID');

        if (sizeof($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
            return false;
        }
        return true;
    }

    private function _displayForm() {

        global $currentIndex, $cookie;
        $updatevalue = NULL;
        if (Tools::isSubmit('updateSlider') AND Tools::getValue('id_tdrevolutionslider'))
            $updatevalue = tdrevolutionsliderModel::getSliderByID((int) Tools::getValue('id_tdrevolutionslider'));
//print_r($updatevalue);
        /* Languages preliminaries */
        $defaultLanguage = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);
        $iso = Language::getIsoById((int) ($cookie->id_lang));
        $divLangName = 'title¤image¤td_image¤image¤description';

        $this->_html .= '
		<fieldset>
			<legend>' . $this->l('ThemesDeveloper Revolution Slider') . '</legend>
			';

        $this->_html.= '<form method="post" action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" enctype="multipart/form-data">
      <fieldset>
        <legend>' . $this->l('Add A New Slider') . '</legend>';


        $this->_html .= '
		<label for="active_on">' . $this->l('Active:') . '</label>
		<div class="margin-form">
			<img src="../img/admin/enabled.gif" alt="Yes" title="Yes" />
			<input type="radio" name="td_active_slide" id="active_on" ' . ((isset($updatevalue[0]['active']) && $updatevalue[0]['active'] == 0) ? '' : 'checked="checked" ') . ' value="1" />
			<label class="t" for="active_on">' . $this->l('Yes') . '</label>
			<img src="../img/admin/disabled.gif" alt="No" title="No" style="margin-left: 10px;" />
			<input type="radio" name="td_active_slide" id="active_off" ' . ((isset($updatevalue[0]['active']) && $updatevalue[0]['active'] == 0) ? 'checked="checked" ' : '') . ' value="0" />
			<label class="t" for="active_off">' . $this->l('No') . '</label>
		</div>';

       $this->_html .='<label>' . $this->l('Title') . '</label>
	<div class="margin-form">';
        foreach ($languages as $language) {
            $this->_html.= '
            <div id="title_' . $language['id_lang'] . '" style="display: ' . ($language['id_lang'] == $defaultLanguage ? 'block' : 'none') . ';float: left;">
                    <input type="text" name="td_title_' . $language['id_lang'] . '" id="td_title_' . $language['id_lang'] . '" size="64"  value="' . (Tools::getValue('td_title_' . $language['id_lang']) ? Tools::getValue('td_title_' . $language['id_lang']) : (isset($updatevalue['image_title'][$language['id_lang']]) ? $updatevalue['image_title'][$language['id_lang']] : '')) . '"/>
            </div>';
        }
        $this->_html .=$this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
        
         $this->_html .='</div><div class="clear"></div><br/>';
         
$this->_html .='<label>' . $this->l('Slider Link') . '</label>
	<div class="margin-form">';
        
            $this->_html.= '
            <div id="slider_link">
                    <input type="text" name="slider_link" id="slider_link" size="64"  value="' . (Tools::getValue('slider_link') ? Tools::getValue('slider_link') : (isset($updatevalue[0]['slider_link']) ? $updatevalue[0]['slider_link'] : '')) . '"/>
            </div>';
   

        
         $this->_html .='</div><div class="clear"></div><br/>';
         
		$this->_html .= '
		<label>'.$this->l('Slider Content:').' </label>
		<div class="margin-form">';
		foreach ($languages as $language)
		{
			$this->_html .= '<div id="description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
				<textarea class="rte" name="td_content_'.$language['id_lang'].'" rows="10" cols="60">'.(Tools::getValue('td_content_' . $language['id_lang']) ? Tools::getValue('td_content_' . $language['id_lang']) : (isset($updatevalue['slider_content'][$language['id_lang']]) ? $updatevalue['slider_content'][$language['id_lang']] : '')).'</textarea>
			</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'description', true);
		$this->_html .= '</div><div class="clear"></div><br />';
              
      if (Tools::isSubmit('updateSlider') AND Tools::getValue('id_tdrevolutionslider')) {
            $this->_html.= '<div class="margin-form">';
            foreach ($languages as $language) {
                $this->_html.= '<div id="image_' . $language['id_lang'] . '" style="display: ' . ($language['id_lang'] == $defaultLanguage ? 'block' : 'none') . ';float: left;">
                    <input type="hidden" name="image_old_link_' . $language['id_lang'] . '" value="' . $updatevalue['image_url'][$language['id_lang']] . '" />
                    <img src="' . __PS_BASE_URI__ . $updatevalue['image_url'][$language['id_lang']] . '" width=60 height=60></div> ';
            }
            $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'image', true);
            $this->_html.= '</div>';
        }
                
        $this->_html.= '<div class="clear"></div><label>' . $this->l('Upload Image') . '</label>';
        foreach ($languages as $language)
		{
            $this->_html .= '<div id="td_image_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
             <input type="file" name="td_image_'.$language['id_lang'].'" value=""/>
                    </div>';
         }
         $this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'td_image', true);
$this->_html .= '<div class="clear"></div><br />';
  
        $this->_html.= '
               <div class="clear"></div><br/>
        <div class="clear center">
            <input type="submit" class="button" name="TDsupmitvelue" value="' . $this->l('Save') . '" />
            <a class="button" style="position:relative; padding:2px 3px 2px 3px; top:1px" href="' . $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '">' . $this->l('Cancel') . '</a>
 
        </div>
      </fieldset>
    </form>
   
';
    }

    private function _displaySlider() {

        global $currentIndex, $cookie;

        $slider = tdrevolutionsliderModel::getAllSlider();

       // print_r($slider) ;
        
        $this->_html .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/jquery/plugins/jquery.tablednd.js"></script><fieldset>
            <legend>ThemesDeveloper Home Revolution Slider Options</legend>
<script type="text/javascript" src="' .  __PS_BASE_URI__ . 'modules/' . $this->name . '/' . $this->name . '.js"></script>
<script type="text/javascript">tdrevolutionslider(\'' . $this->secure_key . '\');</script>';

        $this->_html .= '<p><a href="' . $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&addNewSlider"><img src="' . _PS_ADMIN_IMG_ . 'add.gif" alt="" /> ' . $this->l('Add a new slider') . '</a></p>';
        if ($slider):
            $this->_html.= '<table width="100%" id="tdrevolutionsliderdata" class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr class="nodrag nodrop">
				<th width="5%">' . $this->l('ID') . '</th>
                                <th width="40%">' . $this->l('Images') . '</th>
				<th width="25%" >' . $this->l('Title') . '</th>
				<th width="10%" >' . $this->l('Active') . '</th>
                                <th width="10%">' . $this->l('Position') . '</th>
				<th width="10%">' . $this->l('Actions') . '</th>
			</tr>
			</thead>
			<tbody>';
        endif;
        $i = 1;
        $irow = 0;
        foreach ($slider as $tdsliderdata):

            $this->_html .= '<tr id="tr_0_' . $tdsliderdata['id_tdrevolutionslider'] . '_' . $tdsliderdata['position'] . '" ' . ($irow++ % 2 ? 'class="alt_row"' : '') . '>
                             <td>' . $tdsliderdata['id_tdrevolutionslider'] . '</td>
                                 <td><img src="' . __PS_BASE_URI__ . $tdsliderdata['image_url'] . '" width="80%" height=120></td>
                             <td>' . $tdsliderdata['image_title'] . '</td>
                             <td>';
            if ($tdsliderdata['active'] == 1) :

                $this->_html .= '<img title="Enabled" alt="Enabled" src="../img/admin/enabled.gif">';
            else :
                $this->_html .= '<img title="Disabled" alt="Disabled" src="../img/admin/disabled.gif">';
            endif;
           
            $this->_html .= '</td> 
                       
                            <td class="pointer dragHandle">
                                    <a' . (($tdsliderdata['position'] == (sizeof($tdsliderdata) - 1) OR sizeof($tdsliderdata) == 1) ? ' style="display: none;"' : '') . ' href="' . $currentIndex . '&configure=tdrevolutionslider&id_tdrevolutionslider=' . $tdsliderdata['id_tdrevolutionslider'] . '&token=' . Tools::getAdminTokenLite('AdminModules') . '">
                                    <img src="../img/admin/down.gif" alt="' . $this->l('Down') . '" title="' . $this->l('Down') . '" /></a>
                                    <a' . ($tdsliderdata['position'] == 0 ? ' style="display: none;"' : '') . ' href="' . $currentIndex . '&configure=tdrevolutionslider&id_tdrevolutionslider=' . $tdsliderdata['id_tdrevolutionslider'] . '&token=' . Tools::getAdminTokenLite('AdminModules') . '">
                                    <img src="../img/admin/up.gif" alt="' . $this->l('Up') . '" title="' . $this->l('Up') . '" /></a>
                            </td>
                              <td width="10%" class="center">
                            
                                        <a href="' . $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&updateSlider&id_tdrevolutionslider=' . (int) ($tdsliderdata['id_tdrevolutionslider']) . '" title="' . $this->l('Edit') . '"><img src="' . _PS_ADMIN_IMG_ . 'edit.gif" alt="" /></a> 
                                        <a href="' . $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&deleteSlider&id_tdrevolutionslider=' . (int) ($tdsliderdata['id_tdrevolutionslider']) . '" title="' . $this->l('Delete') . '"><img src="' . _PS_ADMIN_IMG_ . 'delete.gif" alt="" /></a>
					
                                </td>
                        </tr>';
            $i++;
        endforeach;



        $this->_html .= '</table>';
    }

    function hookHome($params) {
        global $smarty;
             $this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
            $tdslider = Db::getInstance()->ExecuteS('
            SELECT td.`id_tdrevolutionslider`, td.`slider_link`, td.`active`, td1.`image_url`, td.`position`,td1.image_title, td1.`slider_content`
            FROM `' . _DB_PREFIX_ . 'tdrevolutionslider` td
            INNER JOIN `' . _DB_PREFIX_ . 'tdrevolutionslider_lang` td1 ON (td.`id_tdrevolutionslider` = td1.`id_tdrevolutionslider`)
            WHERE td1.`id_lang` = ' . (int) $params['cookie']->id_lang . ' 
                AND td.id_shop = '.(int)$id_shop.'
            ORDER BY td.`position`');
            $data = array();
            foreach ($tdslider as $slider):
                if ($slider['active'])
                    $data[] = $slider;
            endforeach;
            //print_r($tdslider); 
            $smarty->assign(array(
                'default_lang' => (int) $params['cookie']->id_lang,
                'id_lang' => (int) $params['cookie']->id_lang,
                'tdrevolutionslider' => $data,
                'base_url' => __PS_BASE_URI__
            ));
            return $this->display(__FILE__, 'tdrevolutionslider.tpl');

    }
}