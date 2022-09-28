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

if (!class_exists('PFGModel')) {
    /**
     * Class PFGModel
     */
    class PFGModel extends ObjectModel
    {
        public $send_mail_to;
        public $action_sender;
        public $action_admin;
        public $title;
        public $subject_sender;
        public $subject_admin;
        public $active;
        public $is_only_connected;
        public $unauth_redirect_url;
        public $accessible;
        public $header;
        public $footer;
        public $success;
        public $send_label;
        public $message_sender;
        public $message_admin;
        public $recaptcha_public;
        public $recaptcha_private;

        public static $definition = array (
            'table' => 'pfg',
            'primary' => 'id_pfg',
            'multilang' => true,
            'fields' => array (
                'send_mail_to'        => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 200),
                'action_sender'       => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 200),
                'action_admin'        => array ('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'active'              => array ('type' => self::TYPE_BOOL,   'validate' => 'isBool',   'required' => true),
                'is_only_connected'   => array ('type' => self::TYPE_BOOL,   'validate' => 'isBool',   'required' => true),
                'unauth_redirect_url' => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'size' => 255),
                'accessible'          => array ('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt'),
                'title'               => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'required' => true),
                'subject_sender'      => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'size' => 255),
                'subject_admin'       => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'size' => 255),
                'header'              => array ('type' => self::TYPE_HTML,   'validate' => 'isString', 'lang' => true, 'required' => false),
                'footer'              => array ('type' => self::TYPE_HTML,   'validate' => 'isString', 'lang' => true, 'required' => false),
                'success'             => array ('type' => self::TYPE_HTML,   'validate' => 'isString', 'lang' => true, 'required' => false),
                'send_label'          => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'size' => 255),
                'message_sender'      => array ('type' => self::TYPE_HTML,   'validate' => 'isString', 'lang' => true, 'required' => false),
                'message_admin'       => array ('type' => self::TYPE_HTML,   'validate' => 'isString', 'lang' => true, 'required' => false),
                'recaptcha_public'    => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 200),
                'recaptcha_private'   => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 200),
            ),
        );

        /**
         * Add the model in the database and returns it
         *
         * @param boolean $autodate
         * @param boolean $null_values
         *
         * @return object
         */
        public function add($autodate = true, $null_values = false)
        {
            foreach ($this->success as $key => $value) {
                if (Tools::substr($this->success[$key], 0, 7) === '<p>http') {
                    $this->success[$key] = strip_tags($value);
                }
            }

            $res = parent::add($autodate, $null_values);

            switch (Shop::getContext()) {
                case Shop::CONTEXT_GROUP:
                    $list_shop_ids = Shop::getShops(false, Shop::getContextShopGroupID(), true);
                    foreach ($list_shop_ids as $id_shop) {
                        $res &= Db::getInstance()->execute(
                            'INSERT INTO `'._DB_PREFIX_.'pfg_shop` (`id_shop`, `id_pfg`)
                            VALUES('.pSQL((int)$id_shop).', '.pSQL((int)$this->id).')'
                        );
                    }
                    break;
                case Shop::CONTEXT_SHOP:
                case Shop::CONTEXT_ALL:
                    $id_shop = (int)Shop::getContextShopID();

                    $res &= Db::getInstance()->execute(
                        'INSERT INTO `'._DB_PREFIX_.'pfg_shop` (`id_shop`, `id_pfg`)
                        VALUES('.pSQL((int)$id_shop).', '.pSQL((int)$this->id).')'
                    );
                    break;
            }

            return $res;
        }

        public function update($null_values = false)
        {
            foreach ($this->success as $key => $value) {
                if (Tools::substr($this->success[$key], 0, 7) === '<p>http') {
                    $this->success[$key] = strip_tags($value);
                }
            }

            return parent::update($null_values);
        }

        /**
         * Delete the instance in the database
         *
         * @return boolean
         */
        public function delete()
        {
            $res = true;
            $res &= Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'pfg_shop`
                WHERE `id_pfg` = '.pSQL((int)$this->id)
            );
            $res &= parent::delete();
            return $res;
        }
    }
}
