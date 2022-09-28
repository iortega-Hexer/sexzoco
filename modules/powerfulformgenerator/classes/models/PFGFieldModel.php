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

if (!class_exists('PFGFieldModel')) {
    /**
     * Class ContactFormFieldModel
     */
    class PFGFieldModel extends ObjectModel
    {
        public $id_pfg;
        public $type;
        public $name;
        public $label;
        public $values;
        public $required;
        public $classname;
        public $style;
        public $extra;
        public $related;
        public $position;

        public static $definition = array (
            'table' => 'pfg_fields',
            'primary' => 'id_field',
            'multilang' => true,
            'fields' => array (
                'id_pfg'     => array ('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt', 'required' => true),
                'type'       => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true),
                'name'       => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
                'label'      => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'size' => 255, 'required' => false),
                'values'     => array ('type' => self::TYPE_HTML,   'validate' => 'isString', 'lang' => true, 'size' => 4096, 'required' => false),
                'required'   => array ('type' => self::TYPE_BOOL,   'validate' => 'isBool', 'required' => true),
                'classname'  => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
                'style'      => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
                'extra'      => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
                'related'    => array ('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
                'position'   => array ('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt'),
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
            $object = parent::add($autodate, $null_values);
            $this->position = PFGFieldModel::getNextAvailablePosition($this->id_pfg);

            $this->update();

            return $object;
        }

        /**
         * Update the position of this element in the database.
         *
         * @param integer $way Way of positionning
         * @param integer $new_position The new position
         *
         * @return object or false
         */
        public function updatePosition($way, $new_position)
        {
            $db = Db::getInstance();
            $count = $db->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.self::$definition['table'].' WHERE id_pfg = '.pSQL((int)$this->id_pfg), false);
            if (($new_position >= 1) && ($new_position <= $count)) {
                $old_position = $way ? ($new_position - 1) : ($new_position + 1);

                if (($old_position >= 1) && ($old_position <= $count)) {
                    $sql = implode(
                        ';',
                        array(
                            'UPDATE '._DB_PREFIX_.self::$definition['table'].' SET position = 0 WHERE position = '.pSQL((int)$new_position).' AND id_pfg='.pSQL((int)$this->id_pfg),
                            'UPDATE '._DB_PREFIX_.self::$definition['table'].' SET position = '.pSQL((int)$new_position).' WHERE position = '.pSQL((int)$old_position).
                                ' AND id_pfg='.pSQL((int)$this->id_pfg),
                            'UPDATE '._DB_PREFIX_.self::$definition['table'].' SET position = '.pSQL((int)$old_position).' WHERE position = 0 AND id_pfg='.pSQL((int)$this->id_pfg)
                        )
                    );

                    // Both old and new positions are valid, we switch them
                    return $db->execute($sql);
                }
            }

            return false;
        }

        /**
         * Update the position of a specific field
         *
         * @param integer $id_pfg ID of the PFG model
         * @param integer $id_field ID of the PFG Field model
         * @param integer $position Position of that field
         *
         * @return boolean
         */
        public static function updatePositionField($id_pfg, $id_field, $position)
        {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('UPDATE '._DB_PREFIX_.self::$definition['table'].' SET position = '.pSQL((int)$position).' WHERE id_pfg = '.pSQL((int)$id_pfg).' AND id_field = '.pSQL((int)$id_field));
        }

        /**
         * Delete the instance in the database
         *
         * @return boolean
         */
        public function delete()
        {
            $position = $this->position;

            if ($result = parent::delete()) {
                Db::getInstance()->execute(
                    'UPDATE '._DB_PREFIX_.self::$definition['table'].
                    ' SET position = position-1 WHERE position > '.pSQL((int)$position).' AND id_pfg = '.pSQL($this->id_pfg)
                );
            }

            return $result;
        }

        /**
         * Retrieve the fields from the given PFG model id
         *
         * @param integer $id_pfg ID of the PFG model
         *
         * @return array or false
         */
        public static function findFields($id_pfg)
        {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT f.type, f.name, f.required, f.classname, f.style, f.extra, f.related, fl.values, fl.label
                FROM `'._DB_PREFIX_.'pfg_fields` f
                LEFT JOIN `'._DB_PREFIX_.'pfg_fields_lang` fl ON f.id_field = fl.id_field
                WHERE `id_pfg` = '.pSQL((int)$id_pfg).' AND fl.id_lang = '.pSQL((int)Context::getContext()->language->id).' ORDER BY f.position'
            );
        }

        /**
         * Return the next available position
         *
         * @param integer $id_pfg ID of the PFG model
         *
         * @return integer
         */
        public static function getNextAvailablePosition($id_pfg)
        {
            $sql = 'SELECT position FROM '._DB_PREFIX_.self::$definition['table'].' WHERE id_pfg = '.pSQL((int)$id_pfg).' ORDER BY position DESC';

            $position = (int)Db::getInstance()->getValue($sql, false);
            return $position + 1;
        }

        /**
         * Indicates if the requested name is available in the database or not
         *
         * @param string $name Name to lookup for
         * @param integer $id_pfg ID of the PFG model
         * @param integer $id_field ID of the PFG Field model
         *
         * @return boolean
         */
        public static function isNameAlreadyTaken($name, $id_pfg, $id_field = null)
        {
            if ($id_field) {
                $query = 'SELECT name FROM `'._DB_PREFIX_.'pfg_fields` WHERE id_field != '.pSQL((int)$id_field).' AND id_pfg = '.pSQL((int)$id_pfg).' AND name = "'.pSQL($name).'"';
            } else {
                $query = 'SELECT name FROM `'._DB_PREFIX_.'pfg_fields` WHERE id_pfg = '.pSQL((int)$id_pfg).' AND name = "'.pSQL($name).'"';
            }

            return count(Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query)) > 0;
        }
    }
}
