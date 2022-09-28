<?php

if(!defined('_PS_VERSION_')) exit;

if(!isset($GLOBALS['magictoolbox'])) {
    $GLOBALS['magictoolbox'] = array();
    $GLOBALS['magictoolbox']['filters'] = array();
    $GLOBALS['magictoolbox']['isProductScriptIncluded'] = false;
    $GLOBALS['magictoolbox']['standardTool'] = '';
    $GLOBALS['magictoolbox']['selectorImageType'] = '';
}

if(!isset($GLOBALS['magictoolbox']['magicscroll'])) {
    $GLOBALS['magictoolbox']['magicscroll'] = array();
    $GLOBALS['magictoolbox']['magicscroll']['headers'] = false;
}

class MagicScroll extends Module {

    //Prestahop v1.5 or above
    public $isPrestahop15x = false;

    //Prestahop v1.6 or above
    public $isPrestahop16x = false;

    //Smarty v3 template engine
    public $isSmarty3 = false;

    //Smarty 'getTemplateVars' function name
    public $getTemplateVars = 'getTemplateVars';

    //Suffix was added to default images types since version 1.5.1.0
    public $imageTypeSuffix = '';

    public function __construct() {

        $this->name = 'magicscroll';
        $this->tab = 'Tools';
        $this->version = '5.5.14';
        $this->author = 'Magic Toolbox';


        $this->module_key = '0da9dca768b05e93d1cde8b495070296';

        parent::__construct();

        $this->displayName = 'Magic Scroll';
        $this->description = "Effortlessly scroll through images and/or text on your web pages.";

        $this->confirmUninstall = 'All magicscroll settings would be deleted. Do you really want to uninstall this module ?';

        $this->isPrestahop15x = version_compare(_PS_VERSION_, '1.5', '>=');
        $this->isPrestahop16x = version_compare(_PS_VERSION_, '1.6', '>=');

        $this->isSmarty3 = $this->isPrestahop15x || Configuration::get('PS_FORCE_SMARTY_2') === "0";
        if($this->isSmarty3) {
            //Smarty v3 template engine
            $this->getTemplateVars = 'getTemplateVars';
        } else {
            //Smarty v2 template engine
            $this->getTemplateVars = 'get_template_vars';
        }

        $this->imageTypeSuffix = version_compare(_PS_VERSION_, '1.5.1.0', '>=') ? '_default' : '';

    }

    public function install() {
        $headerHookID = $this->isPrestahop15x ? Hook::getIdByName('displayHeader') : Hook::get('header');
        if(   !parent::install()
           OR !$this->registerHook($this->isPrestahop15x ? 'displayHeader' : 'header')
           OR !$this->registerHook($this->isPrestahop15x ? 'displayFooterProduct' : 'productFooter')
           OR !$this->registerHook($this->isPrestahop15x ? 'displayFooter' : 'footer')
           OR !$this->installDB()
           OR !$this->fixCSS()
           OR !$this->updatePosition($headerHookID, 0, 1)
          )
          return false;

        $this->sendStat('install');

        return true;
    }

    private function installDB() {
        if(!Db::getInstance()->Execute('CREATE TABLE `'._DB_PREFIX_.'magicscroll_settings` (
                                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                        `block` VARCHAR(32) NOT NULL,
                                        `name` VARCHAR(32) NOT NULL,
                                        `value` TEXT,
                                        `enabled` INT(2) UNSIGNED NOT NULL,
                                        PRIMARY KEY (`id`)
                                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'
                                      )
            OR !$this->fillDB()
            OR !$this->fixDefaultValues()
          ) return false;

        return true;
    }

    private function fixCSS() {

        //fix url's in css files
        $fileContents = file_get_contents(dirname(__FILE__).'/magicscroll.css');
        $toolPath = _MODULE_DIR_.'magicscroll';
        $pattern = '/url\(\s*(?:\'|")?(?!'.preg_quote($toolPath, '/').')\/?([^\)\s]+?)(?:\'|")?\s*\)/is';
        $replace = 'url('.$toolPath.'/$1)';
        $fixedFileContents = preg_replace($pattern, $replace, $fileContents);
        if($fixedFileContents != $fileContents) {
            //file_put_contents(dirname(__FILE__).'/magicscroll.css', $fixedFileContents);
            $fp = fopen(dirname(__FILE__).'/magicscroll.css', 'w+');
            if($fp) {
                fwrite($fp, $fixedFileContents);
                fclose($fp);
            }
        }

        return true;
    }

    private function sendStat($action = '') {

        //NOTE: don't send from working copy
        if('working' == 'v5.5.14' || 'working' == 'v1.0.29') {
            return;
        }

        $hostname = 'www.magictoolbox.com';
        $url = $_SERVER['HTTP_HOST'].preg_replace('/\/$/i', '', __PS_BASE_URI__);
        $url = urlencode(urldecode($url));
        $platformVersion = defined('_PS_VERSION_') ? _PS_VERSION_ : '';
        $path = "api/stat/?action={$action}&tool_name=magicscroll&license=trial&tool_version=v1.0.29&module_version=v5.5.14&platform_name=prestashop&platform_version={$platformVersion}&url={$url}";
        $handle = @fsockopen($hostname, 80, $errno, $errstr, 30);
        if($handle) {
            $headers  = "GET /{$path} HTTP/1.1\r\n";
            $headers .= "Host: {$hostname}\r\n";
            $headers .= "Connection: Close\r\n\r\n";
            fwrite($handle, $headers);
            fclose($handle);
        }

    }

    public function fixDefaultValues() {
        $result = true;
        if(version_compare(_PS_VERSION_, '1.5.1.0', '>=')) {
            $sql = 'UPDATE `'._DB_PREFIX_.'magicscroll_settings` SET `value`=CONCAT(value, \'_default\') WHERE `name`=\'thumb-image\' OR `name`=\'selector-image\' OR `name`=\'large-image\'';
            $result = Db::getInstance()->Execute($sql);
        }
        if($this->isPrestahop16x) {
            $sql = 'UPDATE `'._DB_PREFIX_.'magicscroll_settings` SET `value`=\'home_default\', `enabled`=1 WHERE `name`=\'thumb-image\' AND (`block`=\'homefeatured\' OR `block`=\'blocknewproducts_home\' OR `block`=\'blockbestsellers_home\')';
            $result = Db::getInstance()->Execute($sql);
            $sql = 'UPDATE `'._DB_PREFIX_.'magicscroll_settings` SET `value`=\'0\', `enabled`=1 WHERE `name`=\'width\' AND `block`=\'homefeatured\'';
            $result = Db::getInstance()->Execute($sql);
        }
        return $result;
    }

    public function uninstall() {
        //NOTE: spike to clear cache for 'homefeatured.tpl'
        if(version_compare(_PS_VERSION_, '1.5.5.0', '>=')) {
            $this->name = 'homefeatured';
            $this->_clearCache('homefeatured.tpl');
            $this->name = 'magicscroll';
        }
        if(!parent::uninstall() OR !$this->uninstallDB()) return false;
        $this->sendStat('uninstall');
        return true;
    }

    private function uninstallDB() {
        return  Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'magicscroll_settings`;')
                ;
    }

    public function disable($forceAll = false) {
        //NOTE: spike to clear cache for 'homefeatured.tpl'
        if(version_compare(_PS_VERSION_, '1.5.5.0', '>=')) {
            $this->name = 'homefeatured';
            $this->_clearCache('homefeatured.tpl');
            $this->name = 'magicscroll';
        }
        return parent::disable($forceAll);
    }

    public function enable($forceAll = false) {
        //NOTE: spike to clear cache for 'homefeatured.tpl'
        if(version_compare(_PS_VERSION_, '1.5.5.0', '>=')) {
            $this->name = 'homefeatured';
            $this->_clearCache('homefeatured.tpl');
            $this->name = 'magicscroll';
        }
        return parent::enable($forceAll);
    }

    public function getImagesTypes() {
        if(!isset($GLOBALS['magictoolbox']['imagesTypes'])) {
            $GLOBALS['magictoolbox']['imagesTypes'] = array('original');
            // get image type values
            $sql = 'SELECT name FROM `'._DB_PREFIX_.'image_type` ORDER BY `id_image_type` ASC';
            $result = Db::getInstance()->ExecuteS($sql);
            foreach($result as $row) {
                $GLOBALS['magictoolbox']['imagesTypes'][] = $row['name'];
            }
        }
        return $GLOBALS['magictoolbox']['imagesTypes'];
    }

    public function getContent() {

        $tool = $this->loadTool();
        $paramsMap = $this->getParamsMap();

        $_imagesTypes = array(
            'thumb'
        );

        foreach($_imagesTypes as $name) {
            foreach($this->getBlocks() as $blockId => $blockLabel) {
                if($tool->params->paramExists($name.'-image', $blockId)) {
                    $tool->params->setValues($name.'-image', $this->getImagesTypes(), $blockId);
                }
            }
        }

        $magicSubmit = Tools::getValue('magic_submit', '');
        if(!empty($magicSubmit)) {
            // save settings
            if($magicSubmit == $this->l('Save settings')) {
                foreach($paramsMap as $blockId => $groups) {
                    foreach($groups as $group) {
                        foreach($group as $param) {
                            if(Tools::getValue($blockId.'-'.$param, null) !== null) {
                                $valueToSave = $value = trim(Tools::getValue($blockId.'-'.$param, ''));
                                //switch($tool->params->params[$param]['type']) {
                                switch($tool->params->getType($param)) {
                                    case "num":
                                        $valueToSave = $value = intval($value);
                                        break;
                                    case "array":
                                        if(!in_array($value, $tool->params->getValues($param))) $valueToSave = $value = $tool->params->getDefaultValue($param);
                                        break;
                                    case "text":
                                        $valueToSave = pSQL($value);
                                        break;
                                }
                                Db::getInstance()->Execute(
                                    'UPDATE `'._DB_PREFIX_.'magicscroll_settings` SET `value`=\''.$valueToSave.'\', `enabled`=1 WHERE `block`=\''.$blockId.'\' AND `name`=\''.$param.'\''
                                );
                                $tool->params->setValue($param, $value, $blockId);
                            } else {
                                Db::getInstance()->Execute(
                                    'UPDATE `'._DB_PREFIX_.'magicscroll_settings` SET `enabled`=0 WHERE `block`=\''.$blockId.'\' AND `name`=\''.$param.'\''
                                );
                                if($tool->params->paramExists($param, $blockId)) {
                                    $tool->params->removeParam($param, $blockId);
                                };
                            }
                        }
                    }
                }
                //NOTE: spike to clear cache for 'homefeatured.tpl'
                if(version_compare(_PS_VERSION_, '1.5.5.0', '>=')) {
                    $this->name = 'homefeatured';
                    $this->_clearCache('homefeatured.tpl');
                    $this->name = 'magicscroll';
                }
            }
        }

        //change subtype for some params to display them like radio
        foreach($tool->params->getParams() as $id => $param) {
            if($tool->params->getSubType($id) == 'select' && count($tool->params->getValues($id)) < 6)
                $tool->params->setSubType($id, 'radio');
        }

        // display params
        ob_start();
        include(dirname(__FILE__).'/magicscroll.settings.tpl.php');
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public function loadTool($profile = false, $force = false) {
        if(!isset($GLOBALS['magictoolbox']['magicscroll']['class']) || $force) {
            require_once(dirname(__FILE__).'/magicscroll.module.core.class.php');
            $GLOBALS['magictoolbox']['magicscroll']['class'] = new MagicScrollModuleCoreClass();
            $tool = &$GLOBALS['magictoolbox']['magicscroll']['class'];
            // load current params
            $sql = 'SELECT `name`, `value`, `block` FROM `'._DB_PREFIX_.'magicscroll_settings` WHERE `enabled`=1';
            $result = Db::getInstance()->ExecuteS($sql);
            foreach($result as $row) {
                $tool->params->setValue($row['name'], $row['value'], $row['block']);
            }
            // load translates
            $GLOBALS['magictoolbox']['magicscroll']['translates'] = $this->getMessages();
            foreach($this->getBlocks() as $block => $label) {
                // prepare image types
                foreach(array('large', 'selector', 'thumb') as $name) {
                    if($tool->params->checkValue($name.'-image', 'original', $block)) {
                        $tool->params->setValue($name.'-image', false, $block);
                    }
                }
            }

            if($tool->type == 'standard' && $tool->params->checkValue('magicscroll', 'yes', 'product')) {
                require_once(dirname(__FILE__).'/magicscroll.module.core.class.php');
                $GLOBALS['magictoolbox']['magicscroll']['magicscroll'] = new MagicScrollModuleCoreClass();
                $scroll = &$GLOBALS['magictoolbox']['magicscroll']['magicscroll'];
                $scroll->params->setScope('MagicScroll');
                $scroll->params->appendParams($tool->params->getParams('product'));//!!!!!!!!!!!!!
                $scroll->params->setValue('direction', $scroll->params->checkValue('template', array('left', 'right')) ? 'bottom' : 'right');
            }

        }

        $tool = &$GLOBALS['magictoolbox']['magicscroll']['class'];

        if($profile) {
            $tool->params->setProfile($profile);
        }

        return $tool;

    }

    public function hookHeader($params) {
        global $smarty;

        if(!$this->isPrestahop15x) {
            ob_start();
        }

        $headers = '';
        $tool = $this->loadTool();
        $tool->params->resetProfile();

        $page = $smarty->{$this->getTemplateVars}('page_name');
        switch($page) {
            case 'product':
            case 'index':
            case 'category':
            case 'manufacturer':
            case 'search':
                break;
            case 'best-sales':
                $page = 'bestsellerspage';
                break;
            case 'new-products':
                $page = 'newproductpage';
                break;
            case 'prices-drop':
                $page = 'specialspage';
                break;
            default:
                $page = '';
        }
        //old check if(preg_match('/\/prices-drop.php$/is', $GLOBALS['_SERVER']['SCRIPT_NAME']))

        if($tool->params->checkValue('include-headers-on-all-pages', 'Yes', 'default') && ($GLOBALS['magictoolbox']['magicscroll']['headers'] = true)
           || $tool->params->profileExists($page) && !$tool->params->checkValue('enable-effect', 'No', $page)
           || $page == 'index' && !$tool->params->checkValue('enable-effect', 'No', 'homefeatured') && parent::isInstalled('homefeatured') && parent::getInstanceByName('homefeatured')->active
           || $page == 'index' && !$tool->params->checkValue('enable-effect', 'No', 'blocknewproducts_home') && parent::isInstalled('blocknewproducts') && parent::getInstanceByName('blocknewproducts')->active
           || $page == 'index' && !$tool->params->checkValue('enable-effect', 'No', 'blockbestsellers_home') && parent::isInstalled('blockbestsellers') && parent::getInstanceByName('blockbestsellers')->active
           || !$tool->params->checkValue('enable-effect', 'No', 'blockviewed') && parent::isInstalled('blockviewed') && parent::getInstanceByName('blockviewed')->active
           || !$tool->params->checkValue('enable-effect', 'No', 'blockspecials') && parent::isInstalled('blockspecials') && parent::getInstanceByName('blockspecials')->active
           || (!$tool->params->checkValue('enable-effect', 'No', 'blocknewproducts') || ($page == 'index' && !$tool->params->checkValue('enable-effect', 'No', 'blocknewproducts_home'))) && parent::isInstalled('blocknewproducts') && parent::getInstanceByName('blocknewproducts')->active
           || (!$tool->params->checkValue('enable-effect', 'No', 'blockbestsellers') || ($page == 'index' && !$tool->params->checkValue('enable-effect', 'No', 'blockbestsellers_home'))) && parent::isInstalled('blockbestsellers') && parent::getInstanceByName('blockbestsellers')->active
          ) {
            // include headers
            $headers = $tool->getHeadersTemplate(_MODULE_DIR_.'magicscroll');
            $headers .= '<script type="text/javascript" src="'._MODULE_DIR_.'magicscroll/common.js"></script>';
            if($page == 'product' && !$tool->params->checkValue('enable-effect', 'No', 'product')) {
                $headers .= '
<script type="text/javascript">
</script>';
                if(!$GLOBALS['magictoolbox']['isProductScriptIncluded']) {
                    $headers .= '<script type="text/javascript" src="'._MODULE_DIR_.'magicscroll/product.js"></script>';
                    $GLOBALS['magictoolbox']['isProductScriptIncluded'] = true;
                }
                //<style type="text/css"></style>';
            }
            if($page == 'index') {
                $headers .= '
<script type="text/javascript">
    var isPrestahop16x = '.($this->isPrestahop16x ? 'true' : 'false').';
    $(document).ready(function() {
        if(isPrestahop16x && typeof(MagicScroll) != \'undefined\') {
            var tabsToInit = {};
            $(\'#home-page-tabs li:not(li.active) a\').each(function(index) {
                tabsToInit[this.href.replace(/^.*?#([^#]+)$/, \'$1\')] = index;
            });
            $(\'#home-page-tabs a[data-toggle="tab"]\').on(\'shown.bs.tab\', function (e) {
                var key = e.target.href.replace(/^.*?#([^#]+)$/, \'$1\');
                if(typeof(tabsToInit[key]) != \'undefined\') {
                    var scrollEl = $(\'div#\'+key+\' .MagicScroll[id]\').get(0);
                    MagicScroll.refresh(scrollEl.id);
                    delete tabsToInit[key];    
                }
            });
        }
    });
</script>
';
            }
            /*
                Commented as discussion in issue #0021547
            */
            /*
            $headers .= '
            <!--[if !(IE 8)]>
            <style type="text/css">
                #center_column, #left_column, #right_column {overflow: hidden !important;}
            </style>
            <![endif]-->
            ';*/

            if($this->isSmarty3) {
                //Smarty v3 template engine
                $smarty->registerFilter('output', array(Module::getInstanceByName('magicscroll'), 'parseTemplateCategory'));
            } else {
                //Smarty v2 template engine
                $smarty->register_outputfilter(array(Module::getInstanceByName('magicscroll'), 'parseTemplateCategory'));
            }
            $GLOBALS['magictoolbox']['filters']['magicscroll'] = 'parseTemplateCategory';

            // presta create new class every time when hook called
            // so we need save our data in the GLOBALS
            $GLOBALS['magictoolbox']['magicscroll']['cookie'] = $params['cookie'];
            $GLOBALS['magictoolbox']['magicscroll']['productsViewed'] = (isset($params['cookie']->viewed) AND !empty($params['cookie']->viewed)) ? explode(',', $params['cookie']->viewed) : array();

            $headers = '<!-- MAGICSCROLL HEADERS START -->'.$headers.'<!-- MAGICSCROLL HEADERS END -->';

        }

        return $headers;

    }

    public function hookProductFooter($params) {
        //we need save this data in the GLOBALS for compatible with some Prestashop module which reset the $product smarty variable
        $GLOBALS['magictoolbox']['magicscroll']['product'] = array('id' => $params['product']->id, 'name' => $params['product']->name, 'link_rewrite' => $params['product']->link_rewrite);
        return '';
    }

    public function hookFooter($params) {

        if(!$this->isPrestahop15x) {

            $contents = ob_get_contents();
            ob_end_clean();


            if($GLOBALS['magictoolbox']['magicscroll']['headers'] == false) {
                $contents = preg_replace('/<\!-- MAGICSCROLL HEADERS START -->.*?<\!-- MAGICSCROLL HEADERS END -->/is', '', $contents);
            } else {
                $contents = preg_replace('/<\!-- MAGICSCROLL HEADERS (START|END) -->/is', '', $contents);
            }

            echo $contents;

        }

        return '';

    }


    private static $outputMatches = array();

    public function prepareOutput($output, $index = 'DEFAULT') {

        if(!isset(self::$outputMatches[$index])) {
            preg_match_all('/<div [^>]*?class="[^"]*?MagicToolboxContainer[^"]*?".*?<\/div>\s/is', $output, self::$outputMatches[$index]);
            foreach(self::$outputMatches[$index][0] as $key => $match) {
                $output = str_replace($match, 'MAGICSCROLL_MATCH_'.$index.'_'.$key.'_', $output);
            }
        } else {
            foreach(self::$outputMatches[$index][0] as $key => $match) {
                $output = str_replace('MAGICSCROLL_MATCH_'.$index.'_'.$key.'_', $match, $output);
            }
            unset(self::$outputMatches[$index]);
        }
        return $output;

    }


    public function parseTemplateCategory($output, $smarty) {
        if($this->isSmarty3) {
            //Smarty v3 template engine
            //$currentTemplate = substr(basename($smarty->_current_file), 0, -4);
            $currentTemplate = substr(basename($smarty->template_resource), 0, -4);
            if($currentTemplate == 'breadcrumb') {
                $currentTemplate = 'product';
            } elseif($currentTemplate == 'pagination') {
                $currentTemplate = 'category';
            }
        } else {
            //Smarty v2 template engine
            $currentTemplate = $smarty->currentTemplate;
        }

        if($this->isPrestahop15x && $currentTemplate == 'layout') {


            if(version_compare(_PS_VERSION_, '1.5.5.0', '>=')) {
                //NOTE: because we do not know whether the effect is applied to the blocks in the cache
                $GLOBALS['magictoolbox']['magicscroll']['headers'] = true;
            }
            //NOTE: full contents in prestashop 1.5.x
            if($GLOBALS['magictoolbox']['magicscroll']['headers'] == false) {
                $output = preg_replace('/<\!-- MAGICSCROLL HEADERS START -->.*?<\!-- MAGICSCROLL HEADERS END -->/is', '', $output);
            } else {
                $output = preg_replace('/<\!-- MAGICSCROLL HEADERS (START|END) -->/is', '', $output);
            }
            return $output;
        }

        switch($currentTemplate) {
            case 'search':
            case 'manufacturer':
                //$currentTemplate = 'manufacturer';
                break;
            case 'best-sales':
                $currentTemplate = 'bestsellerspage';
                break;
            case 'new-products':
                $currentTemplate = 'newproductpage';
                break;
            case 'prices-drop':
                $currentTemplate = 'specialspage';
                break;
            case 'blockbestsellers-home':
                $currentTemplate = 'blockbestsellers_home';
                break;
            case 'product-list'://for 'Layered navigation block'
                if(strpos($_SERVER['REQUEST_URI'], 'blocklayered-ajax.php') !== false) {
                    $currentTemplate = 'category';
                }
                break;
        }

        $tool = $this->loadTool();
        if(!$tool->params->profileExists($currentTemplate) || $tool->params->checkValue('enable-effect', 'No', $currentTemplate)) {
            return $output;
        }
        $tool->params->setProfile($currentTemplate);

        global $link;
        $cookie = &$GLOBALS['magictoolbox']['magicscroll']['cookie'];
        if(method_exists($link, 'getImageLink')) {
            $_link = &$link;
        } else {
            //for Prestashop ver 1.1
            $_link = &$this;
        }

        $output = self::prepareOutput($output);

        switch($currentTemplate) {
            case 'homefeatured':
                $categoryID = $this->isPrestahop15x ? Context::getContext()->shop->getCategory() : 1;
                $category = new Category($categoryID);
                $nb = intval(Configuration::get('HOME_FEATURED_NBR'));//Number of product displayed
                $products = $category->getProducts(intval($cookie->id_lang), 1, ($nb ? $nb : 10));
                if(!is_array($products)) break;
                $pCount = count($products);
                if(!$pCount) break;
                $GLOBALS['magictoolbox']['magicscroll']['headers'] = true;
                if($pCount < $tool->params->getValue('items')) {
                    $tool->params->setValue('items', $pCount);
                }
                $productImagesData = array();
                $useLink = $tool->params->checkValue('link-to-product-page', 'Yes');
                foreach($products as $p_key => $product) {
                    $productImagesData[$p_key]['link'] = $useLink?$link->getProductLink($product['id_product'], $product['link_rewrite'], isset($product['category']) ? $product['category'] : null):'';
                    $productImagesData[$p_key]['title'] = $product['name'];
                    $productImagesData[$p_key]['img'] = $_link->getImageLink($product['link_rewrite'], $product['id_image'], $tool->params->getValue('thumb-image'));
                }
                $magicscroll = $tool->getMainTemplate($productImagesData, array("id" => "homefeaturedMagicScroll"));
                if($this->isPrestahop16x) {
                    $magicscroll = '<div id="homefeatured" class="MagicToolboxContainer homefeatured tab-pane">'.$magicscroll.'</div>';
                }
                $pattern = '<ul[^>]*?>.*?<\/ul>';
                $output = preg_replace('/'.$pattern.'/is', $magicscroll, $output);
                break;
            case 'category':
            case 'manufacturer':
            case 'newproductpage':
            case 'bestsellerspage':
            case 'specialspage':
            case 'search':
                $products = $smarty->{$this->getTemplateVars}('products');
                if(!is_array($products)) break;
                $pCount = count($products);
                if(!$pCount) break;
                $GLOBALS['magictoolbox']['magicscroll']['headers'] = true;
                if($pCount < $tool->params->getValue('items')) {
                    $tool->params->setValue('items', $pCount);
                }
                $magicscroll = array();
                $left_block_pattern = '(?:<div[^>]*?class="left_block"[^>]*>[^<]*'.
                                        '((?:<p[^>]*?class="compare"[^>]*>[^<]*'.
                                        '<input[^>]*>[^<]*'.
                                        '<label[^>]*>[^<]*<\/label>[^<]*'.
                                        '<\/p>[^<]*)?)'.
                                        '<\/div>[^<]*)?';
                foreach($products as $product) {
                    $lrw = $product['link_rewrite'];
                    $img = $_link->getImageLink($lrw, $product['id_image'], 'home'.$this->imageTypeSuffix);
                    $pattern = '/<li[^>]*?class="[^"]*?ajax_block_product[^"]*"[^>]*>[^<]*'.$left_block_pattern.'<div[^>]*?class="center_block"[^>]*>((?:[^<]*<span[^>]*>[^<]*<\/span>)?[^<]*<a[^>]*>[^<]*<img[^>]*?src="'.preg_quote($img, '/').'"[^>]*>[^<]*(?:<span[^>]*?class="new"[^>]*>.*?<\/span>[^<]*)?<\/a>.*?)<\/div>[^<]*<div[^>]*?class="right_block"[^>]*>(.*?)<\/div>(?:[^<]*<br[^>]*>)?[^<]*<\/li>/is';
                    $matches = array();
                    if(preg_match($pattern, $output, $matches)) {
                        $left_block = !empty($matches[1]) ? '<div class="left_block">'.$matches[1].'</div>' : '';
                        $magicscroll[] = '<div>'.$left_block.$matches[2].'<div class="bottom_block">'.$matches[3].'</div></div>';
                    }
                }
                if(!empty($magicscroll)) {
                    $tool->params->setValue('item-tag', 'div');
                    $options = $tool->getOptionsTemplate('categoryMagicScroll');
                    $additionalClass = '';
                    if($tool->params->checkValue('scroll-style', 'with-borders')) {
                        $additionalClass = ' msborder';
                    }
                    if($this->isPrestahop15x) {
                        $additionalClass .= ' prestahop15x';
                    } else {
                        $additionalClass .= ' prestahop14x';
                    }
                    $magicscroll = $options.'<div id="categoryMagicScroll" class="MagicScroll'.$additionalClass.'">'.implode('', $magicscroll).'</div>';
                    $magicscroll = strtr($magicscroll, array('\\' => '\\\\', '$' => '\$'));
                    $output = preg_replace('/<ul[^>]*?id="product_list"[^>]*>.*?<\/ul>/is', $magicscroll, $output);
                    $tool->params->setValue('item-tag', 'a');
                }
                break;
            case 'product':
                if(!isset($GLOBALS['magictoolbox']['magicscroll']['product'])) {
                    //for skip loyalty module product.tpl
                    break;
                }

                $images = $smarty->{$this->getTemplateVars}('images');
                $pCount = count($images);
                if(!$pCount) break;
                if($pCount < $tool->params->getValue('items')) {
                    $tool->params->setValue('items', $pCount);
                }

                //$product = $smarty->tpl_vars['product'];
                //get some data from $GLOBALS for compatible with Prestashop modules which reset the $product smarty variable
                $product = &$GLOBALS['magictoolbox']['magicscroll']['product'];

                $cover = $smarty->{$this->getTemplateVars}('cover');
                if(!isset($cover['id_image'])) {
                    break;
                }
                $coverImageIds = is_numeric($cover['id_image']) ? $product['id'].'-'.$cover['id_image'] : $cover['id_image'];


                $productImagesData = array();
                $ids = array();
                foreach($images as $image) {
                    $id_image = intval($image['id_image']);
                    $ids[] = $id_image;
                    //if($image['cover']) $coverID = $id_image;
                    $productImagesData[$id_image]['title'] = /*$product['name']*/$image['legend'];
                    $productImagesData[$id_image]['img'] = $_link->getImageLink($product['link_rewrite'], intval($product['id']).'-'.$id_image, $tool->params->getValue('thumb-image'));
                }

                $GLOBALS['magictoolbox']['magicscroll']['headers'] = true;

                $magicscroll = $tool->getMainTemplate($productImagesData, array("id" => "productMagicScroll"));

                $magicscroll .= '<script type="text/javascript">magictoolboxImagesOrder = ['.implode(',', $ids).'];</script>';

                //need img#bigpic for blockcart module
                $magicscroll = '<div style="width:0px;height:0px;overflow:hidden;visibility:hidden;"><img id="bigpic" src="'.$productImagesData[$ids[0]]['img'].'" /></div>'.$magicscroll;

                /*
                $imagePatternTemplate = '<img [^>]*?src="[^"]*?__SRC__"[^>]*>';
                $patternTemplate = '<a [^>]*>[^<]*'.$imagePatternTemplate.'[^<]*<\/a>|'.$imagePatternTemplate;
                $patternTemplate = '<span [^>]*?id="view_full_size"[^>]*>[^<]*'.
                                   '(?:<span [^>]*?class="[^"]*"[^>]*>[^<]*<\/span>[^<]*)*'.
                                   '(?:'.$patternTemplate.')[^<]*'.
                                   '(?:<span [^>]*?class="[^"]*?span_link[^"]*"[^>]*>.*?<\/span>[^<]*)*'.
                                   '<\/span>|'.$patternTemplate;
                //NOTE: added support custom theme #53897
                $patternTemplate = $patternTemplate.'|'.
                    '<div [^>]*?id="wrap"[^>]*>[^<]*'.
                    '<a [^>]*>[^<]*'.
                    '<span [^>]*?id="view_full_size"[^>]*>[^<]*'.
                    $imagePatternTemplate.'[^<]*'.
                    '<\/span>[^<]*'.
                    '<\/a>[^<]*'.
                    '<\/div>[^<]*'.
                    '<div [^>]*?class="[^"]*?zoom-b[^"]*"[^>]*>[^<]*'.
                    '<a [^>]*>[^<]*<\/a>[^<]*'.
                    '<\/div>';
                //NOTE: added support custom theme #54204
                $patternTemplate = $patternTemplate.'|'.
                    '<span [^>]*?id="view_full_size"[^>]*>[^<]*'.
                    '<a [^>]*>[^<]*'.
                    '<img [^>]*>[^<]*'.
                    $imagePatternTemplate.'[^<]*'.
                    '<span [^>]*?class="[^"]*?mask[^"]*"[^>]*>.*?<\/span>[^<]*'.
                    '<\/a>[^<]*'.
                    '<\/span>[^<]*';

                $patternTemplate = '(?:'.$patternTemplate.')';

                //$patternTemplate = '(<div[^>]*?id="image-block"[^>]*>[^<]*)'.$patternTemplate;//NOTE: we need this to determine the main image
                //NOTE: added support custom theme #53897
                $patternTemplate = '(<div [^>]*?(?:id="image-block"|class="[^"]*?image[^"]*")[^>]*>[^<]*)'.$patternTemplate;

                $srcPattern = preg_quote($_link->getImageLink($product['link_rewrite'], $coverImageIds, 'large'.$this->imageTypeSuffix), '/');
                $pattern = str_replace('__SRC__', $srcPattern, $patternTemplate);

                $replaced = 0;
                $output = preg_replace('/'.$pattern.'/is', '$1'.$magicscroll, $output, -1, $replaced);
                if(!$replaced) {
                    $iTypes = $this->getImagesTypes();
                    foreach($iTypes as $iType) {
                        if($iType != 'large'.$this->imageTypeSuffix) {

                            $srcPattern = preg_quote($_link->getImageLink($product['link_rewrite'], $coverImageIds, $iType), '/');
                            $noImageSrcPattern = preg_quote($img_prod_dir.$lang_iso.'-default-'.$iType.'.jpg', '/');
                            $pattern = str_replace('__SRC__', $srcPattern, $patternTemplate);
                            $output = preg_replace('/'.$pattern.'/is', '$1'.$magicscroll, $output, -1, $replaced);
                            if($replaced) break;
                        }
                    }
                }
                */

                //NOTE: common pattern to match div#image-block tag
                $pattern =  '(<div\b[^>]*?(?:\bid\s*+=\s*+"image-block"|\bclass\s*+=\s*+"[^"]*?\bimage\b[^"]*+")[^>]*+>)'.
                            '('.
                            '(?:'.
                                '[^<]++'.
                                '|'.
                                '<(?!/?div\b|!--)'.
                                '|'.
                                '<!--.*?-->'.
                                '|'.
                                '<div\b[^>]*+>'.
                                    '(?2)'.
                                '</div\s*+>'.
                            ')*+'.
                            ')'.
                            '</div\s*+>';
                //$replaced = 0;
                //preg_match_all('%'.$pattern.'%is', $output, $__matches, PREG_SET_ORDER);
                //NOTE: limit = 1 because pattern can be matched with other products, located below the main product
                $output = preg_replace('%'.$pattern.'%is', '$1'.$magicscroll.'</div>', $output, 1/*, $replaced*/);

                //remove selectors
                //$output = preg_replace('/<div [^>]*?id="thumbs_list"[^>]*>.*?<\/div>/is', '', $output);
                //NOTE: added support custom theme #53897
                $output = preg_replace('/<div [^>]*?(?:id="thumbs_list"|class="[^"]*?image-additional[^"]*")[^>]*>.*?<\/div>/is', '', $output);

                //NOTE: div#views_block is parent for div#thumbs_list
                $output = preg_replace('/<div [^>]*?id="views_block"[^>]*>.*?<\/div>/is', '', $output);

                //#resetImages link
                $output = preg_replace('/<\!-- thumbnails -->[^<]*<p[^>]*><a[^>]+reset[^>]+>.*?<\/a><\/p>/is', '<!-- thumbnails -->', $output);
                //remove "View full size" link
                $output = preg_replace('/<li>[^<]*<span[^>]*?id="view_full_size"[^>]*?>[^<]*<\/span>[^<]*<\/li>/is', '', $output);
                //remove "Display all pictures" link
                $output = preg_replace('/<p[^>]*>[^<]*<span[^>]*?id="wrapResetImages"[^>]*>.*?<\/span>[^<]*<\/p>/is', '', $output);
                break;
            case 'blockspecials':
                if(version_compare(_PS_VERSION_, '1.4', '<')) {
                    $products = $this->getAllSpecial(intval($cookie->id_lang));
                } else {
                    $products = Product::getPricesDrop((int)($cookie->id_lang), 0, 10, false, 'position', 'asc');
                }
                if(!is_array($products)) break;
                $pCount = count($products);
                if(!$pCount) break;
                $GLOBALS['magictoolbox']['magicscroll']['headers'] = true;
                if($pCount < $tool->params->getValue('items')) {
                    $tool->params->setValue('items', $pCount);
                }
                $productImagesData = array();
                $useLink = $tool->params->checkValue('link-to-product-page', 'Yes');

                foreach($products as $p_key => $product) {
                    if($useLink && (!Tools::getValue('id_product', false) || (Tools::getValue('id_product', false) != $product['id_product']))) {
                        $productImagesData[$p_key]['link'] = $link->getProductLink($product['id_product'], $product['link_rewrite'], isset($product['category']) ? $product['category'] : null);
                    } else {
                        $productImagesData[$p_key]['link'] = '';
                    }
                    $productImagesData[$p_key]['title'] = $product['name'];
                    $productImagesData[$p_key]['img'] = $_link->getImageLink($product['link_rewrite'], $product['id_image'], $tool->params->getValue('thumb-image'));
                }

                $magicscroll = $tool->getMainTemplate($productImagesData, array("id" => "blockspecialsMagicScroll"));
                $pattern = '<ul[^>]*?>.*?<\/ul>';
                $output = preg_replace('/'.$pattern.'/is', $magicscroll, $output);
                break;
            case 'blockviewed':
                $productsViewed = $GLOBALS['magictoolbox']['magicscroll']['productsViewed'];
                $pCount = count($productsViewed);
                if(!$pCount) break;
                $GLOBALS['magictoolbox']['magicscroll']['headers'] = true;
                if($pCount < $tool->params->getValue('items')) {
                    $tool->params->setValue('items', $pCount);
                }
                $productImagesData = array();
                $useLink = $tool->params->checkValue('link-to-product-page', 'Yes');

                foreach($productsViewed as $id_product) {
                    $productViewedObj = new Product(intval($id_product), false, intval($cookie->id_lang));
                    if (!Validate::isLoadedObject($productViewedObj) OR !$productViewedObj->active)
                        continue;
                    else {
                        $images = $productViewedObj->getImages(intval($cookie->id_lang));
                        foreach($images as $image) {
                            if($image['cover']) {
                                $productViewedObj->cover = $productViewedObj->id.'-'.$image['id_image'];
                                $productViewedObj->legend = $image['legend'];
                                break;
                            }
                        }
                        if(!isset($productViewedObj->cover)) {
                            $productViewedObj->cover = Language::getIsoById($cookie->id_lang).'-default';
                            $productViewedObj->legend = '';
                        }
                        $lrw = $productViewedObj->link_rewrite;
                        if($useLink && (!Tools::getValue('id_product', false) || (Tools::getValue('id_product', false) != $id_product))) {
                            $productImagesData[$id_product]['link'] = $link->getProductLink($id_product, $lrw, $productViewedObj->category);
                        } else {
                            $productImagesData[$id_product]['link'] = '';
                        }
                        $productImagesData[$id_product]['title'] = $productViewedObj->name;
                        $productImagesData[$id_product]['img'] = $_link->getImageLink($lrw, $productViewedObj->cover, $tool->params->getValue('thumb-image'));
                    }
                }
                $magicscroll = $tool->getMainTemplate($productImagesData, array("id" => "blockviewedMagicScroll"));
                $pattern = '<ul[^>]*?>.*?<\/ul>';
                $output = preg_replace('/'.$pattern.'/is', $magicscroll, $output);
                break;
            case 'blockbestsellers':
            case 'blockbestsellers_home':
            case 'blocknewproducts':
            case 'blocknewproducts_home':
                if(in_array($currentTemplate, array('blockbestsellers', 'blockbestsellers_home'))) {
                    $nb_products = $tool->params->getValue('max-number-of-products', $currentTemplate);
                    //$products = $smarty->{$this->getTemplateVars}('best_sellers');
                    //to get with description etc.
                    $products = ProductSale::getBestSales(intval($cookie->id_lang), 0, $nb_products);
                } else {
                    $products = $smarty->{$this->getTemplateVars}('new_products');
                }
                if(!is_array($products)) break;
                $pCount = count($products);
                if(!$pCount || !$products) break;
                $GLOBALS['magictoolbox']['magicscroll']['headers'] = true;
                if($pCount < $tool->params->getValue('items')) {
                    $tool->params->setValue('items', $pCount);
                }
                $productImagesData = array();
                $useLink = $tool->params->checkValue('link-to-product-page', 'Yes');
                foreach($products as $p_key => $product) {
                    if($useLink && (!Tools::getValue('id_product', false) || (Tools::getValue('id_product', false) != $product['id_product']))) {
                        $productImagesData[$p_key]['link'] = $link->getProductLink($product['id_product'], $product['link_rewrite'], isset($product['category']) ? $product['category'] : null);
                    } else {
                        $productImagesData[$p_key]['link'] = '';
                    }
                    $productImagesData[$p_key]['title'] = $product['name'];
                    $productImagesData[$p_key]['img'] = $_link->getImageLink($product['link_rewrite'], $product['id_image'], $tool->params->getValue('thumb-image'));
                }
                $magicscroll = $tool->getMainTemplate($productImagesData, array("id" => $currentTemplate."MagicScroll"));
                if($this->isPrestahop16x) {
                    if($currentTemplate == 'blockbestsellers_home') {
                        $magicscroll = '<div id="blockbestsellers" class="MagicToolboxContainer blockbestsellers tab-pane">'.$magicscroll.'</div>';
                    } else if($currentTemplate == 'blocknewproducts_home') {
                        $magicscroll = '<div id="blocknewproducts" class="MagicToolboxContainer blocknewproducts tab-pane active">'.$magicscroll.'</div>';
                    }
                }
                $pattern = '<ul[^>]*?>.*?<\/ul>';
                $output = preg_replace('/'.$pattern.'/is', $magicscroll, $output);
                break;
        }

        return self::prepareOutput($output);

    }

    public function getAllSpecial($id_lang, $beginning = false, $ending = false) {

        $currentDate = date('Y-m-d');
        $result = Db::getInstance()->ExecuteS('
        SELECT p.*, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, p.`ean13`,
            i.`id_image`, il.`legend`, t.`rate`
        FROM `'._DB_PREFIX_.'product` p
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.intval($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.intval($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'tax` t ON t.`id_tax` = p.`id_tax`
        WHERE (`reduction_price` > 0 OR `reduction_percent` > 0)
        '.((!$beginning AND !$ending) ?
            'AND (`reduction_from` = `reduction_to` OR (`reduction_from` <= \''.$currentDate.'\' AND `reduction_to` >= \''.$currentDate.'\'))'
        :
            ($beginning ? 'AND `reduction_from` <= \''.$beginning.'\'' : '').($ending ? 'AND `reduction_to` >= \''.$ending.'\'' : '')).'
        AND p.`active` = 1
        ORDER BY RAND()');

        if (!$result)
            return false;

        foreach ($result as $row)
            $rows[] = Product::getProductProperties($id_lang, $row);

        return $rows;
    }

    //for Prestashop ver 1.1
    public function getImageLink($name, $ids, $type = null) {
        return _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').'.jpg';
    }


    public function getProductDescription($id_product, $id_lang) {
        $sql = 'SELECT `description` FROM `'._DB_PREFIX_.'product_lang` WHERE `id_product` = '.(int)($id_product).' AND `id_lang` = '.(int)($id_lang);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        return isset($result[0]['description'])? $result[0]['description'] : '';
    }

    function fillDB() {
		$sql = 'INSERT INTO `'._DB_PREFIX_.'magicscroll_settings` (`block`, `name`, `value`, `enabled`) VALUES
				(\'default\', \'thumb-image\', \'large\', 1),
				(\'default\', \'link-to-product-page\', \'Yes\', 1),
				(\'default\', \'include-headers-on-all-pages\', \'No\', 1),
				(\'default\', \'scroll-style\', \'default\', 1),
				(\'default\', \'show-image-title\', \'Yes\', 1),
				(\'default\', \'loop\', \'continue\', 1),
				(\'default\', \'speed\', \'5000\', 1),
				(\'default\', \'width\', \'0\', 1),
				(\'default\', \'height\', \'0\', 1),
				(\'default\', \'item-width\', \'0\', 1),
				(\'default\', \'item-height\', \'0\', 1),
				(\'default\', \'step\', \'3\', 1),
				(\'default\', \'items\', \'3\', 1),
				(\'default\', \'arrows\', \'outside\', 1),
				(\'default\', \'arrows-opacity\', \'60\', 1),
				(\'default\', \'arrows-hover-opacity\', \'100\', 1),
				(\'default\', \'slider-size\', \'10%\', 1),
				(\'default\', \'slider\', \'false\', 1),
				(\'default\', \'direction\', \'right\', 1),
				(\'default\', \'duration\', \'1000\', 1),
				(\'product\', \'thumb-image\', \'large\', 0),
				(\'product\', \'enable-effect\', \'No\', 1),
				(\'product\', \'scroll-style\', \'default\', 0),
				(\'product\', \'show-image-title\', \'Yes\', 0),
				(\'product\', \'loop\', \'continue\', 0),
				(\'product\', \'speed\', \'5000\', 0),
				(\'product\', \'width\', \'0\', 0),
				(\'product\', \'height\', \'0\', 0),
				(\'product\', \'item-width\', \'0\', 0),
				(\'product\', \'item-height\', \'0\', 0),
				(\'product\', \'step\', \'1\', 1),
				(\'product\', \'items\', \'1\', 1),
				(\'product\', \'arrows\', \'inside\', 1),
				(\'product\', \'arrows-opacity\', \'60\', 0),
				(\'product\', \'arrows-hover-opacity\', \'100\', 0),
				(\'product\', \'slider-size\', \'10%\', 0),
				(\'product\', \'slider\', \'false\', 0),
				(\'product\', \'direction\', \'right\', 0),
				(\'product\', \'duration\', \'1000\', 0),
				(\'category\', \'thumb-image\', \'large\', 0),
				(\'category\', \'enable-effect\', \'No\', 1),
				(\'category\', \'link-to-product-page\', \'Yes\', 0),
				(\'category\', \'scroll-style\', \'default\', 0),
				(\'category\', \'show-image-title\', \'Yes\', 0),
				(\'category\', \'loop\', \'continue\', 0),
				(\'category\', \'speed\', \'5000\', 0),
				(\'category\', \'width\', \'0\', 0),
				(\'category\', \'height\', \'0\', 0),
				(\'category\', \'item-width\', \'150\', 1),
				(\'category\', \'item-height\', \'450\', 1),
				(\'category\', \'step\', \'3\', 0),
				(\'category\', \'items\', \'3\', 0),
				(\'category\', \'arrows\', \'outside\', 0),
				(\'category\', \'arrows-opacity\', \'60\', 0),
				(\'category\', \'arrows-hover-opacity\', \'100\', 0),
				(\'category\', \'slider-size\', \'10%\', 0),
				(\'category\', \'slider\', \'false\', 0),
				(\'category\', \'direction\', \'right\', 0),
				(\'category\', \'duration\', \'1000\', 0),
				(\'manufacturer\', \'thumb-image\', \'large\', 0),
				(\'manufacturer\', \'enable-effect\', \'No\', 1),
				(\'manufacturer\', \'link-to-product-page\', \'Yes\', 0),
				(\'manufacturer\', \'scroll-style\', \'default\', 0),
				(\'manufacturer\', \'show-image-title\', \'Yes\', 0),
				(\'manufacturer\', \'loop\', \'continue\', 0),
				(\'manufacturer\', \'speed\', \'5000\', 0),
				(\'manufacturer\', \'width\', \'0\', 0),
				(\'manufacturer\', \'height\', \'0\', 0),
				(\'manufacturer\', \'item-width\', \'150\', 1),
				(\'manufacturer\', \'item-height\', \'450\', 1),
				(\'manufacturer\', \'step\', \'3\', 0),
				(\'manufacturer\', \'items\', \'3\', 0),
				(\'manufacturer\', \'arrows\', \'outside\', 0),
				(\'manufacturer\', \'arrows-opacity\', \'60\', 0),
				(\'manufacturer\', \'arrows-hover-opacity\', \'100\', 0),
				(\'manufacturer\', \'slider-size\', \'10%\', 0),
				(\'manufacturer\', \'slider\', \'false\', 0),
				(\'manufacturer\', \'direction\', \'right\', 0),
				(\'manufacturer\', \'duration\', \'1000\', 0),
				(\'newproductpage\', \'thumb-image\', \'large\', 0),
				(\'newproductpage\', \'enable-effect\', \'No\', 1),
				(\'newproductpage\', \'link-to-product-page\', \'Yes\', 0),
				(\'newproductpage\', \'scroll-style\', \'default\', 0),
				(\'newproductpage\', \'show-image-title\', \'Yes\', 0),
				(\'newproductpage\', \'loop\', \'continue\', 0),
				(\'newproductpage\', \'speed\', \'5000\', 0),
				(\'newproductpage\', \'width\', \'0\', 0),
				(\'newproductpage\', \'height\', \'0\', 0),
				(\'newproductpage\', \'item-width\', \'150\', 1),
				(\'newproductpage\', \'item-height\', \'450\', 1),
				(\'newproductpage\', \'step\', \'3\', 0),
				(\'newproductpage\', \'items\', \'3\', 0),
				(\'newproductpage\', \'arrows\', \'outside\', 0),
				(\'newproductpage\', \'arrows-opacity\', \'60\', 0),
				(\'newproductpage\', \'arrows-hover-opacity\', \'100\', 0),
				(\'newproductpage\', \'slider-size\', \'10%\', 0),
				(\'newproductpage\', \'slider\', \'false\', 0),
				(\'newproductpage\', \'direction\', \'right\', 0),
				(\'newproductpage\', \'duration\', \'1000\', 0),
				(\'blocknewproducts\', \'thumb-image\', \'home\', 1),
				(\'blocknewproducts\', \'enable-effect\', \'Yes\', 1),
				(\'blocknewproducts\', \'link-to-product-page\', \'Yes\', 0),
				(\'blocknewproducts\', \'scroll-style\', \'default\', 0),
				(\'blocknewproducts\', \'show-image-title\', \'Yes\', 0),
				(\'blocknewproducts\', \'loop\', \'continue\', 0),
				(\'blocknewproducts\', \'speed\', \'5000\', 0),
				(\'blocknewproducts\', \'width\', \'0\', 0),
				(\'blocknewproducts\', \'height\', \'0\', 0),
				(\'blocknewproducts\', \'item-width\', \'0\', 0),
				(\'blocknewproducts\', \'item-height\', \'0\', 0),
				(\'blocknewproducts\', \'step\', \'3\', 0),
				(\'blocknewproducts\', \'items\', \'3\', 0),
				(\'blocknewproducts\', \'arrows\', \'outside\', 0),
				(\'blocknewproducts\', \'arrows-opacity\', \'60\', 0),
				(\'blocknewproducts\', \'arrows-hover-opacity\', \'100\', 0),
				(\'blocknewproducts\', \'slider-size\', \'10%\', 0),
				(\'blocknewproducts\', \'slider\', \'false\', 0),
				(\'blocknewproducts\', \'direction\', \'bottom\', 1),
				(\'blocknewproducts\', \'duration\', \'1000\', 0),
				(\'blocknewproducts_home\', \'thumb-image\', \'large\', 0),
				(\'blocknewproducts_home\', \'enable-effect\', \'Yes\', 1),
				(\'blocknewproducts_home\', \'link-to-product-page\', \'Yes\', 0),
				(\'blocknewproducts_home\', \'scroll-style\', \'default\', 0),
				(\'blocknewproducts_home\', \'show-image-title\', \'Yes\', 0),
				(\'blocknewproducts_home\', \'loop\', \'continue\', 0),
				(\'blocknewproducts_home\', \'speed\', \'5000\', 0),
				(\'blocknewproducts_home\', \'width\', \'0\', 0),
				(\'blocknewproducts_home\', \'height\', \'0\', 0),
				(\'blocknewproducts_home\', \'item-width\', \'0\', 0),
				(\'blocknewproducts_home\', \'item-height\', \'0\', 0),
				(\'blocknewproducts_home\', \'step\', \'3\', 0),
				(\'blocknewproducts_home\', \'items\', \'3\', 0),
				(\'blocknewproducts_home\', \'arrows\', \'outside\', 0),
				(\'blocknewproducts_home\', \'arrows-opacity\', \'60\', 0),
				(\'blocknewproducts_home\', \'arrows-hover-opacity\', \'100\', 0),
				(\'blocknewproducts_home\', \'slider-size\', \'10%\', 0),
				(\'blocknewproducts_home\', \'slider\', \'false\', 0),
				(\'blocknewproducts_home\', \'direction\', \'right\', 0),
				(\'blocknewproducts_home\', \'duration\', \'1000\', 0),
				(\'bestsellerspage\', \'thumb-image\', \'large\', 0),
				(\'bestsellerspage\', \'enable-effect\', \'No\', 1),
				(\'bestsellerspage\', \'link-to-product-page\', \'Yes\', 0),
				(\'bestsellerspage\', \'scroll-style\', \'default\', 0),
				(\'bestsellerspage\', \'show-image-title\', \'Yes\', 0),
				(\'bestsellerspage\', \'loop\', \'continue\', 0),
				(\'bestsellerspage\', \'speed\', \'5000\', 0),
				(\'bestsellerspage\', \'width\', \'0\', 0),
				(\'bestsellerspage\', \'height\', \'0\', 0),
				(\'bestsellerspage\', \'item-width\', \'150\', 1),
				(\'bestsellerspage\', \'item-height\', \'450\', 1),
				(\'bestsellerspage\', \'step\', \'3\', 0),
				(\'bestsellerspage\', \'items\', \'3\', 0),
				(\'bestsellerspage\', \'arrows\', \'outside\', 0),
				(\'bestsellerspage\', \'arrows-opacity\', \'60\', 0),
				(\'bestsellerspage\', \'arrows-hover-opacity\', \'100\', 0),
				(\'bestsellerspage\', \'slider-size\', \'10%\', 0),
				(\'bestsellerspage\', \'slider\', \'false\', 0),
				(\'bestsellerspage\', \'direction\', \'right\', 0),
				(\'bestsellerspage\', \'duration\', \'1000\', 0),
				(\'blockbestsellers\', \'thumb-image\', \'home\', 1),
				(\'blockbestsellers\', \'max-number-of-products\', \'5\', 1),
				(\'blockbestsellers\', \'enable-effect\', \'Yes\', 1),
				(\'blockbestsellers\', \'link-to-product-page\', \'Yes\', 0),
				(\'blockbestsellers\', \'scroll-style\', \'default\', 0),
				(\'blockbestsellers\', \'show-image-title\', \'Yes\', 0),
				(\'blockbestsellers\', \'loop\', \'continue\', 0),
				(\'blockbestsellers\', \'speed\', \'5000\', 0),
				(\'blockbestsellers\', \'width\', \'0\', 0),
				(\'blockbestsellers\', \'height\', \'0\', 0),
				(\'blockbestsellers\', \'item-width\', \'0\', 0),
				(\'blockbestsellers\', \'item-height\', \'0\', 0),
				(\'blockbestsellers\', \'step\', \'3\', 0),
				(\'blockbestsellers\', \'items\', \'3\', 0),
				(\'blockbestsellers\', \'arrows\', \'outside\', 0),
				(\'blockbestsellers\', \'arrows-opacity\', \'60\', 0),
				(\'blockbestsellers\', \'arrows-hover-opacity\', \'100\', 0),
				(\'blockbestsellers\', \'slider-size\', \'10%\', 0),
				(\'blockbestsellers\', \'slider\', \'false\', 0),
				(\'blockbestsellers\', \'direction\', \'bottom\', 1),
				(\'blockbestsellers\', \'duration\', \'1000\', 0),
				(\'blockbestsellers_home\', \'thumb-image\', \'large\', 0),
				(\'blockbestsellers_home\', \'max-number-of-products\', \'8\', 1),
				(\'blockbestsellers_home\', \'enable-effect\', \'Yes\', 1),
				(\'blockbestsellers_home\', \'link-to-product-page\', \'Yes\', 0),
				(\'blockbestsellers_home\', \'scroll-style\', \'default\', 0),
				(\'blockbestsellers_home\', \'show-image-title\', \'Yes\', 0),
				(\'blockbestsellers_home\', \'loop\', \'continue\', 0),
				(\'blockbestsellers_home\', \'speed\', \'5000\', 0),
				(\'blockbestsellers_home\', \'width\', \'0\', 0),
				(\'blockbestsellers_home\', \'height\', \'0\', 0),
				(\'blockbestsellers_home\', \'item-width\', \'0\', 0),
				(\'blockbestsellers_home\', \'item-height\', \'0\', 0),
				(\'blockbestsellers_home\', \'step\', \'3\', 0),
				(\'blockbestsellers_home\', \'items\', \'3\', 0),
				(\'blockbestsellers_home\', \'arrows\', \'outside\', 0),
				(\'blockbestsellers_home\', \'arrows-opacity\', \'60\', 0),
				(\'blockbestsellers_home\', \'arrows-hover-opacity\', \'100\', 0),
				(\'blockbestsellers_home\', \'slider-size\', \'10%\', 0),
				(\'blockbestsellers_home\', \'slider\', \'false\', 0),
				(\'blockbestsellers_home\', \'direction\', \'right\', 0),
				(\'blockbestsellers_home\', \'duration\', \'1000\', 0),
				(\'specialspage\', \'thumb-image\', \'large\', 0),
				(\'specialspage\', \'enable-effect\', \'No\', 1),
				(\'specialspage\', \'link-to-product-page\', \'Yes\', 0),
				(\'specialspage\', \'scroll-style\', \'default\', 0),
				(\'specialspage\', \'show-image-title\', \'Yes\', 0),
				(\'specialspage\', \'loop\', \'continue\', 0),
				(\'specialspage\', \'speed\', \'5000\', 0),
				(\'specialspage\', \'width\', \'0\', 0),
				(\'specialspage\', \'height\', \'0\', 0),
				(\'specialspage\', \'item-width\', \'150\', 1),
				(\'specialspage\', \'item-height\', \'450\', 1),
				(\'specialspage\', \'step\', \'3\', 0),
				(\'specialspage\', \'items\', \'3\', 0),
				(\'specialspage\', \'arrows\', \'outside\', 0),
				(\'specialspage\', \'arrows-opacity\', \'60\', 0),
				(\'specialspage\', \'arrows-hover-opacity\', \'100\', 0),
				(\'specialspage\', \'slider-size\', \'10%\', 0),
				(\'specialspage\', \'slider\', \'false\', 0),
				(\'specialspage\', \'direction\', \'right\', 0),
				(\'specialspage\', \'duration\', \'1000\', 0),
				(\'blockspecials\', \'thumb-image\', \'home\', 1),
				(\'blockspecials\', \'enable-effect\', \'Yes\', 1),
				(\'blockspecials\', \'link-to-product-page\', \'Yes\', 0),
				(\'blockspecials\', \'scroll-style\', \'default\', 0),
				(\'blockspecials\', \'show-image-title\', \'Yes\', 0),
				(\'blockspecials\', \'loop\', \'continue\', 0),
				(\'blockspecials\', \'speed\', \'5000\', 0),
				(\'blockspecials\', \'width\', \'0\', 0),
				(\'blockspecials\', \'height\', \'0\', 0),
				(\'blockspecials\', \'item-width\', \'0\', 0),
				(\'blockspecials\', \'item-height\', \'0\', 0),
				(\'blockspecials\', \'step\', \'3\', 0),
				(\'blockspecials\', \'items\', \'3\', 0),
				(\'blockspecials\', \'arrows\', \'outside\', 0),
				(\'blockspecials\', \'arrows-opacity\', \'60\', 0),
				(\'blockspecials\', \'arrows-hover-opacity\', \'100\', 0),
				(\'blockspecials\', \'slider-size\', \'10%\', 0),
				(\'blockspecials\', \'slider\', \'false\', 0),
				(\'blockspecials\', \'direction\', \'bottom\', 1),
				(\'blockspecials\', \'duration\', \'1000\', 0),
				(\'blockviewed\', \'thumb-image\', \'home\', 1),
				(\'blockviewed\', \'enable-effect\', \'Yes\', 1),
				(\'blockviewed\', \'link-to-product-page\', \'Yes\', 0),
				(\'blockviewed\', \'scroll-style\', \'default\', 0),
				(\'blockviewed\', \'show-image-title\', \'Yes\', 0),
				(\'blockviewed\', \'loop\', \'continue\', 0),
				(\'blockviewed\', \'speed\', \'5000\', 0),
				(\'blockviewed\', \'width\', \'0\', 0),
				(\'blockviewed\', \'height\', \'0\', 0),
				(\'blockviewed\', \'item-width\', \'0\', 0),
				(\'blockviewed\', \'item-height\', \'0\', 0),
				(\'blockviewed\', \'step\', \'3\', 0),
				(\'blockviewed\', \'items\', \'3\', 0),
				(\'blockviewed\', \'arrows\', \'outside\', 0),
				(\'blockviewed\', \'arrows-opacity\', \'60\', 0),
				(\'blockviewed\', \'arrows-hover-opacity\', \'100\', 0),
				(\'blockviewed\', \'slider-size\', \'10%\', 0),
				(\'blockviewed\', \'slider\', \'false\', 0),
				(\'blockviewed\', \'direction\', \'bottom\', 1),
				(\'blockviewed\', \'duration\', \'1000\', 0),
				(\'homefeatured\', \'thumb-image\', \'home\', 1),
				(\'homefeatured\', \'enable-effect\', \'Yes\', 1),
				(\'homefeatured\', \'link-to-product-page\', \'Yes\', 0),
				(\'homefeatured\', \'scroll-style\', \'default\', 0),
				(\'homefeatured\', \'show-image-title\', \'Yes\', 0),
				(\'homefeatured\', \'loop\', \'continue\', 0),
				(\'homefeatured\', \'speed\', \'5000\', 0),
				(\'homefeatured\', \'width\', \'535\', 1),
				(\'homefeatured\', \'height\', \'0\', 0),
				(\'homefeatured\', \'item-width\', \'0\', 0),
				(\'homefeatured\', \'item-height\', \'0\', 0),
				(\'homefeatured\', \'step\', \'3\', 0),
				(\'homefeatured\', \'items\', \'3\', 0),
				(\'homefeatured\', \'arrows\', \'outside\', 0),
				(\'homefeatured\', \'arrows-opacity\', \'60\', 0),
				(\'homefeatured\', \'arrows-hover-opacity\', \'100\', 0),
				(\'homefeatured\', \'slider-size\', \'10%\', 0),
				(\'homefeatured\', \'slider\', \'false\', 0),
				(\'homefeatured\', \'direction\', \'right\', 0),
				(\'homefeatured\', \'duration\', \'1000\', 0),
				(\'search\', \'thumb-image\', \'large\', 0),
				(\'search\', \'enable-effect\', \'No\', 1),
				(\'search\', \'link-to-product-page\', \'Yes\', 0),
				(\'search\', \'scroll-style\', \'default\', 0),
				(\'search\', \'show-image-title\', \'Yes\', 0),
				(\'search\', \'loop\', \'continue\', 0),
				(\'search\', \'speed\', \'5000\', 0),
				(\'search\', \'width\', \'0\', 0),
				(\'search\', \'height\', \'0\', 0),
				(\'search\', \'item-width\', \'150\', 1),
				(\'search\', \'item-height\', \'450\', 1),
				(\'search\', \'step\', \'3\', 0),
				(\'search\', \'items\', \'3\', 0),
				(\'search\', \'arrows\', \'outside\', 0),
				(\'search\', \'arrows-opacity\', \'60\', 0),
				(\'search\', \'arrows-hover-opacity\', \'100\', 0),
				(\'search\', \'slider-size\', \'10%\', 0),
				(\'search\', \'slider\', \'false\', 0),
				(\'search\', \'direction\', \'right\', 0),
				(\'search\', \'duration\', \'1000\', 0)';
		if($this->isPrestahop16x) {
			$sql = preg_replace('/\r\n\s*..(?:category|manufacturer|newproductpage|bestsellerspage|specialspage|search)\b[^\r]*+/i', '', $sql);
			$sql = rtrim($sql, ',');
		}
		if(!$this->isPrestahop16x) {
			$sql = preg_replace('/\r\n\s*..(?:blockbestsellers_home|blocknewproducts_home)\b[^\r]*+/i', '', $sql);
			$sql = rtrim($sql, ',');
		}
		return Db::getInstance()->Execute($sql);
	}

	function getBlocks() {
		$blocks = array(
			'default' => 'Defaults',
			'product' => 'Product page',
			'category' => 'Category page',
			'manufacturer' => 'Manufacturers page',
			'newproductpage' => 'New products page',
			'blocknewproducts' => 'New products sidebar',
			'blocknewproducts_home' => 'New products block',
			'bestsellerspage' => 'Bestsellers page',
			'blockbestsellers' => 'Bestsellers sidebar',
			'blockbestsellers_home' => 'Bestsellers block',
			'specialspage' => 'Specials page',
			'blockspecials' => 'Specials sidebar',
			'blockviewed' => 'Viewed sidebar',
			'homefeatured' => 'Featured block',
			'search' => 'Search page'
		);
		if($this->isPrestahop16x) {
			unset($blocks['category'], $blocks['manufacturer'], $blocks['newproductpage'], $blocks['bestsellerspage'], $blocks['specialspage'], $blocks['search']);
		}
		if(!$this->isPrestahop16x) {
			unset($blocks['blockbestsellers_home'], $blocks['blocknewproducts_home']);
		}
		return $blocks;
	}

	function getMessages() {
		return array(
			'default' => array(
				'message' => array(
					'title' => 'Defaults message (under Magic Scroll)',
					'translate' => $this->l('Defaults message (under Magic Scroll)')
				)
			),
			'product' => array(
				'message' => array(
					'title' => 'Product page message (under Magic Scroll)',
					'translate' => $this->l('Product page message (under Magic Scroll)')
				)
			),
			'category' => array(
				'message' => array(
					'title' => 'Category page message (under Magic Scroll)',
					'translate' => $this->l('Category page message (under Magic Scroll)')
				)
			),
			'manufacturer' => array(
				'message' => array(
					'title' => 'Manufacturers page message (under Magic Scroll)',
					'translate' => $this->l('Manufacturers page message (under Magic Scroll)')
				)
			),
			'newproductpage' => array(
				'message' => array(
					'title' => 'New products page message (under Magic Scroll)',
					'translate' => $this->l('New products page message (under Magic Scroll)')
				)
			),
			'blocknewproducts' => array(
				'message' => array(
					'title' => 'New products sidebar message (under Magic Scroll)',
					'translate' => $this->l('New products sidebar message (under Magic Scroll)')
				)
			),
			'blocknewproducts_home' => array(
				'message' => array(
					'title' => 'New products block message (under Magic Scroll)',
					'translate' => $this->l('New products block message (under Magic Scroll)')
				)
			),
			'bestsellerspage' => array(
				'message' => array(
					'title' => 'Bestsellers page message (under Magic Scroll)',
					'translate' => $this->l('Bestsellers page message (under Magic Scroll)')
				)
			),
			'blockbestsellers' => array(
				'message' => array(
					'title' => 'Bestsellers sidebar message (under Magic Scroll)',
					'translate' => $this->l('Bestsellers sidebar message (under Magic Scroll)')
				)
			),
			'blockbestsellers_home' => array(
				'message' => array(
					'title' => 'Bestsellers block message (under Magic Scroll)',
					'translate' => $this->l('Bestsellers block message (under Magic Scroll)')
				)
			),
			'specialspage' => array(
				'message' => array(
					'title' => 'Specials page message (under Magic Scroll)',
					'translate' => $this->l('Specials page message (under Magic Scroll)')
				)
			),
			'blockspecials' => array(
				'message' => array(
					'title' => 'Specials sidebar message (under Magic Scroll)',
					'translate' => $this->l('Specials sidebar message (under Magic Scroll)')
				)
			),
			'blockviewed' => array(
				'message' => array(
					'title' => 'Viewed sidebar message (under Magic Scroll)',
					'translate' => $this->l('Viewed sidebar message (under Magic Scroll)')
				)
			),
			'homefeatured' => array(
				'message' => array(
					'title' => 'Featured block message (under Magic Scroll)',
					'translate' => $this->l('Featured block message (under Magic Scroll)')
				)
			),
			'search' => array(
				'message' => array(
					'title' => 'Search page message (under Magic Scroll)',
					'translate' => $this->l('Search page message (under Magic Scroll)')
				)
			)
		);
	}

	function getParamsMap() {
		$map = array(
			'default' => array(
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'link-to-product-page',
					'include-headers-on-all-pages'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'product' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'category' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'manufacturer' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'newproductpage' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'blocknewproducts' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'blocknewproducts_home' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'bestsellerspage' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'blockbestsellers' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'max-number-of-products',
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'blockbestsellers_home' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'max-number-of-products',
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'specialspage' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'blockspecials' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'blockviewed' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'homefeatured' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Image type' => array(
					'thumb-image'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			),
			'search' => array(
				'Enable effect' => array(
					'enable-effect'
				),
				'Miscellaneous' => array(
					'link-to-product-page'
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items'
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity'
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider'
				),
				'Scroll effect' => array(
					'direction',
					'duration'
				)
			)
		);
		if($this->isPrestahop16x) {
			unset($map['category'], $map['manufacturer'], $map['newproductpage'], $map['bestsellerspage'], $map['specialspage'], $map['search']);
		}
		if(!$this->isPrestahop16x) {
			unset($map['blockbestsellers_home'], $map['blocknewproducts_home']);
		}
		return $map;
	}

}
