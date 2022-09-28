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
 * Upgrade to v4.1.0
 *
 * @param \Advancedemailguard $module
 * @return bool
 */
function upgrade_module_4_1_0($module)
{
    if (Shop::isFeatureActive()) {
        Shop::setContext(Shop::CONTEXT_ALL);
    }

    if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
        $newHooks = array('displayFooterAfter', 'displayBeforeBodyClosingTag');
        foreach ($newHooks as $hook) {
            $module->registerHook($hook);
        }
    }

    // Create new configs.
    $configs = array(
        'ADVEG_LOGS_MODE' => 'all',
    );
    foreach ($configs as $key => $value) {
        Configuration::updateValue($key, $value);
    }

    $globalConfigs = array(
        'ADVEG_LOGS_TOKEN' => Tools::passwdGen(12),
        'ADVEG_INSTALL_DATE' => date('Y-m-d H:i:s'),
        'ADVEG_DISPLAY_RATING' => 0,
    );

    // Promote install date to global config.
    $query = new DbQuery();
    $query->select('`value`')->from('configuration')->where('`name` = \'ADVEG_INSTALL_DATE\'');
    $value = $module->db->getValue($query);
    if (is_string($value)) {
        $globalConfigs['ADVEG_INSTALL_DATE'] = $value;
    }
    Configuration::deleteByName('ADVEG_INSTALL_DATE');

    // Promote display rating to global config.
    $query = new DbQuery();
    $query->select('`value`')->from('configuration')->where('`name` = \'ADVEG_DISPLAY_RATING\'');
    $value = $module->db->getValue($query);
    if (is_string($value)) {
        $globalConfigs['ADVEG_DISPLAY_RATING'] = (int) $value;
    }
    Configuration::deleteByName('ADVEG_DISPLAY_RATING');

    foreach ($globalConfigs as $key => $value) {
        Configuration::updateGlobalValue($key, $value);
    }

    return true;
}
