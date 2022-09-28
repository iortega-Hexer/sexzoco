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
*  @copyright 2020 idnovate
*  @license   See above
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_3_5($module)
{
    try {
        Db::getInstance()->Execute('
            CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$module->name).'_orders` (
                `id_codfee_orders` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_codfee_configuration` int(10) unsigned NOT NULL,
                `id_order` int(11) unsigned NOT NULL,
                `fee_amount` decimal(10,3) DEFAULT "0.000",
                PRIMARY KEY (`id_codfee_orders`),
                KEY `id_codfee_orders` (`id_codfee_orders`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');
        return $module->installOverrides();
    } catch (Exception $e) {
        return true;
    }
}
