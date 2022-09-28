<?php
/**
* Cash On Delivery With Fee
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2017 idnovate
*  @license   See above
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_2_0($module)
{
    try {
        Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'codfee_configuration`
            ADD `show_productpage` tinyint(1) unsigned NOT NULL DEFAULT "0" AFTER `round`;'
        );
        $module->registerHook('displayRightColumnProduct');
        $module->registerHook('displayLeftColumnProduct');
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $module->registerHook('displayProductAdditionalInfo');
        }
        return $module;
    } catch (Exception $e) {
        return true;
    }
}
