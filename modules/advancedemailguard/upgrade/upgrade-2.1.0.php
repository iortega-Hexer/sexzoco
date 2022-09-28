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
 * Upgrade to v2.1.0
 *
 * @param \Advancedemailguard $module
 * @return bool
 */
function upgrade_module_2_1_0($module)
{
    unset($module);
    $recaptchaSize = Configuration::get('ADVEG_REC_SIZE');

    // Create new configs.
    $configs = array(
        'ADVEG_GUARD_STOCK_ALERT' => false,
        'ADVEG_REC_STOCK_ALERT' => false,
        'ADVEG_REC_STOCK_ALERT_ALIGN' => 'left',
        'ADVEG_REC_STOCK_ALERT_INDENT' => 1,
        'ADVEG_REC_STOCK_ALERT_SIZE' => 'normal',

        'ADVEG_REC_CONTACT_US_SIZE' => $recaptchaSize,
        'ADVEG_REC_REGISTER_SIZE' => $recaptchaSize,
        'ADVEG_REC_CHECKOUT_SIZE' => $recaptchaSize,
        'ADVEG_REC_NEWSLETTER_SIZE' => $recaptchaSize,
        'ADVEG_REC_SEND_TO_FRIEND_SIZE' => 'compact',
        'ADVEG_REC_PRODUCT_REVIEWS_SIZE' => 'compact',
    );

    foreach ($configs as $key => $value) {
        Configuration::updateValue($key, $value);
    }

    // Delete old configs.
    Configuration::deleteByName('ADVEG_REC_SIZE');

    return true;
}
