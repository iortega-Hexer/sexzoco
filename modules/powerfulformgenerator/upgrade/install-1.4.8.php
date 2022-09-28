<?php
/**
 * Powerful Form Generator
 *
 * This modules aims to provide for your customer any kind of form you want.
 *
 * If you find errors, bugs or if you want to share some improvments,
 * feel free to contact at contact@prestaddons.net ! :)
 * Si vous trouvez des erreurs, des bugs ou si vous souhaitez
 * tout simplement partager un conseil ou une amélioration,
 * n'hésitez pas à me contacter à contact@prestaddons.net
 *
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) April 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @license   Nicodème Cyril
 * @package   modules
 * @since     2014-04-15
 * @version   2.7.9
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_8($object)
{
    // Update database and add hooks :)
    return Db::getInstance()->execute(
        'ALTER TABLE `'._DB_PREFIX_.'pfg`
        ADD COLUMN `is_only_connected` tinyint(1) NOT NULL DEFAULT 0 AFTER `active`,
        ADD COLUMN `accessible` tinyint(1) NOT NULL DEFAULT 1 AFTER `is_only_connected`;'
    ) && $object->registerHook('displayPowerfulForm') && $object->registerHook('displayHeader');
}
