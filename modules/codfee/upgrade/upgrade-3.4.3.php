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

function upgrade_module_3_4_3($module)
{
    try {
        Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'codfee_orders`
            ADD `id_carrier` int(5) unsigned NOT NULL AFTER `id_order`;'
        );
        $orders = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.bqSQL('codfee_orders').'`'
        );
        foreach ($orders as $key => $ord) {
            $order = new Order((int)$ord['id_order']);
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.bqSQL('codfee_orders').'`
                SET `id_carrier` = '.$order->id_carrier.'
                WHERE `'.bqSQL('id_order').'` = '.(int)$order->id
            );
        }
        addTab($module->displayName, $module->tabClassAjaxName, -1, $module);
        return $module;
    } catch (Exception $e) {
        return true;
    }
}

function addTab($tabName, $tabClassName, $id_parent = -1, $module)
{
    $id_tab = Tab::getIdFromClassName($tabClassName);
    $tabNames = array();
    if (!$id_tab) {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $langs = Language::getlanguages(false);

            foreach ($langs as $l) {
                $tabNames[$l['id_lang']] = Tools::substr($tabName, 0, 32);
            }

            $tab = new Tab();
            $tab->module = 'codfee';
            $tab->name = $tabNames;
            $tab->class_name = $tabClassName;
            $tab->id_parent = -1;

            if (!$tab->save()) {
                return false;
            }
        } else {
            $tab = new Tab();
            $tab->class_name = $tabClassName;
            $tab->id_parent = -1;
            $tab->module = 'codfee';
            $languages = Language::getLanguages();

            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = Tools::substr($module->l($tabName), 0, 32);
            }

            if (!$tab->add()) {
                return false;
            }
        }
    }
    return true;
}
