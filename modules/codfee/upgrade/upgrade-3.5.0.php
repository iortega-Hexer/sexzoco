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
*  @copyright 2021 idnovate
*  @license   See above
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_5_0($module)
{
    try {
        $check_field = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="._DB_PREFIX_."'codfee_configuration' AND COLUMN_NAME='id_tax_rule'";
        if (!Db::getInstance()->getRow($check_field)) {
            Db::getInstance()->execute(
                'ALTER TABLE `'._DB_PREFIX_.'codfee_configuration`
                ADD `id_tax_rule` int(10) unsigned NOT NULL DEFAULT "0" AFTER `percentage`;'
            );
        }
        $check_field = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="._DB_PREFIX_."'codfee_configuration' AND COLUMN_NAME='filter_by_product'";
        if (!Db::getInstance()->getRow($check_field)) {
            Db::getInstance()->execute(
                'ALTER TABLE `'._DB_PREFIX_.'codfee_configuration`
                ADD `filter_by_product` tinyint(1) unsigned NOT NULL DEFAULT "0" AFTER `categories`;'
            );
        }
        $check_field = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="._DB_PREFIX_."'codfee_configuration' AND COLUMN_NAME='products'";
        if (!Db::getInstance()->getRow($check_field)) {
            Db::getInstance()->execute(
                'ALTER TABLE `'._DB_PREFIX_.'codfee_configuration`
                ADD `products` TEXT NULL AFTER `filter_by_product`;'
            );
        }
        $check_field = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="._DB_PREFIX_."'codfee_configuration' AND COLUMN_NAME='filter_by_customer'";
        if (!Db::getInstance()->getRow($check_field)) {
            Db::getInstance()->execute(
                'ALTER TABLE `'._DB_PREFIX_.'codfee_configuration`
                ADD `filter_by_customer` tinyint(1) unsigned NOT NULL DEFAULT "0" AFTER `groups`;'
            );
        }
        $check_field = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="._DB_PREFIX_."'codfee_configuration' AND COLUMN_NAME='customers'";
        if (!Db::getInstance()->getRow($check_field)) {
            Db::getInstance()->execute(
                'ALTER TABLE `'._DB_PREFIX_.'codfee_configuration`
                ADD `customers` TEXT NULL AFTER `filter_by_customer`;'
            );
        }
        return $module;
    } catch (Exception $e) {
        return true;
    }
}
