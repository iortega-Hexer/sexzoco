<?php
/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2022 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to v2.0.0
 *
 * @param \Advancedemailguard $module
 *
 * @return bool
 */
function upgrade_module_2_0_0($module)
{
    // Create new configs.
    $configs = array(
        'ADVEG_INSTALL_DATE' => date('Y-m-d H:i:s'),
        'ADVEG_HELLO_DISMISSED' => false,

        'ADVEG_MSG_GUARD_ENABLED' => false,
        'ADVEG_MSG_GUARD_CONTACT_US' => false,
        'ADVEG_MSG_GUARD_PRODUCT_REVIEWS' => false,
        'ADVEG_MSG_GUARD_BANNED_PHRASES' => '["sign up free today"]',

        'ADVEG_REC_ENABLED' => false,
        'ADVEG_REC_KEY' => null,
        'ADVEG_REC_SECRET' => null,
        'ADVEG_REC_INV_KEY' => null,
        'ADVEG_REC_INV_SECRET' => null,
        'ADVEG_REC_CONTACT_US' => true,
        'ADVEG_REC_REGISTER' => true,
        'ADVEG_REC_CHECKOUT' => false,
        'ADVEG_REC_NEWSLETTER' => false,
        'ADVEG_REC_SEND_TO_FRIEND' => false,
        'ADVEG_REC_PRODUCT_REVIEWS' => false,

        'ADVEG_REC_LANGUAGE' => 'shop',
        'ADVEG_REC_TYPE' => 'recaptcha_v2',
        'ADVEG_REC_THEME' => 'light',
        'ADVEG_REC_SIZE' => 'normal',
        'ADVEG_REC_POSITION' => 'bottomright',

        'ADVEG_REC_CONTACT_US_ALIGN' => 'indent',
        'ADVEG_REC_REGISTER_ALIGN' => 'center',
        'ADVEG_REC_CHECKOUT_ALIGN' => 'center',
        'ADVEG_REC_NEWSLETTER_ALIGN' => 'left',
        'ADVEG_REC_SEND_TO_FRIEND_ALIGN' => 'left',
        'ADVEG_REC_PRODUCT_REVIEWS_ALIGN' => 'left',

        'ADVEG_REC_CONTACT_US_INDENT' => 3,
        'ADVEG_REC_REGISTER_INDENT' => 1,
        'ADVEG_REC_CHECKOUT_INDENT' => 1,
        'ADVEG_REC_NEWSLETTER_INDENT' => 1,
        'ADVEG_REC_SEND_TO_FRIEND_INDENT' => 1,
        'ADVEG_REC_PRODUCT_REVIEWS_INDENT' => 1,
    );

    foreach ($configs as $key => $value) {
        if (is_bool($value)) {
            $value = (int) $value;
        }

        Configuration::updateValue($key, $value);
    }

    // Create new admin controllers.
    $adminControllers = array(
        array(
            'name' => 'Advanced Anti Spam + reCAPTCHA',
            'class_name' => 'AdminAdvancedEmailGuardProxy',
            'id_parent' => 0,
            'active' => 0,
        ),
        array(
            'name' => 'Advanced Anti Spam + reCAPTCHA',
            'class_name' => 'AdminAdvancedEmailGuard',
            'id_parent' => 0,
            'active' => 0,
        ),
    );

    $useNewIcons = Tools::version_compare(_PS_VERSION_, '1.7', '>=');

    foreach ($adminControllers as $controller) {
        $tab = $module->newAdminTab();

        foreach ($controller as $property => $value) {
            if ($property == 'name') {
                $tab->$property = $module->getMultiLangArray($value);
            } elseif ($property == 'id_parent' && is_string($value)) {
                $tab->$property = (int) Tab::getIdFromClassName($value);
            } elseif ($property == 'icons') {
                $tab->icon = $useNewIcons ? $value['md'] : $value['fa'];
            } else {
                $tab->$property = $value;
            }
        }

        if (!$tab->save()) {
            return false;
        }
    }

    // Migrate the database.
    if (!$sql = $module->getMigrationQueriesArray('upgradesql/upgrade-2.0.0.sql')) {
        return false;
    }

    foreach ($sql as $query) {
        if (!$module->db->execute($query)) {
            return false;
        }
    }

    return true;
}
