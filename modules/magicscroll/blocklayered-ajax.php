<?php

chdir(dirname(__FILE__).'/../blocklayered');

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$magicscrollInstance = Module::getInstanceByName('magicscroll');

if($magicscrollInstance && $magicscrollInstance->active) {
    $magicscrollTool = $magicscrollInstance->loadTool();
    $magicscrollFilter = 'parseTemplate'.($magicscrollTool->type == 'standard' ? 'Standard' : 'Category');
    if($magicscrollInstance->isSmarty3) {
        //Smarty v3 template engine
        $smarty->registerFilter('output', array($magicscrollInstance, $magicscrollFilter));
    } else {
        //Smarty v2 template engine
        $smarty->register_outputfilter(array($magicscrollInstance, $magicscrollFilter));
    }
    if(!isset($GLOBALS['magictoolbox']['filters'])) {
        $GLOBALS['magictoolbox']['filters'] = array();
    }
    $GLOBALS['magictoolbox']['filters']['magicscroll'] = $magicscrollFilter;
}

include(dirname(__FILE__).'/../blocklayered/blocklayered.php');

Context::getContext()->controller->php_self = 'category';
$blockLayered = new BlockLayered();
echo $blockLayered->ajaxCall();
