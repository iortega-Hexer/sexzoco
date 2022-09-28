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
 * Upgrade to v3.0.0
 *
 * @param \Advancedemailguard $module
 * @return bool
 */
function upgrade_module_3_0_0($module)
{
    // Create new configs.
    $configs = array(
        'ADVEG_REC_V3_KEY' => null,
        'ADVEG_REC_V3_SECRET' => null,
        'ADVEG_REC_THRESHOLD' => '0.5',
    );

    foreach ($configs as $key => $value) {
        Configuration::updateValue($key, $value);
    }

    // Migrate the database.
    if (!$sql = $module->getMigrationQueriesArray('upgradesql/upgrade-3.0.0.sql')) {
        return false;
    }

    foreach ($sql as $query) {
        if (!$module->db->execute($query)) {
            return false;
        }
    }

    return true;
}
