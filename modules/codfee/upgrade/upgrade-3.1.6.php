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

function upgrade_module_3_1_6($module)
{
    try {
        Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'codfee_configuration`
            ADD `payment_size` varchar(10) NOT NULL DEFAULT "col-md-12" AFTER `position`;'
        );
        return $module;
    } catch (Exception $e) {
        return true;
    }
}