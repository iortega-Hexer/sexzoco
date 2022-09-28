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

function upgrade_module_2_5_7($object)
{
    // Update database :)
    try {
        Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'pfg_fields` CHANGE `class` `classname` VARCHAR(255) NULL DEFAULT NULL;'
        );
    } catch (Exception $e) {
        return false;
    }

    return true;
}
