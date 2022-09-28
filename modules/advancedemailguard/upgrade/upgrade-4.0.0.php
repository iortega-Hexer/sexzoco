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
 * Upgrade to v4.0.0
 *
 * @param \Advancedemailguard $module
 * @return bool
 */
function upgrade_module_4_0_0($module)
{
    // Delete deprecated admin controller.
    $tab = Tab::getInstanceFromClassName('AdminAdvancedEmailGuardProxy');
    if (!$tab->delete()) {
        return false;
    }

    // Run migrations.
    $sql = $module->getMigrationQueriesArray('upgradesql/upgrade-4.0.0.sql');
    if (empty($sql)) {
        return false;
    }
    foreach ($sql as $query) {
        if (!$module->db->execute($query)) {
            return false;
        }
    }

    $defShop = (int) Configuration::get('PS_SHOP_DEFAULT');
    foreach ($module->getLogTables() as $type => $table) {
        $module->db->update($table, array('id_shop' => $defShop));
        $module->db->update($table, array('form' => 'write_review'), '`form` = \'product_reviews\'');
        $module->db->update($table, array('form' => 'notify_when_in_stock'), '`form` = \'stock_alert\'');
        $module->db->update($table, array('form' => 'quick_order'), '`form` = \'checkout\'');

        if ($type === 'message') {
            $query = new DbQuery();
            $logs = $module->db->executeS($query->from($table)) ?: array();
            foreach ($logs as $log) {
                $newText = '';
                $text = json_decode($log['text']);
                if (is_array($text) && count($text) > 0) {
                    $newText = $text[0];
                }
                $module->db->update($table, array('text' => pSQL($newText)), '`id_log` = ' . (int) $log['id_log']);
            }
        }
    }

    $groups = array();
    $shops = array(0);
    if (Shop::isFeatureActive()) {
        $sQuery = new DbQuery();
        $shops = $module->db->executeS($sQuery->select('id_shop')->from('shop')) ?: array();
        $shops = array_map('intval', array_column($shops, 'id_shop'));
        array_unshift($shops, 0);

        $gQuery = new DbQuery();
        $groups = $module->db->executeS($gQuery->select('id_shop_group')->from('shop_group')) ?: array();
        $groups = array_map('intval', array_column($groups, 'id_shop_group'));
    }

    $forms = array(
        'CONTACT_US', 'REGISTER', 'CHECKOUT', 'NEWSLETTER',
        'SEND_TO_FRIEND', 'PRODUCT_REVIEWS', 'STOCK_ALERT',
    );
    $contexts = compact('groups', 'shops');
    foreach ($contexts as $type => $ids) {
        foreach ($ids as $id) {
            $groupID = 0;
            $shopID = 0;
            //  0  - 0   => global context
            // >=1 - 0   => shop group context
            //  0  - >=1 => shop context
            if ($type === 'groups') {
                $groupID = $id;
            } elseif ($id !== 0) {
                $shopID = $id;
            }

            $rating = (bool) Configuration::get('ADVEG_HELLO_DISMISSED', null, $groupID, $shopID);
            Configuration::updateValue('ADVEG_DISPLAY_RATING', (int) $rating, false, $groupID, $shopID);

            $threshold = Configuration::get('ADVEG_REC_THRESHOLD', null, $groupID, $shopID) ?: '0.5';
            Configuration::updateValue('ADVEG_REC_SCORE_THRESHOLD', $threshold, false, $groupID, $shopID);

            $recType = Configuration::get('ADVEG_REC_TYPE', null, $groupID, $shopID);
            if ($recType === false || $recType === 'recaptcha_v2') {
                Configuration::updateValue('ADVEG_REC_TYPE', 'v2_cbx', false, $groupID, $shopID);
            } elseif ($recType === 'invisible_recaptcha') {
                Configuration::updateValue('ADVEG_REC_TYPE', 'v2_inv', false, $groupID, $shopID);
            } else {
                Configuration::updateValue('ADVEG_REC_TYPE', 'v3', false, $groupID, $shopID);
            }

            $value = Configuration::get('ADVEG_REC_KEY', null, $groupID, $shopID) ?: '';
            Configuration::updateValue('ADVEG_REC_V2_CBX_KEY', $value, false, $groupID, $shopID);

            $value = Configuration::get('ADVEG_REC_SECRET', null, $groupID, $shopID) ?: '';
            Configuration::updateValue('ADVEG_REC_V2_CBX_SECRET', $value, false, $groupID, $shopID);

            $value = Configuration::get('ADVEG_REC_INV_KEY', null, $groupID, $shopID) ?: '';
            Configuration::updateValue('ADVEG_REC_V2_INV_KEY', $value, false, $groupID, $shopID);

            $value = Configuration::get('ADVEG_REC_INV_SECRET', null, $groupID, $shopID) ?: '';
            Configuration::updateValue('ADVEG_REC_V2_INV_SECRET', $value, false, $groupID, $shopID);

            $filters = Configuration::get('ADVEG_GUARD_USES_FILTERS', null, $groupID, $shopID);
            if ($filters === '1') {
                $value = Configuration::get('ADVEG_GUARD_BANNED_EMAILS', null, $groupID, $shopID) ?: '[]';
                Configuration::updateValue('ADVEG_FORBIDDEN_EMAILS', $value, false, $groupID, $shopID);

                $value = Configuration::get('ADVEG_GUARD_BANNED_DOMAINS', null, $groupID, $shopID) ?: '[]';
                Configuration::updateValue('ADVEG_FORBIDDEN_EMAIL_DOMAINS', $value, false, $groupID, $shopID);

                $value = Configuration::get('ADVEG_GUARD_BANNED_PATTERNS', null, $groupID, $shopID) ?: '[]';
                Configuration::updateValue('ADVEG_FORBIDDEN_EMAIL_PATTERNS', $value, false, $groupID, $shopID);
            } else {
                Configuration::updateValue('ADVEG_FORBIDDEN_EMAILS', '[]', false, $groupID, $shopID);
                Configuration::updateValue('ADVEG_FORBIDDEN_EMAIL_DOMAINS', '[]', false, $groupID, $shopID);
                Configuration::updateValue('ADVEG_FORBIDDEN_EMAIL_PATTERNS', '[]', false, $groupID, $shopID);
            }

            $mFilters = Configuration::get('ADVEG_MSG_GUARD_ENABLED', null, $groupID, $shopID);
            if ($mFilters === '1') {
                $value = Configuration::get('ADVEG_MSG_GUARD_BANNED_PHRASES', null, $groupID, $shopID) ?: '[]';
                Configuration::updateValue('ADVEG_FORBIDDEN_TEXTS', $value, false, $groupID, $shopID);
            } else {
                Configuration::updateValue('ADVEG_FORBIDDEN_TEXTS', '[]', false, $groupID, $shopID);
            }

            $mvEnabled = (bool) $mFilters;
            $evEnabled = (bool) Configuration::get('ADVEG_GUARD_ENABLED', null, $groupID, $shopID);
            $rvEnabled = (bool) Configuration::get('ADVEG_REC_ENABLED', null, $groupID, $shopID);
            foreach ($forms as $form) {
                $name = $form;
                if ($name === 'PRODUCT_REVIEWS') {
                    $name = 'WRITE_REVIEW';
                } elseif ($name === 'STOCK_ALERT') {
                    $name = 'NOTIFY_WHEN_IN_STOCK';
                } elseif ($name === 'CHECKOUT') {
                    $name = 'QUICK_ORDER';
                }
                $newConfig = 'ADVEG_' . $name . '_OPTS';
                $values = array(
                    'emailValidation' => false,
                    'messageValidation' => false,
                    'recaptchaValidation' => false,
                    'recaptchaSize' => 'normal',
                    'recaptchaAlign' => 'left',
                    'recaptchaOffset' => 1,
                );

                if ($evEnabled && (bool) Configuration::get('ADVEG_GUARD_' . $form, null, $groupID, $shopID)) {
                    $values['emailValidation'] = true;
                }
                if ($mvEnabled && (bool) Configuration::get('ADVEG_MSG_GUARD_' . $form, null, $groupID, $shopID)) {
                    $values['messageValidation'] = true;
                }
                if ($rvEnabled && (bool) Configuration::get('ADVEG_REC_' . $form, null, $groupID, $shopID)) {
                    $values['recaptchaValidation'] = true;
                }

                $size = Configuration::get('ADVEG_REC_' . $form . '_SIZE', null, $groupID, $shopID);
                if ($size !== false) {
                    $values['recaptchaSize'] = $size;
                }
                $align = Configuration::get('ADVEG_REC_' . $form . '_ALIGN', null, $groupID, $shopID);
                if ($align !== false) {
                    if ($align === 'indent') {
                        $values['recaptchaAlign'] = 'offset';
                    } else {
                        $values['recaptchaAlign'] = $align;
                    }
                }
                $offset = Configuration::get('ADVEG_REC_' . $form . '_INDENT', null, $groupID, $shopID);
                if ($offset !== false) {
                    $values['recaptchaOffset'] = (int) $offset;
                }

                Configuration::updateValue($newConfig, json_encode($values), false, $groupID, $shopID);
            }
        }
    }

    $deleteConfigs = array(
        'ADVEG_HELLO_DISMISSED',
        'ADVEG_GUARD_USES_FILTERS',
        'ADVEG_REC_KEY', 'ADVEG_REC_SECRET',
        'ADVEG_REC_INV_KEY', 'ADVEG_REC_INV_SECRET', 'ADVEG_REC_THRESHOLD',
        'ADVEG_GUARD_ENABLED', 'ADVEG_MSG_GUARD_ENABLED', 'ADVEG_REC_ENABLED',
        'ADVEG_GUARD_BANNED_EMAILS', 'ADVEG_GUARD_BANNED_DOMAINS', 'ADVEG_GUARD_BANNED_PATTERNS',
        'ADVEG_MSG_GUARD_BANNED_PHRASES',
    );
    foreach ($forms as $form) {
        $deleteConfigs[] = 'ADVEG_GUARD_' . $form;
        $deleteConfigs[] = 'ADVEG_MSG_GUARD_' . $form;
        $deleteConfigs[] = 'ADVEG_REC_' . $form;
        $deleteConfigs[] = 'ADVEG_REC_' . $form . '_SIZE';
        $deleteConfigs[] = 'ADVEG_REC_' . $form . '_ALIGN';
        $deleteConfigs[] = 'ADVEG_REC_' . $form . '_INDENT';
    }
    foreach ($deleteConfigs as $config) {
        Configuration::deleteByName($config);
    }

    $newConfigs = array(
        'ADVEG_REC_LEGAL_LINKS' => false,
        'ADVEG_CHECK_EMAIL_DISPOSABLE' => true,
        'ADVEG_PREVIEW_MODE' => false,
        'ADVEG_PREVIEW_MODE_IPS' => '[]',
        'ADVEG_LOGIN_OPTS' => '[]',
        'ADVEG_RESET_PASSWORD_OPTS' => '[]',
    );
    foreach ($newConfigs as $config => $value) {
        if (is_bool($value)) {
            $value = (int) $value;
        }
        Configuration::updateValue($config, $value, false, 0, 0); // Global context only.
    }

    return true;
}
