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

/* http://doc.prestashop.com/pages/viewpage.action?pageId=15171738 */
require_once(dirname(__FILE__).'/classes/models/PFGModel.php');
require_once(dirname(__FILE__).'/classes/PFGRenderer.php');

class PowerfulFormGenerator extends Module
{
    const INSTALL_SQL_FILE   = 'install.sql';
    const UNINSTALL_SQL_FILE = 'uninstall.sql';

    /**
     * This setups the module with the required informations
     * for the needs of Prestashop back and front office.
     */
    public function __construct()
    {
        $this->name = 'powerfulformgenerator';
        $this->tab = 'content_management';
        $this->version = '2.7.9';
        $this->author = 'Cyril Nicodeme';
        $this->need_instance = 0;
        $this->module_key = '4a42249dce8de8e336494fd07a6e501f';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->_trans('Powerful Form Generator');
        $this->description = $this->_trans('Create multiple generic forms, the easy way.');

        $this->confirmUninstall = $this->_trans('Are you sure you want to uninstall?');

        if (!is_dir(_PS_ROOT_DIR_.'/upload') || !is_writable(_PS_ROOT_DIR_.'/upload')) {
            $this->warning = $this->_trans('Your /upload/ folder must be writeable if you plan to use file upload.');
        }

        PFGRenderer::setModuleId($this->id);
        switch (Tools::substr(_PS_VERSION_, 0, 3)) {
            case '1.5':
                PFGRenderer::setTemplatePath($this->getTemplatePath('views/templates/front/form.tpl'));
                break;
            case '1.6':
                PFGRenderer::setTemplatePath($this->getTemplatePath('form16.tpl'));
                break;
            case '1.7':
                PFGRenderer::setTemplatePath('module:powerfulformgenerator/views/templates/front/form17.tpl');
                break;
        }
    }

    /**
     * Used for backward compatibility between 1.7 Prestashop version and older
     */
    private function _trans($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($class === null || $class == 'AdminTab') {
            $class = Tools::substr(get_class($this), 0, -10);
        } elseif (Tools::strtolower(Tools::substr($class, -10)) == 'controller') {
            /* classname has changed, from AdminXXX to AdminXXXController, so we remove 10 characters and we keep same keys */
            $class = Tools::substr($class, 0, -10);
        }

        if (version_compare(Tools::substr(_PS_VERSION_, 0, 7), '1.7.0.3', '>=')) {
            return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
        } else {
            return $this->l($string, $class, $addslashes, $htmlentities);
        }
    }

    /**
     * This will install this module
     * By adding all the required tables in the database,
     * the hooks and calling the parent install method.
     *
     * @return boolean
     *
     * @see Module::install
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()) {
            return false;
        }

        if (!$this->registerHook('displayAdminPFGSubmissionsListBefore')) {
            return false;
        }
        if (!$this->registerHook('displayAdminPFGListBefore')) {
            return false;
        }
        if (!$this->registerHook('displayHeader')) {
            return false;
        }
        if (!$this->registerHook('displayPowerfulForm')) {
            return false;
        }
        if (!$this->registerHook('registerGDPRConsent')) {
            return false;
        }
        if (!$this->registerHook('actionDeleteGDPRCustomer')) {
            return false;
        }
        if (!$this->registerHook('actionExportGDPRData')) {
            return false;
        }

        if (!$this->installTable()) {
            return false;
        }
        if (!$this->installModuleTab('AdminPFG', 'Powerful Form Generator')) {
            return false;
        }
        if (!$this->installModuleTab('AdminPFGFields', 'PFG Fields', 'AdminPFG', false)) {
            return false;
        }
        if (!$this->installModuleTab('AdminPFGSubmissions', 'PFG Submissions', 'AdminPFG', false)) {
            return false;
        }

        return true;
    }

    /**
     * This will uninstall this module
     * By removing all the tables, the hooks and calling the parent uninstall method.
     *
     * @return boolean
     *
     * @see Module::uninstall
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        if (!$this->unregisterHook('displayAdminPFGSubmissionsListBefore')) {
            return false;
        }
        if (!$this->unregisterHook('displayAdminPFGListBefore')) {
            return false;
        }
        if (!$this->unregisterHook('displayHeader')) {
            return false;
        }
        if (!$this->unregisterHook('displayPowerfulForm')) {
            return false;
        }
        if (!$this->unregisterHook('registerGDPRConsent')) {
            return false;
        }
        if (!$this->unregisterHook('actionDeleteGDPRCustomer')) {
            return false;
        }
        if (!$this->unregisterHook('actionExportGDPRData')) {
            return false;
        }

        if (!$this->uninstallModuleTab('AdminPFG')) {
            return false;
        }
        if (!$this->uninstallModuleTab('AdminPFGFields')) {
            return false;
        }
        if (!$this->uninstallModuleTab('AdminPFGSubmissions')) {
            return false;
        }

        if (!$this->uninstallTable()) {
            return false;
        }

        return true;
    }

    /**
     * This will install all the required tables for this module
     * Using the install.sql file located in db/
     *
     * @return boolean
     */
    public function installTable()
    {
        if (!file_exists(dirname(__FILE__).'/db/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/db/'.self::INSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace('ps_', _DB_PREFIX_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if (!empty($query)) {
                if (!Db::getInstance()->Execute(trim($query))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * This will uninstall all the created database tables
     * Using the uninstall.sql file located in db/
     *
     * @return boolean
     */
    public function uninstallTable()
    {
        if (!file_exists(dirname(__FILE__).'/db/'.self::UNINSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/db/'.self::UNINSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace('ps_', _DB_PREFIX_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if (!Db::getInstance()->Execute(trim($query))) {
                return false;
            }
        }

        return true;
    }

    /**
     * This will add a new Tab on the Back office menu
     *
     * @param string $tab_class Class name of the Tab
     * @param string $title Title of the tab (name)
     * @param string $tab_parent Tab parent on where to put the newly created tab
     * @param boolean $active (default true) Indicate weither this new table is active or not
     *
     * @return boolean
     */
    private function installModuleTab($tab_class, $title, $tab_parent = null, $active = true)
    {
        if (is_null($tab_parent)) {
            if (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '>=')) {
                $tab_parent = 'AdminParentThemes';
            } else {
                $tab_parent = 'AdminParentModules';
            }
        }

        $id_tab_parent = Tab::getIdFromClassName($tab_parent);

        $languages = Language::getLanguages(true);
        $names = array();
        foreach ($languages as $language) {
            $names[$language['id_lang']] = $title;
        }

        $tab = new Tab();
        $tab->name = $names;
        $tab->class_name = $tab_class;
        $tab->module = $this->name;
        $tab->id_parent = $id_tab_parent;
        $tab->active = $active;

        if (!$tab->save()) {
            return false;
        }

        return true;
    }

    /**
     * This will remove the Tab on the Back office menu
     *
     * @param string $tab_class Class name of the Tab
     *
     * @return boolean
     */
    private function uninstallModuleTab($tab_class)
    {
        $id_tab = Tab::getIdFromClassName($tab_class);

        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }

        return false;
    }

    /**
     * Show the "Configure" button in the module page
     * This is a shortcut to the link in the menu
     */
    public function getContent()
    {
        $link = new Link();
        return Tools::redirectAdmin($link->getAdminLink('AdminPFG'));
    }

    /**
     * This will display the informations to help the user
     * on how to add fields to his new form.
     */
    public function hookDisplayAdminPFGListBefore()
    {
        return $this->display(__FILE__, '/views/templates/admin/header_help.tpl');
    }

    /**
     * Message displayed before the list of submissions
     * This will indicate to the user how to view his form
     * And also display informations about the state of the form.
     */
    public function hookDisplayAdminPFGSubmissionsListBefore()
    {
        $form = new PFGModel(Tools::getValue('id_pfg'));

        $url = $this->context->link->getModuleLink('powerfulformgenerator', 'display');

        if (strpos($url, '?', strrpos($url, '/') + 1) === false) {
            $url .= '?id=';
        } else {
            $url .= '&id=';
        }

        $url .= $form->id;

        $fields_url = $this->context->link->getAdminLink('AdminPFGFields');
        $fields_url .= '&id_pfg='.$form->id;

        $this->context->smarty->assign(
            array(
                'id' => $form->id,
                'url' => $url,
                'url_cms' => $this->context->link->getAdminLink('AdminMeta'),
                'active' => ($form->active === '1') ? true : false,
                'show_url' => ($form->accessible === '0' || $form->accessible === '1') ? true : false,
                'show_hook' => ($form->accessible === '0' || $form->accessible === '2') ? true : false,
                'fields_url' => $fields_url,
                'cms_pages_url' => $this->context->link->getAdminLink('AdminCmsContent')
            )
        );

        return $this->display(__FILE__, '/views/templates/admin/header_url.tpl');
    }

    /**
     * Add required files for the form to be displayed correctly.
     */
    public function hookDisplayHeader()
    {
        if (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '>=')) {
            Context::getContext()->controller->addCSS(__PS_BASE_URI__.'modules/powerfulformgenerator/views/css/contact-form-enhanced.css');
        }
        if (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '>=')) {
            Context::getContext()->controller->addCSS(__PS_BASE_URI__.'modules/powerfulformgenerator/views/css/contact-form-1.7.css');
        }

        Context::getContext()->controller->addCSS(_THEME_CSS_DIR_.'contact-form.css');
        Context::getContext()->controller->addJqueryUI('ui.datepicker');
        Context::getContext()->controller->addJS(__PS_BASE_URI__.'modules/powerfulformgenerator/views/js/contact-form-enhanced.js');
    }

    /**
     * Call the PFGRenderer class which will generate the form,
     *     perform the validations and process the submit
     *     depending on the state of the request.
     */
    public function hookDisplayPowerfulForm($params)
    {
        try {
            if (!isset($params['id']) || !is_numeric($params['id'])) {
                throw new Exception('Missing "id" parameter for hook "displayPowerfulForm"');
            }

            $renderer = new PFGRenderer($params['id']);

            if (!$renderer->isAllowed()) {
                return;
            }

            if (isset($params['type'])) {
                if ($params['type'] === 'url' && $renderer->getForm()->accessible === '2') {
                    return;
                }
            } else { // On considère que c'est par hook pure
                if ($renderer->getForm()->accessible === '1') {
                    return;
                }
            }

            return $renderer->displayForm();
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                throw $e;
            } else {
                // TODO Improve this render
                exit($e->getMessage());
            }
        }
    }

    private function _getEmailSearchString($customer)
    {
        $email = null;
        if (is_string($customer) && !Tools::isEmpty($customer) && Validate::isEmail($customer['email'])) {
            $email = $customer;
        }
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
            $email = $customer['email'];
        }

        if (!is_null($email)) {
            return '%:"'.$email.'"%';
        }

        return null;
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        $qs = $this->_getEmailSearchString($customer);
        if (is_null($qs)) {
            return;
        }

        $sql = "DELETE FROM "._DB_PREFIX_."pfg_submissions WHERE entry LIKE '".pSQL($qs)."'";
        if (Db::getInstance()->execute($sql)) {
            return json_encode(true);
        }

        return json_encode($this->l('PowerfulFormGenerator : Unable to delete customer using email.'));
    }

    public function hookActionExportGDPRData($customer)
    {
        $qs = $this->_getEmailSearchString($customer);
        if (is_null($qs)) {
            return;
        }
        $sql = "SELECT entry FROM "._DB_PREFIX_."pfg_submissions WHERE entry LIKE '".pSQL($qs)."'";
        if ($res = Db::getInstance()->ExecuteS($sql)) {
            $result = array();
            foreach ($res as $r) {
                $entry = json_decode($r['entry'], true);

                foreach ($entry as $k=>$v) {
                    if (is_array($v)) {
                        $entry[$k] = implode(', ', $v);
                    }
                }
                $result[] = $entry;
            }

            return json_encode($result);
        }
        return json_encode($this->l('PowerfulFormGenerator : Unable to export customer using email.'));
    }
}
