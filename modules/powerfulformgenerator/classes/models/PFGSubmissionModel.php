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

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

if (!class_exists('PFGSubmissionModel')) {
    /**
     * Class ContactFormSubmissionModel
     */
    class PFGSubmissionModel extends ObjectModel
    {
        public $id_pfg;
        public $entry;
        public $date_add;

        public static $definition = array (
            'table' => 'pfg_submissions',
            'primary' => 'id_submission',
            'fields' => array (
                'id_pfg'       => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt'),
                'entry'        => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'date_add'     => array('type' => self::TYPE_DATE,   'validate' => 'isDate'),
            ),
        );
    }
}
