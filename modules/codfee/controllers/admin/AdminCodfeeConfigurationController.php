<?php
/**
* Cash On Delivery With Fee
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2017 idnovate
*  @license   See above
*/

class AdminCodfeeConfigurationController extends ModuleAdminController
{
    protected $delete_mode;
    protected $_defaultOrderBy = 'position';
    protected $_defaultOrderWay = 'ASC';
    protected $can_add_codfeeconf = true;
    protected $top_elements_in_list = 4;
    protected $_default_pagination = 25;
    protected $position_identifier = 'id_codfee_configuration';
    protected $statuses_array = array();
    protected $carriers_array = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'codfee_configuration';
        $this->className = 'CodfeeConfiguration';
        $this->tabClassName = 'AdminCodfeeConfiguration';
        $this->module_name = 'codfee';

        parent::__construct();

        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->_orderBy = $this->_defaultOrderBy;
        $this->_orderWay = $this->_defaultOrderWay;
        $this->show_toolbar = true;
        $this->allow_export = true;
        $this->imageType = 'png';
        $this->identifier = 'id_codfee_configuration';

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $this->meta_title[] = $this->l('Cash on delivery with fee configuration');
        } else {
            $this->meta_title = $this->l('Cash on delivery with fee configuration');
        }
        $this->tpl_list_vars['title'] = $this->l('List of cash on delivery fee configurations');
        $this->taxes_included = (Configuration::get('PS_TAX') == '0' ? false : true);

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;

        $this->fieldImageSettings = array(
            'name' => 'logo',
            'dir' => 'tmp',
        );

        $this->tpl_vars = array(
            'icon' => 'icon-bars',
        );
        $this->context->smarty->assign($this->tpl_vars);

        $this->fields_list = array(
            /*'id_codfee_configuration' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'filter_key' => 'a!name'
            ),*/
            'logo' => array(
                'title' => $this->l('Payment image'),
                'image' => 'tmp',
                'orderby' => false,
                'search' => false,
                'align' => 'center',
            ),
            'payment_name' => array(
                'title' => $this->l('Payment method text'),
                'filter_key' => 'a!payment_name',
                'callback' => 'getPaymentMethodText',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'callback' => 'printActiveIcon'
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'callback' => 'getCodfeeType',
                'align' => 'text-center'
            ),
            'amount_calc' => array(
                'title' => $this->l('Base calc'),
                'callback' => 'getAmountCalcType',
                'align' => 'text-center'
            ),
            'fix' => array(
                'title' => $this->l('Fix'),
                'callback' => 'getFeeForList',
                'align' => 'text-center'
            ),
            'percentage' => array(
                'title' => $this->l('Percentage'),
                'callback' => 'getFeeForList',
                'align' => 'text-center'
            ),
            /*
            'min' => array(
                'title' => $this->l('Minimum fee'),
                'align' => 'text-center'
            ),
            'max' => array(
                'title' => $this->l('Maximum fee'),
                'align' => 'text-center'
            ),
            'groups' => array(
                'title' => $this->l('Group(s)'),
                'callback' => 'getCustomerGroups',
                'align' => 'text-center'
            ),
            'carriers' => array(
                'title' => $this->l('Carrier(s)'),
                'callback' => 'getCarriers',
                'align' => 'text-center'
            ),
            'countries' => array(
                'title' => $this->l('Country(s)'),
                'callback' => 'getCountries',
                'align' => 'text-center'
            ),
            'zones' => array(
                'title' => $this->l('Zone(s)'),
                'callback' => 'getZones',
                'align' => 'text-center'
            ),
            'categories' => array(
                'title' => $this->l('Category(s)'),
                'callback' => 'getCategories',
                'align' => 'text-center'
            ),
            'show_conf_page' => array(
                'title' => $this->l('Show conf page'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printShowConfPageIcon',
                'filter_key' => 'a!show_conf_page'
            ),
            'free_on_freeshipping' => array(
                'title' => $this->l('Free on free shipping'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printFreeOnFreeShippingIcon',
                'filter_key' => 'a!free_on_freeshipping'
            ),
            'hide_first_order' => array(
                'title' => $this->l('Hide on first order'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printHideFirstOrderIcon',
                'filter_key' => 'a!hide_first_order'
            ),
            'only_stock' => array(
                'title' => $this->l('Only stock'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printOnlyStockIcon',
                'filter_key' => 'a!only_stock'
            ),
            'round' => array(
                'title' => $this->l('Round'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printRoundIcon',
                'filter_key' => 'a!round'
            ),
            'show_productpage' => array(
                'title' => $this->l('Product page'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printShowProductPageIcon',
                'filter_key' => 'a!show_productpage'
            ),*/
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position'
            ),
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            unset($this->fields_list['show_conf_page']);
        }

        if (version_compare(_PS_VERSION_, '1.8', '>=')) {
            unset($this->fields_list['logo']);
        }

        $this->shopLinkType = 'shop';

        if (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)) {
            $this->can_add_codfeeconf = false;
        }

        $this->setFilters();

        if (Tools::isSubmit('submitFilter'.$this->module->name.'orders') && (int)Tools::getValue('submitFilter'.$this->module->name.'orders') == 0) {
            $this->action = 'reset_filters';
        }
    }

    public function setMedia($isNewTheme = false)
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->addJs($this->module->getLocalPath().'views/js/back.js');
            return parent::setMedia($isNewTheme);
        } else {
            parent::setMedia($isNewTheme);
            if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                if ($this->display) {
                    $this->context->controller->addJS($this->module->getLocalPath().'views/js/tabs.js');
                }
            }
            $this->context->controller->addJS($this->module->getLocalPath().'views/js/back.js');
            $this->addJqueryPlugin(array('typewatch', 'fancybox', 'autocomplete'));
            $this->addJqueryUI('ui.button');
            $this->addJqueryUI('ui.sortable');
            $this->addJqueryUI('ui.droppable');
            $this->context->controller->addCSS($this->module->getLocalPath().'views/css/back.css', 'all');
            Media::addJsDef(array(
                'AdminCodfeeAjaxController' => $this->context->link->getAdminLink('AdminCodfeeAjax')
            ));
        }
    }

    public function initContent()
    {
        if (Tools::isSubmit('submitResetorder')) {
            Tools::redirectAdmin('index.php?controller=' . $this->tabClassName . '&token=' . Tools::getAdminTokenLite($this->tabClassName));
        }
        if ($this->action == 'select_delete') {
            $this->context->smarty->assign(array(
                'delete_form' => true,
                'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
                'boxes' => $this->boxes,
            ));
        }
        if (!$this->can_add_codfeeconf && !$this->display) {
            $this->informations[] = $this->l('You have to select a shop if you want to create a new fee.');
        }
        parent::initContent();
        if (Tools::isSubmit('exportcodfeeorders')) {
            $collection = $this->getCodFeeOrdersList(true);
            if (count($collection) == 0) {
                $this->warnings[] = $this->l('Cash on delivery with fee orders list empty.');
            } else {
                die($this->downloadCsv($collection, date('Ymd_hi').'_'.$this->module->name.'orders.csv'));
            }
        }
        if (Tools::getValue('action') == 'updatePositions') {
            $this->_updatePositions(Tools::getValue('codfee_configuration'));
        }
        if ($this->action != 'new' && !Tools::isSubmit('updatecodfee_configuration')) {
            $this->content .= $this->getCodFeeOrdersList();
        }
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $codfee = new Codfee();
            $this->context->smarty->assign(array(
                'this_path'     => $this->module->getPathUri(),
                'support_id'    => $codfee->addons_id_product
            ));

            $available_iso_codes = array('en', 'es');
            $default_iso_code = 'en';
            $template_iso_suffix = in_array($this->context->language->iso_code, $available_iso_codes) ? $this->context->language->iso_code : $default_iso_code;
            $this->content .= $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/company/information_'.$template_iso_suffix.'.tpl');
        }
        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function init()
    {
        parent::init();
        //parent::initBreadcrumbs(Tab::getIdFromClassName('CodfeeConfiguration'));
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if (!$this->can_add_codfeeconf) {
            unset($this->toolbar_btn['new']);
        }
    }

    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
    }


    public function initToolbarTitle()
    {
        parent::initToolbarTitle();
        switch ($this->display) {
            case '':
            case 'list':
                array_pop($this->toolbar_title);
                $this->toolbar_title[] = $this->l('Manage Cash On Delivery With Fee Configuration');
                break;
            case 'view':
                if (($codfeeconf = $this->loadObject(true)) && Validate::isLoadedObject($codfeeconf)) {
                    array_pop($this->toolbar_title);
                    $this->toolbar_title[] = sprintf($this->l('Fee configuration: %s'), $codfeeconf->id_codfee_configuration.' - '.$codfeeconf->name);
                }
                break;
            case 'add':
            case 'edit':
                array_pop($this->toolbar_title);
                if (($codfeeconf = $this->loadObject(true)) && Validate::isLoadedObject($codfeeconf)) {
                    $this->toolbar_title[] = sprintf($this->l('Editing fee configuration: %s'), $codfeeconf->id_codfee_configuration.' - '.$codfeeconf->name);
                } else {
                    $this->toolbar_title[] = $this->l('Creating a new cash on delivery fee configuration');
                }
                break;
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['desc-module-back'] = array(
                'href' => 'index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back'),
                'icon' => 'process-icon-back'
            );
            $this->page_header_toolbar_btn['desc-module-new'] = array(
                'href' => 'index.php?controller='.$this->tabClassName.'&add'.$this->table.'&token='.Tools::getAdminTokenLite($this->tabClassName),
                'desc' => $this->l('New'),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['desc-module-reload'] = array(
                'href' => 'index.php?controller='.$this->tabClassName.'&token='.Tools::getAdminTokenLite($this->tabClassName).'&reload=1',
                'desc' => $this->l('Reload'),
                'icon' => 'process-icon-refresh'
            );
            $this->page_header_toolbar_btn['desc-module-translate'] = array(
                'href' => '#',
                'desc' => $this->l('Translate'),
                'modal_target' => '#moduleTradLangSelect',
                'icon' => 'process-icon-flag'
            );
            $this->page_header_toolbar_btn['desc-module-hook'] = array(
                'href' => 'index.php?tab=AdminModulesPositions&token='.Tools::getAdminTokenLite('AdminModulesPositions').'&show_modules='.Module::getModuleIdByName('codfee'),
                'desc' => $this->l('Manage hooks'),
                'icon' => 'process-icon-anchor'
            );
        }

        if (!$this->can_add_codfeeconf) {
            unset($this->page_header_toolbar_btn['desc-module-new']);
        }
    }

    public function initModal()
    {
        parent::initModal();

        $languages = Language::getLanguages(false);
        $translateLinks = array();

        if (version_compare(_PS_VERSION_, '1.7.2.1', '>=')) {
            $module = Module::getInstanceByName($this->module->name);
            $isNewTranslateSystem = $module->isUsingNewTranslationSystem();
            $link = Context::getContext()->link;
            foreach ($languages as $lang) {
                if ($isNewTranslateSystem) {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslationSf', true, array(
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $module->name,
                        'locale' => $lang['locale'],
                    ));
                } else {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslations', true, array(), array(
                        'type' => 'modules',
                        'module' => $module->name,
                        'lang' => $lang['iso_code'],
                    ));
                }
            }
        }

        $this->context->smarty->assign(array(
            'trad_link' => 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&module='.$this->module->name.'&lang=',
            'module_languages' => $languages,
            'module_name' => $this->module->name,
            'translateLinks' => $translateLinks,
        ));

        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');

        $this->modals[] = array(
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->l('Translate this module'),
            'modal_content' => $modal_content
        );
    }

    public function initProcess()
    {
        parent::initProcess();

        if (Tools::getIsset('reload')) {
            $this->action = 'reset_filters';
        }

        if (Tools::isSubmit('changeActiveVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_active_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('changeShowConfPageVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_show_conf_page_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('changeFreeOnFreeShippingVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_free_on_freeshipping_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('changeHideFirstOrderVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_hide_first_order_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('changeOnlyStockVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_only_stock_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('changeRoundVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_round_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('changeShowProductPageVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_showproductpage_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
    }

    public function renderList()
    {
        if (!CodfeeConfiguration::getNbObjects()) {
            $this->redirect_after = 'index.php?controller='.$this->tabClassName.'&add'.$this->table.'&token='.Tools::getAdminTokenLite($this->tabClassName);
            $this->redirect();
        }

        if ((Tools::isSubmit('submitBulkdelete'.$this->table) || Tools::isSubmit('delete'.$this->table)) && $this->tabAccess['delete'] === '1') {
            $this->tpl_list_vars = array(
                'delete_codfeeconf' => true,
                'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                'POST' => $_POST
            );
        }
        return parent::renderList();
    }

    public function renderOptions()
    {
        return parent::renderOptions();
    }

    public function renderForm()
    {
        if (!($codfeeconf = $this->loadObject(true))) {
            return false;
        }

        $image = _PS_TMP_IMG_DIR_.$this->module->name.'_'.$codfeeconf->id.'.'.$this->imageType;
        $image_url = ImageManager::thumbnail($image, $this->module->name.'_'.(int)$codfeeconf->id.'.'.$this->imageType, 350, $this->imageType, true, false);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $types = $this->getCodfeeTypes();
        $calc_types = $this->getAmountCalcTypes();

        $groups = array();
        $carriers = array();
        $zones = array();
        $countries = array();
        $categories = array();
        $manufacturers = array();
        $suppliers = array();
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $carriers = array_merge($carriers, Carrier::getCarriers($this->context->cookie->id_lang, true, false, false, null, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE));
            $statuses = OrderState::getOrderStates((int)$this->context->cookie->id_lang);
            $groups = array_merge($groups, Group::getGroups($this->context->cookie->id_lang, true));
            $categories = array_merge($categories, Category::getCategories((int)($this->context->cookie->id_lang), false, false));
            $countries = array_merge($countries, Country::getCountries((int)($this->context->cookie->id_lang)));
            $manufacturers = array_merge($manufacturers, Manufacturer::getManufacturers(false, (int)($this->context->cookie->id_lang), false));
            $suppliers = array_merge($suppliers, Supplier::getSuppliers(false, (int)($this->context->cookie->id_lang), false));
        } else {
            $carriers = array_merge($carriers, Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS));
            $statuses = OrderState::getOrderStates((int)$this->context->language->id);
            $groups = array_merge($groups, Group::getGroups($this->context->language->id, true));
            $categories = array_merge($categories, Category::getCategories((int)($this->context->language->id), false, false));
            $countries = array_merge($countries, Country::getCountries((int)($this->context->language->id)));
            $manufacturers = array_merge($manufacturers, Manufacturer::getManufacturers(false, (int)($this->context->language->id), false));
            $suppliers = array_merge($suppliers, Supplier::getSuppliers(false, (int)($this->context->language->id), false));
        }

        $sizes = array('100%' => 'col-md-12', '75%' => 'col-md-9', '50%' => 'col-md-6', '25%' => 'col-md-3');
        $list_sizes = array();
        foreach ($sizes as $key => $size) {
            $list_sizes[$key]['id'] = $size;
            $list_sizes[$key]['value'] = $size;
            $list_sizes[$key]['name'] = $key;
        }

        $this->multiple_fieldsets = true;
        $this->default_form_language = $this->context->language->id;
        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Fee configuration'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'class' => 't',
                    'col' => '3',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Enable or Disable COD Fee Configuration')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'required' => true,
                    'col' => '3',
                    'class' => 'fixed-width-md',
                    'options' => array(
                        'query' => $types,
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => $this->l('-- Choose --')
                        )
                    ),
                    'desc' => $this->l('Type of fee calculation or Cash on pickup option')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Base amount'),
                    'name' => 'amount_calc',
                    'required' => true,
                    'col' => '3',
                    'class' => 'toggle_type fixed-width-md',
                    'options' => array(
                        'query' => $calc_types,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Base amount for fee calculation')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Fix'),
                    'name' => 'fix',
                    'class' => 'toggle_type',
                    'col' => '2',
                    'desc' => sprintf($this->l('Fix fee (by default currency) %s'), ($this->taxes_included) ? $this->l('taxes included') : $this->l('without taxes')),
                    /*'desc' => ($this->taxes_included) ? $this->l('With taxes') : $this->l('Without taxes'),*/
                    'suffix' => (version_compare(_PS_VERSION_, '1.7.6', '>=')) ? $this->context->currency->symbol : $this->context->currency->sign,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Percentage'),
                    'name' => 'percentage',
                    'class' => 'toggle_type',
                    'col' => '2',
                    'desc' => $this->l('Percentage fee'),
                    'suffix' => '%'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Tax rule'),
                    'name' => 'id_tax_rule',
                    'col' => '3',
                    'class' => 'fixed-width-md',
                    'options' => array(
                        'query' => array_merge(TaxRulesGroup::getTaxRulesGroups(), array(
                            array(
                                'id_tax_rules_group' => 9999, 'name' => $this->l('Without taxes')
                            )
                        )),
                        'id' => 'id_tax_rules_group',
                        'name' => 'name',
                        'default' => array(
                            'value' => '0',
                            'label' => $this->l('Apply selected carrier taxes')
                        )
                    ),
                    'desc' => $this->l('COD fee taxes. You can select an specific tax rule or apply the tax rule of selected carrier at checkout')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Minimun fee'),
                    'name' => 'min',
                    'class' => 'toggle_type',
                    'col' => '2',
                    'desc' => $this->l('Minimum fee to add (by default currency)'),
                    'suffix' => (version_compare(_PS_VERSION_, '1.7.6', '>=')) ? $this->context->currency->symbol : $this->context->currency->sign,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum fee'),
                    'name' => 'max',
                    'class' => 'toggle_type',
                    'col' => '2',
                    'desc' => $this->l('Maximum fee to add (by default currency)'),
                    'suffix' => (version_compare(_PS_VERSION_, '1.7.6', '>=')) ? $this->context->currency->symbol : $this->context->currency->sign,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Order amount free fee'),
                    'name' => 'amount_free',
                    'class' => 'toggle_type',
                    'col' => '2',
                    'desc' => $this->l('Order amount for free fee (zero to disable)'),
                    'suffix' => (version_compare(_PS_VERSION_, '1.7.6', '>=')) ? $this->context->currency->symbol : $this->context->currency->sign,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Initial order status'),
                    'name' => 'initial_status',
                    'required' => true,
                    'col' => '5',
                    'class' => 'fixed-width-md',
                    'options' => array(
                        'query' => $statuses,
                        'id' => 'id_order_state',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => $this->l('-- Choose --')
                        )
                    ),
                    'desc' => $this->l('Initial status when an order is placed with this payment option')
                ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Position'),
                    'name' => 'position',
                    'col' => '1'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Filters (when will be shown)'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Carrier(s) allowed'),
                    'name' => 'carriers[]',
                    'multiple' => true,
                    'required' => true,
                    'col' => '5',
                    'class' => 'multiple_select fixed-width-md',
                    'options' => array(
                        'query' => $carriers,
                        'id' => 'id_reference',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Carrier(s) with this payment option enabled')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Order minimum'),
                    'name' => 'order_min',
                    'col' => '3',
                    'desc' => $this->l('Order minimum amount to enable this payment option (zero to disable)'),
                    'suffix' => (version_compare(_PS_VERSION_, '1.7.6', '>=')) ? $this->context->currency->symbol : $this->context->currency->sign,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Order maximum'),
                    'name' => 'order_max',
                    'col' => '3',
                    'desc' => $this->l('Order maximum amount to enable this payment option (zero to disable)'),
                    'suffix' => (version_compare(_PS_VERSION_, '1.7.6', '>=')) ? $this->context->currency->symbol : $this->context->currency->sign,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Products with weight from'),
                    'name' => 'min_weight',
                    'col' => '3',
                    'default' => '0',
                    'desc' => $this->l('Enable rule to products with weight from'),
                    'suffix' => Configuration::get('PS_WEIGHT_UNIT')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Products with weight until'),
                    'name' => 'max_weight',
                    'col' => '3',
                    'default' => '0',
                    'desc' => $this->l('Enable rule to products with weight until (zero to disable)'),
                    'suffix' => Configuration::get('PS_WEIGHT_UNIT')
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Filter by product'),
                    'name' => 'filter_by_product',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'filter_by_product_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'filter_by_product_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Enable if you want to filter by specific products'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Product(s)'),
                    'name' => 'products[]',
                    'class' => 'multiple_select toggle_filter_by_product',
                    'multiple' => true,
                    'required' => false,
                    'col' => '7',
                    'options' => array(
                        'query' => $codfeeconf->filter_by_product ? Codfee::getProductsLite($this->context->language->id, true, true) : array(),
                        'id' => 'id_product',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Select the Product(s) where the configuration will be applied. If you don\'t select any value, the configuration will be applied to all Products'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category(s) allowed'),
                    'name' => 'categories[]',
                    'multiple' => true,
                    'required' => true,
                    'col' => '5',
                    'class' => 'multiple_select fixed-width-md',
                    'options' => array(
                        'query' => $categories,
                        'id' => 'id_category',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Category(s) with this payment option enabled'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Manufacturer(s) allowed'),
                    'name' => 'manufacturers[]',
                    'multiple' => true,
                    'required' => true,
                    'col' => '5',
                    'class' => 'multiple_select fixed-width-md',
                    'options' => array(
                        'query' => $manufacturers,
                        'id' => 'id_manufacturer',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Manufacturer(s) with this payment option enabled')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Supplier(s) allowed'),
                    'name' => 'suppliers[]',
                    'multiple' => true,
                    'required' => true,
                    'col' => '5',
                    'class' => 'multiple_select fixed-width-md',
                    'options' => array(
                        'query' => $suppliers,
                        'id' => 'id_supplier',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Supplier(s) with this payment option enabled')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Audience (who will see payment method)'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Filter by customer'),
                    'name' => 'filter_by_customer',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'filter_by_customer_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'filter_by_customer_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Enable if you want to filter by specific customers'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Customer(s)'),
                    'name' => 'customers[]',
                    'class' => 'multiple_select toggle_filter_by_customer',
                    'multiple' => true,
                    'required' => false,
                    'col' => '7',
                    'options' => array(
                        'query' => $codfeeconf->filter_by_customer ? Customer::getCustomers(true) : array(),
                        'id' => 'id_customer',
                        'name' => 'email'
                    ),
                    'desc' => $this->l('Select the Customer(s) with this payment option enabled. If you don\'t select any value, this payment option enabled will be enabled to all Customers'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Customer group(s)'),
                    'name' => 'groups[]',
                    'multiple' => true,
                    'required' => true,
                    'col' => '5',
                    'class' => 'multiple_select fixed-width-md',
                    'options' => array(
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Customer group(s) with this payment option enabled'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Country(s) allowed'),
                    'name' => 'countries[]',
                    'multiple' => true,
                    'required' => true,
                    'col' => '5',
                    'class' => 'multiple_select fixed-width-md',
                    'options' => array(
                        'query' => $countries,
                        'id' => 'id_country',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Country(s) with this payment option enabled')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Zone(s) allowed'),
                    'name' => 'zones[]',
                    'multiple' => true,
                    'required' => true,
                    'col' => '5',
                    'class' => 'multiple_select fixed-width-md',
                    'options' => array(
                        'query' => array_merge($zones, Zone::getZones()),
                        'id' => 'id_zone',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Zone(s) with this payment option enabled')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Design options'),
                'icon' => 'icon-desktop'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Payment name'),
                    'name' => 'payment_name',
                    'lang' => true,
                    'col' => '5',
                    'placeholder' => $this->l('Pay with cash on delivery: {total_without_fee} ({fee}) = {total_with_fee}'),
                    'desc' => array(
                        $this->l('Payment method name shown in payment options section'),
                        $this->l('You can use these variables:'),
                        '{total_without_fee} -> '.$this->l('to show the order total without COD fee amount'),
                        '{fee} -> '.$this->l('to show the COD fee amount'),
                        '{fee_wt} -> '.$this->l('to show the COD fee amount without taxes'),
                        '{total_with_fee} -> '.$this->l('to show the order total with cod fee amount included'),
                        $this->l('For example: Pay with cash on delivery: {total_without_fee} ({fee}) = {total_with_fee}'),
                        $this->l('To show: Pay with cash on delivery: 100.00€ (2.50€) = 102.50€'),
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Payment image'),
                    'name' => 'logo',
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'display_image' => true,
                    'col' => '5',
                    'desc' => $this->l('Upload a payment image for your Checkout page')
                ),
                array(
                    'type' => version_compare(_PS_VERSION_, '1.7', '>=') ? 'hidden' : 'select',
                    'label' => $this->l('Payment size'),
                    'name' => 'payment_size',
                    'required' => false,
                    'col' => '2',
                    'class' => 'fixed-width-md',
                    'default_value' => version_compare(_PS_VERSION_, '1.7', '>=') ? '' : $list_sizes['100%'],
                    'options' => array(
                        'query' => $list_sizes,
                        'id' => 'value',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Width of the payment method shown in checkout')
                ),
                array(
                    'type' => 'textarea',
                    'label' => (version_compare(_PS_VERSION_, '1.7', '>=')) ? $this->l('Text in checkout and order confirmation page') : $this->l('Text in checkout'),
                    'name' => 'payment_text',
                    'lang' => true,
                    'cols' => 60,
                    'rows' => 10,
                    'autoload_rte' => 'rte',
                    'col' => 6,
                    'desc' => array(
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                        (version_compare(_PS_VERSION_, '1.7', '>=')) ? $this->l('Additional text to show in checkout and order confirmation page.') : $this->l('Additional text to show in checkout payment method.')
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Custom CSS'),
                    'name' => 'custom_css',
                    'class' => 'pe_custom_css',
                    'cols' => 40,
                    'rows' => 5,
                    'desc' => $this->l('Custom CSS styles. This will override other defined css classes.')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Custom JS'),
                    'name' => 'custom_js',
                    'class' => 'pe_custom_js',
                    'cols' => 40,
                    'rows' => 5,
                    'desc' => $this->l('Custom JavaScript code. For example, you can add here Google Analytics code to track button event clicks.')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Other options'),
                'icon' => 'icon-renren'
            ),
            'input' => array(
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Show confirmation page'),
                    'name' => 'show_conf_page',
                    'class' => 't',
                    'col' => '4',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_conf_page_on',
                            'value' => 1,
                            'label' => $this->l('Show')
                        ),
                        array(
                            'id' => 'show_conf_page_off',
                            'value' => 0,
                            'label' => $this->l('Don\'t show')
                        )
                    ),
                    'desc' => $this->l('Show checkout confirmation page')
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Free on free shipping'),
                    'name' => 'free_on_freeshipping',
                    'class' => 't',
                    'col' => '4',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'free_on_freeshipping_on',
                            'value' => 1,
                            'label' => $this->l('Free on free shipping')
                        ),
                        array(
                            'id' => 'free_on_freeshipping_off',
                            'value' => 0,
                            'label' => $this->l('Not free on free shipping')
                        )
                    ),
                    'desc' => $this->l('Do not apply fee when order has free shipping')
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Hide on customer first order'),
                    'name' => 'hide_first_order',
                    'class' => 't',
                    'col' => '4',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'hide_first_order_on',
                            'value' => 1,
                            'label' => $this->l('Hide on first order')
                        ),
                        array(
                            'id' => 'hide_first_order_off',
                            'value' => 0,
                            'label' => $this->l('Don\'t hide on first order')
                        )
                    ),
                    'desc' => $this->l('This payment method will not be shown on the first order of the customer')
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Only eligible for products in stock?'),
                    'name' => 'only_stock',
                    'class' => 't',
                    'col' => '4',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'only_stock_on',
                            'value' => 1,
                            'label' => $this->l('Only with products in stock')
                        ),
                        array(
                            'id' => 'only_stock_off',
                            'value' => 0,
                            'label' => $this->l('With or without products in stock')
                        )
                    ),
                    'desc' => $this->l('This payment method will not be shown if the cart has products without stock')
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Hide with customized products?'),
                    'name' => 'hide_customized',
                    'class' => 't',
                    'col' => '4',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'hide_customized_on',
                            'value' => 1,
                            'label' => $this->l('Only with products in stock')
                        ),
                        array(
                            'id' => 'hide_customized_off',
                            'value' => 0,
                            'label' => $this->l('With or without products in stock')
                        )
                    ),
                    'desc' => $this->l('This payment method will not be shown if the cart has a customized product')
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Round total?'),
                    'name' => 'round',
                    'class' => 't',
                    'col' => '4',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'round_on',
                            'value' => 1,
                            'label' => $this->l('Round')
                        ),
                        array(
                            'id' => 'round_off',
                            'value' => 0,
                            'label' => $this->l('Don\'t round')
                        )
                    ),
                    'desc' => $this->l('Round total to next .00 (ex. 23.18 -> 24.00) and add difference to the fee amount')
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Show message on product page?'),
                    'name' => 'show_productpage',
                    'class' => 't',
                    'col' => '4',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_productpage_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'show_productpage_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                    'desc' => $this->l('Display a message on product page advising that the product can be sent with COD')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $image = ImageManager::thumbnail(_PS_TMP_IMG_DIR_.$this->module->name.'_'.$codfeeconf->id.'.'.$this->imageType, $this->module->name.'_'.(int)$codfeeconf->id.'.'.$this->imageType, 350, $this->imageType, true, false);
            $this->fields_value = array(
                'image' => $image ? $image : false,
                'size' => $image ? filesize(_PS_TMP_IMG_DIR_.$this->module->name.'_'.$codfeeconf->id.'.'.$this->imageType) / 1000 : false
            );
        }

        if ($codfeeconf->id) {
            $customers_db = explode(';', $codfeeconf->customers);
            $groups_db = explode(';', $codfeeconf->groups);
            $carriers_db = explode(';', $codfeeconf->carriers);
            $zones_db = explode(';', $codfeeconf->zones);
            $countries_db = explode(';', $codfeeconf->countries);
            $products_db = explode(';', $codfeeconf->products);
            $categories_db = explode(';', $codfeeconf->categories);
            $manufacturers_db = explode(';', $codfeeconf->manufacturers);
            $suppliers_db = explode(';', $codfeeconf->suppliers);
            $this->fields_value = array(
                'customers[]' => $customers_db,
                'groups[]' => $groups_db,
                'carriers[]' => $carriers_db,
                'zones[]' => $zones_db,
                'countries[]' => $countries_db,
                'products[]' => $products_db,
                'categories[]' => $categories_db,
                'manufacturers[]' => $manufacturers_db,
                'suppliers[]' => $suppliers_db
            );
        } else {
            $this->fields_value = array(
                'position' => (int)$this->getLastPosition() + 1,
                'amount_calc' => 0,
            );
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->smarty->assign(array(
                'AdminCodfeeAjaxController' => $this->context->link->getAdminLink('AdminCodfeeAjax')
            ));
        }

        $this->content .= parent::renderForm();
        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/translations.tpl');
        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/multiselect.tpl');

        return '';// parent::renderForm();
    }

    public function renderView()
    {
        if (!$this->loadObject()) {
            return false;
        }

        return parent::renderView();
    }

    public function processDelete()
    {
        parent::processDelete();
    }

    protected function processBulkDelete()
    {
        parent::processBulkDelete();
    }

    public function processAdd()
    {
        if (Tools::getValue('submitFormAjax')) {
            $this->redirect_after = false;
        }

        $this->_formValidations();

        if ($this->errors) {
            return parent::processAdd();
        }

        $codfeeconf = new CodfeeConfiguration();
        $codfeeconf->name = Tools::getValue('name');
        $codfeeconf->type = Tools::getValue('type');
        $codfeeconf->amount_calc = Tools::getValue('amount_calc');
        $codfeeconf->fix = Tools::getValue('fix');
        $codfeeconf->percentage = Tools::getValue('percentage');
        $codfeeconf->id_tax_rule = Tools::getValue('id_tax_rule');
        $codfeeconf->min = Tools::getValue('min');
        $codfeeconf->max = Tools::getValue('max');
        $codfeeconf->order_min = Tools::getValue('order_min');
        $codfeeconf->order_max = Tools::getValue('order_max');
        $codfeeconf->min_weight = Tools::getValue('min_weight');
        $codfeeconf->max_weight = Tools::getValue('max_weight');
        $codfeeconf->amount_free = Tools::getValue('amount_free');
        $codfeeconf->filter_by_customer = Tools::getValue('filter_by_customer');
        $codfeeconf->filter_by_product = Tools::getValue('filter_by_product');
        $codfeeconf->customers = (is_array(Tools::getValue('customers')) ? (in_array('all', Tools::getValue('customers')) ? 'all' : implode(';', Tools::getValue('customers'))) : (Tools::getValue('customers') == '' ? 'all' : Tools::getValue('customers')));
        $codfeeconf->groups = (is_array(Tools::getValue('groups')) ? (in_array('all', Tools::getValue('groups')) ? 'all' : implode(';', Tools::getValue('groups'))) : (Tools::getValue('groups') == '' ? 'all' : Tools::getValue('groups')));
        $codfeeconf->carriers = (is_array(Tools::getValue('carriers')) ? (in_array('all', Tools::getValue('carriers')) ? 'all' : implode(';', Tools::getValue('carriers'))) : (Tools::getValue('carriers') == '' ? 'all' : Tools::getValue('carriers')));
        $codfeeconf->countries = (is_array(Tools::getValue('countries')) ? (in_array('all', Tools::getValue('countries')) ? 'all' : implode(';', Tools::getValue('countries'))) : (Tools::getValue('countries') == '' ? 'all' : Tools::getValue('countries')));
        $codfeeconf->zones = (is_array(Tools::getValue('zones')) ? (in_array('all', Tools::getValue('zones')) ? 'all' : implode(';', Tools::getValue('zones'))) : (Tools::getValue('zones') == '' ? 'all' : Tools::getValue('zones')));
        $codfeeconf->products = (is_array(Tools::getValue('products')) ? (in_array('all', Tools::getValue('products')) ? 'all' : implode(';', Tools::getValue('products'))) : (Tools::getValue('products') == '' ? 'all' : Tools::getValue('products')));
        $codfeeconf->categories = (is_array(Tools::getValue('categories')) ? (in_array('all', Tools::getValue('categories')) ? 'all' : implode(';', Tools::getValue('categories'))) : (Tools::getValue('categories') == '' ? 'all' : Tools::getValue('categories')));
        $codfeeconf->suppliers = (is_array(Tools::getValue('suppliers')) ? (in_array('all', Tools::getValue('suppliers')) ? 'all' : implode(';', Tools::getValue('suppliers'))) : (Tools::getValue('suppliers') == '' ? 'all' : Tools::getValue('suppliers')));
        $codfeeconf->manufacturers = (is_array(Tools::getValue('manufacturers')) ? (in_array('all', Tools::getValue('manufacturers')) ? 'all' : implode(';', Tools::getValue('manufacturers'))) : (Tools::getValue('manufacturers') == '' ? 'all' : Tools::getValue('manufacturers')));
        $codfeeconf->initial_status = Tools::getValue('initial_status');
        $codfeeconf->show_conf_page = Tools::getValue('show_conf_page');
        $codfeeconf->free_on_freeshipping = Tools::getValue('free_on_freeshipping');
        $codfeeconf->only_stock = Tools::getValue('only_stock');
        $codfeeconf->hide_customized = Tools::getValue('hide_customized');
        $codfeeconf->hide_first_order = Tools::getValue('hide_first_order');
        $codfeeconf->round = Tools::getValue('round');
        $codfeeconf->show_productpage = Tools::getValue('show_productpage');
        $codfeeconf->active = Tools::getValue('active');
        $codfeeconf->id_shop = $this->context->shop->id;
        foreach (Language::getIsoIds(false) as $lang) {
            $id_lang = $lang['id_lang'];
            $codfeeconf->payment_name[$id_lang] = Tools::getValue('payment_name_'.$id_lang);
            $codfeeconf->payment_text[$id_lang] = Tools::getValue('payment_text_'.$id_lang);
        }
        $codfeeconf->position = Tools::getValue('position');
        $codfeeconf->payment_size = Tools::getValue('payment_size');
        $codfeeconf->save();

        if (Tools::getValue('filename') && Tools::getValue('filename'  != '')) {
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name . '_' . (int) $codfeeconf->id . '.' . $this->imageType, null, null, $this->imageType);
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name . '_mini_' . (int) $codfeeconf->id . '_' . $this->context->shop->id . '.' . $this->imageType, null, null, $this->imageType);
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->table . '_mini_' . (int) $codfeeconf->id . '_' . $this->context->shop->id . '.' . $this->imageType, null, null, $this->imageType);
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.(int) $codfeeconf->id . '.' . $this->imageType, null, null, $this->imageType);
            ImageManager::thumbnail(_PS_TMP_IMG_DIR_.$this->module->name . '_mini_' . (int) $codfeeconf->id . '.' . $this->imageType, $this->module->name . '_' . (int) $codfeeconf->id . '.' . $this->imageType, null, $this->imageType, true, true);
        }

        $this->_success = true;
    }

    public function processUpdate()
    {
        if (Validate::isLoadedObject($this->object)) {
            $this->_formValidations();
            if ($this->errors) {
                return parent::processUpdate();
            }
            if ($this->object) {
                $codfeeconf = new CodfeeConfiguration((int)$this->object->id_codfee_configuration);
            }
            if (Validate::isLoadedObject($codfeeconf)) {
                $codfeeconf->name = Tools::getValue('name');
                $codfeeconf->type = Tools::getValue('type');
                $codfeeconf->amount_calc = Tools::getValue('amount_calc');
                $codfeeconf->fix = Tools::getValue('fix');
                $codfeeconf->percentage = Tools::getValue('percentage');
                $codfeeconf->id_tax_rule = Tools::getValue('id_tax_rule');
                $codfeeconf->min = Tools::getValue('min');
                $codfeeconf->max = Tools::getValue('max');
                $codfeeconf->order_min = Tools::getValue('order_min');
                $codfeeconf->order_max = Tools::getValue('order_max');
                $codfeeconf->min_weight = Tools::getValue('min_weight');
                $codfeeconf->max_weight = Tools::getValue('max_weight');
                $codfeeconf->amount_free = Tools::getValue('amount_free');
                $codfeeconf->filter_by_customer = Tools::getValue('filter_by_customer');
                $codfeeconf->filter_by_product = Tools::getValue('filter_by_product');
                $codfeeconf->customers = (is_array(Tools::getValue('customers')) ? (in_array('all', Tools::getValue('customers')) ? 'all' : implode(';', Tools::getValue('customers'))) : (Tools::getValue('customers') == '' ? 'all' : Tools::getValue('customers')));
                $codfeeconf->groups = (is_array(Tools::getValue('groups')) ? (in_array('all', Tools::getValue('groups')) ? 'all' : implode(';', Tools::getValue('groups'))) : (Tools::getValue('groups') == '' ? 'all' : Tools::getValue('groups')));
                $codfeeconf->carriers = (is_array(Tools::getValue('carriers')) ? (in_array('all', Tools::getValue('carriers')) ? 'all' : implode(';', Tools::getValue('carriers'))) : (Tools::getValue('carriers') == '' ? 'all' : Tools::getValue('carriers')));
                $codfeeconf->countries = (is_array(Tools::getValue('countries')) ? (in_array('all', Tools::getValue('countries')) ? 'all' : implode(';', Tools::getValue('countries'))) : (Tools::getValue('countries') == '' ? 'all' : Tools::getValue('countries')));
                $codfeeconf->zones = (is_array(Tools::getValue('zones')) ? (in_array('all', Tools::getValue('zones')) ? 'all' : implode(';', Tools::getValue('zones'))) : (Tools::getValue('zones') == '' ? 'all' : Tools::getValue('zones')));
                $codfeeconf->products = (is_array(Tools::getValue('products')) ? (in_array('all', Tools::getValue('products')) ? 'all' : implode(';', Tools::getValue('products'))) : (Tools::getValue('products') == '' ? 'all' : Tools::getValue('products')));
                $codfeeconf->categories = (is_array(Tools::getValue('categories')) ? (in_array('all', Tools::getValue('categories')) ? 'all' : implode(';', Tools::getValue('categories'))) : (Tools::getValue('categories') == '' ? 'all' : Tools::getValue('categories')));
                $codfeeconf->suppliers = (is_array(Tools::getValue('suppliers')) ? (in_array('all', Tools::getValue('suppliers')) ? 'all' : implode(';', Tools::getValue('suppliers'))) : (Tools::getValue('suppliers') == '' ? 'all' : Tools::getValue('suppliers')));
                $codfeeconf->manufacturers = (is_array(Tools::getValue('manufacturers')) ? (in_array('all', Tools::getValue('manufacturers')) ? 'all' : implode(';', Tools::getValue('manufacturers'))) : (Tools::getValue('manufacturers') == '' ? 'all' : Tools::getValue('manufacturers')));
                $codfeeconf->initial_status = Tools::getValue('initial_status');
                $codfeeconf->show_conf_page = Tools::getValue('show_conf_page');
                $codfeeconf->free_on_freeshipping = Tools::getValue('free_on_freeshipping');
                $codfeeconf->only_stock = Tools::getValue('only_stock');
                $codfeeconf->hide_customized = Tools::getValue('hide_customized');
                $codfeeconf->hide_first_order = Tools::getValue('hide_first_order');
                $codfeeconf->round = Tools::getValue('round');
                $codfeeconf->show_productpage = Tools::getValue('show_productpage');
                $codfeeconf->active = Tools::getValue('active');
                $codfeeconf->id_shop = $this->context->shop->id;
                foreach (Language::getIsoIds(false) as $lang) {
                    $id_lang = $lang['id_lang'];
                    $codfeeconf->payment_name[$id_lang] = Tools::getValue('payment_name_'.$id_lang);
                    $codfeeconf->payment_text[$id_lang] = Tools::getValue('payment_text_'.$id_lang);
                }
                $codfeeconf->position = Tools::getValue('position');
                $codfeeconf->payment_size = Tools::getValue('payment_size');
                if (Tools::getValue('filename') && Tools::getValue('filename') != '') {
                    parent::postImage($this->object->id_codfee_configuration);
                }

                if ($_FILES['logo'] && $_FILES['logo']['tmp_name'] != '') {
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_'.(int)$codfeeconf->id.'.'.$this->imageType, null, null, $this->imageType);
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.(int)$codfeeconf->id.'_'.$this->context->shop->id.'.'.$this->imageType, null, null, $this->imageType);
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->table.'_mini_'.(int)$codfeeconf->id.'_'.$this->context->shop->id.'.'.$this->imageType, null, null, $this->imageType);
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.(int)$codfeeconf->id.'.'.$this->imageType, null, null, $this->imageType);
                    ImageManager::thumbnail(_PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.(int)$codfeeconf->id.'.'.$this->imageType, $this->module->name.'_'.(int)$codfeeconf->id.'.'.$this->imageType, null, $this->imageType, true, true);
                }

                $codfeeconf->save();
                $this->_success = true;
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while loading the object.').'
                <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
    }

    public function postProcess()
    {
        if (isset($_FILES['logo']) && $_FILES['logo']['tmp_name'] != '') {
            $codfeeconf = new CodfeeConfiguration((int)Tools::getValue('id_codfee_configuration'));
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_'.$codfeeconf->id.'.'.$this->imageType);
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.$codfeeconf->id.'_'.$this->context->shop->id.'.'.$this->imageType, 90);
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->table.'_mini_'.$codfeeconf->id.'_'.$this->context->shop->id.'.'.$this->imageType, 90);
            ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$codfeeconf->id.'.'.$this->imageType);
            ImageManager::thumbnail(_PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.$codfeeconf->id.'.'.$this->imageType, $this->module->name.'_'.$codfeeconf->id.'.'.$this->imageType, 350, $this->imageType, true, true);
        }

        return parent::postProcess();
    }

    public function processSave()
    {
        return parent::processSave();
    }

    protected function afterAdd($object)
    {
        $id_codfee_configuration = Tools::getValue('id_codfee_configuration');
        $this->afterUpdate($object, $id_codfee_configuration);

        if (Validate::isLoadedObject($object)) {
            if ($_FILES['logo'] && $_FILES['logo']['tmp_name'] != '') {
                ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_'.$id_codfee_configuration.'.'.$this->imageType);
                ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.$id_codfee_configuration.'_'.$this->context->shop->id.'.'.$this->imageType, 90);
                ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->table.'_mini_'.$id_codfee_configuration.'_'.$this->context->shop->id.'.'.$this->imageType, 90);
                ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.(int)$id_codfee_configuration.'.'.$this->imageType);
                ImageManager::thumbnail(_PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.(int)$id_codfee_configuration.'.'.$this->imageType, $this->module->name.'_'.$id_codfee_configuration.'.'.$this->imageType, 350, $this->imageType, true, true);
            }
        }

        return true;
    }

    protected function afterUpdate($object, $id_codfee_configuration = false)
    {
        if ($id_codfee_configuration) {
            $codfeeconf = new CodfeeConfiguration((int)$id_codfee_configuration);
        } else {
            $codfeeconf = new CodfeeConfiguration((int)$object->id_codfee_configuration);
        }
        if (Validate::isLoadedObject($codfeeconf)) {
            $codfeeconf->customers = (in_array('all', Tools::getValue('customers'))) ? 'all' : implode(';', Tools::getValue('customers'));
            $codfeeconf->groups = (in_array('all', Tools::getValue('groups'))) ? 'all' : implode(';', Tools::getValue('groups'));
            $codfeeconf->countries = (in_array('all', Tools::getValue('countries'))) ? 'all' : implode(';', Tools::getValue('countries'));
            $codfeeconf->zones = (in_array('all', Tools::getValue('zones'))) ? 'all' : implode(';', Tools::getValue('zones'));
            $codfeeconf->carriers = (in_array('all', Tools::getValue('carriers'))) ? 'all' : implode(';', Tools::getValue('carriers'));
            $codfeeconf->products = (in_array('all', Tools::getValue('products'))) ? 'all' : implode(';', Tools::getValue('products'));
            $codfeeconf->categories = (in_array('all', Tools::getValue('categories'))) ? 'all' : implode(';', Tools::getValue('categories'));
            $codfeeconf->manufacturers = (in_array('all', Tools::getValue('manufacturers'))) ? 'all' : implode(';', Tools::getValue('manufacturers'));
            $codfeeconf->suppliers = (in_array('all', Tools::getValue('suppliers'))) ? 'all' : implode(';', Tools::getValue('suppliers'));
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                if ($_FILES['logo'] && $_FILES['logo']['tmp_name'] != '') {
                    ImageManager::thumbnail(_PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.$codfeeconf->id.'.'.$this->imageType, $this->module->name.'_'.$codfeeconf->id.'.'.$this->imageType, 350, $this->imageType, true, true);
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_'.$codfeeconf->id.'.'.$this->imageType);
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->module->name.'_mini_'.$codfeeconf->id.'_'.$this->context->shop->id.'.'.$this->imageType, 90);
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$this->table.'_mini_'.$codfeeconf->id.'_'.$this->context->shop->id.'.'.$this->imageType, 90);
                    ImageManager::resize($_FILES['logo']['tmp_name'], _PS_TMP_IMG_DIR_.$codfeeconf->id.'.'.$this->imageType);
                }
            }
            $codfeeconf->save();
        }
        return true;
    }

    /**
     * Toggle active flag
     */
    public function processChangeActiveVal()
    {
        $codfeeconf = new CodfeeConfiguration($this->id_object);

        if (!Validate::isLoadedObject($codfeeconf)) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        $codfeeconf->active = $codfeeconf->active ? 0 : 1;
        if (!$codfeeconf->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    /**
     * Toggle show conf page flag
     */
    public function processChangeShowConfPageVal()
    {
        $codfeeconf = new CodfeeConfiguration($this->id_object);

        if (!Validate::isLoadedObject($codfeeconf)) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        $codfeeconf->show_conf_page = $codfeeconf->show_conf_page ? 0 : 1;
        if (!$codfeeconf->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processChangeFreeOnFreeShippingVal()
    {
        $codfeeconf = new CodfeeConfiguration($this->id_object);

        if (!Validate::isLoadedObject($codfeeconf)) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        $codfeeconf->free_on_freeshipping = $codfeeconf->free_on_freeshipping ? 0 : 1;
        if (!$codfeeconf->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processChangeHideFirstOrderVal()
    {
        $codfeeconf = new CodfeeConfiguration($this->id_object);

        if (!Validate::isLoadedObject($codfeeconf)) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        $codfeeconf->hide_first_order = $codfeeconf->hide_first_order ? 0 : 1;
        if (!$codfeeconf->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processChangeOnlyStockVal()
    {
        $codfeeconf = new CodfeeConfiguration($this->id_object);

        if (!Validate::isLoadedObject($codfeeconf)) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        $codfeeconf->only_stock = $codfeeconf->only_stock ? 0 : 1;
        if (!$codfeeconf->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processChangeRoundVal()
    {
        $codfeeconf = new CodfeeConfiguration($this->id_object);

        if (!Validate::isLoadedObject($codfeeconf)) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        $codfeeconf->round = $codfeeconf->round ? 0 : 1;
        if (!$codfeeconf->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processChangeShowProductPageVal()
    {
        $codfeeconf = new CodfeeConfiguration($this->id_object);

        if (!Validate::isLoadedObject($codfeeconf)) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        $codfeeconf->show_productpage = $codfeeconf->show_productpage ? 0 : 1;
        if (!$codfeeconf->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating fee information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function printShowConfPageIcon($value, $codfeeconf)
    {
        $this->context->smarty->assign(array(
            'value' => $value
        ));
        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/valid_icon.tpl')->fetch();
    }

    public function printFreeOnFreeShippingIcon($value, $codfeeconf)
    {
        $this->context->smarty->assign(array(
            'value' => $value
        ));
        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/valid_icon.tpl')->fetch();
    }

    public function printHideFirstOrderIcon($value, $codfeeconf)
    {
        $this->context->smarty->assign(array(
            'value' => $value
        ));
        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/valid_icon.tpl')->fetch();
     }

    public function printOnlyStockIcon($value, $codfeeconf)
    {
        $this->context->smarty->assign(array(
            'value' => $value
        ));
        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/valid_icon.tpl')->fetch();
     }

    public function printRoundIcon($value, $codfeeconf)
    {
        $this->context->smarty->assign(array(
            'value' => $value
        ));
        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/valid_icon.tpl')->fetch();
     }

    public function printShowProductPageIcon($value, $codfeeconf)
    {
        $this->context->smarty->assign(array(
            'value' => $value
        ));
        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/valid_icon.tpl')->fetch();
    }

    public function displayDeleteLink($token = null, $id = 0, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');
        $tpl->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token),
            'confirm' => $this->l('Delete the selected item?').$name,
            'action' => $this->l('Delete'),
            'id' => $id,
        ));

        return $tpl->fetch();
    }

    protected function getCodfeeTypes()
    {
        $types = array($this->l('Fix'), $this->l('Percentage'), $this->l('Fix + Percentage'), $this->l('Cash on pickup'));

        $list_types = array();
        foreach ($types as $key => $type) {
            $list_types[$key]['id'] = $key;
            $list_types[$key]['value'] = $key;
            $list_types[$key]['name'] = $type;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            unset($list_types[3]);
        }
        return $list_types;
    }

    public function getCodfeeType($type)
    {
        if ($type == '0') {
            return $this->l('Fix');
        } elseif ($type == '1') {
            return $this->l('Percentage');
        } elseif ($type == '2') {
            return $this->l('Fix + Percentage');
        } elseif ($type == '3') {
            return $this->l('Cash on pickup');
        }
        return '';
    }

    public function getPaymentMethodText($text, $conf)
    {
        if (trim($text) == '') {
            if ($conf['type'] == '3') {
                return $this->l('Pay upon cash on pickup');
            }
            return $this->l('Pay with cash on delivery: {total_without_fee} ({fee}) = {total_with_fee}');
        }
        return $text;
    }

    protected function getAmountCalcTypes()
    {
        $types = array($this->l('Order total'), $this->l('Order total wt shipping'), $this->l('Only shipping'));

        $list_types = array();
        foreach ($types as $key => $type) {
            $list_types[$key]['id'] = $key;
            $list_types[$key]['value'] = $key;
            $list_types[$key]['name'] = $type;
        }
        return $list_types;
    }

    public function getAmountCalcType($type, $conf)
    {
        switch ($conf['type']) {
            case '0':
            case '3':
                return '--';
            default:
        }
        if ($type == '0') {
            return $this->l('Order total');
        } elseif ($type == '1') {
            return $this->l('Order total wt shipping');
        } elseif ($type == '2') {
            return $this->l('Only shipping');
        } else {
            return '--';
        }
    }

    public function getFeeForList($value, $row)
    {
        if ($row['type'] == '3') {
            return '--';
        } else {
            return $value;
        }
    }

    public function getCustomerGroups($ids_customer_groups)
    {
        if ($ids_customer_groups === 'all') {
            return $this->l('All');
        }
        $groups = array();
        $groups_array = explode(';', $ids_customer_groups);
        foreach ($groups_array as $key => $group) {
            if ($key == $this->top_elements_in_list) {
                $groups[] = $this->l('...and more');
                break;
            }
            $group = new Group($group, $this->context->language->id);
            $groups[] = $group->name;
        }
        return implode('<br />', $groups);
    }

    public function getCarriers($ids_carriers)
    {
        if ($ids_carriers === 'all') {
            return $this->l('All');
        }
        $carriers = array();
        $carriers_array = explode(';', $ids_carriers);
        foreach ($carriers_array as $key => $carrier) {
            if ($key == $this->top_elements_in_list) {
                $carriers[] = $this->l('...and more');
                break;
            }
            $carrier = Carrier::getCarrierByReference($carrier);
            $carriers[] = $carrier->name;
        }
        return implode('<br />', $carriers);
    }

    public function getCountries($ids_countries)
    {
        if ($ids_countries === 'all') {
            return $this->l('All');
        }
        $countries = array();
        $countries_array = explode(';', $ids_countries);
        foreach ($countries_array as $key => $country) {
            if ($key == $this->top_elements_in_list) {
                $countries[] = $this->l('...and more');
                break;
            }
            $country = new Country($country, $this->context->language->id);
            $countries[] = $country->name;
        }
        return implode('<br />', $countries);
    }

    public function getZones($ids_zones)
    {
        if ($ids_zones === 'all') {
            return $this->l('All');
        }
        $zones = array();
        $zones_array = explode(';', $ids_zones);
        foreach ($zones_array as $key => $zone) {
            if ($key == $this->top_elements_in_list) {
                $zones[] = $this->l('...and more');
                break;
            }
            $zone = new Zone($zone);
            $zones[] = $zone->name;
        }
        return implode('<br />', $zones);
    }

    public function getCategories($ids_categories)
    {
        if ($ids_categories === 'all') {
            return $this->l('All');
        }
        $categories = array();
        $categories_array = explode(';', $ids_categories);
        foreach ($categories_array as $key => $category) {
            if ($key == $this->top_elements_in_list) {
                $categories[] = $this->l('...and more');
                break;
            }
            $category = new Category($category, $this->context->language->id);
            $categories[] = $category->name;
        }
        return implode('<br />', $categories);
    }

    public function getCodFeeOrdersList($export = false, $params = false)
    {
        $list_codfeeorders = $this->getCodFeeOrders($params);

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $carriers = Carrier::getCarriers((int)$this->context->language->id);
        foreach ($carriers as $carrier) {
            $this->carriers_array[$carrier['id_reference']] = $carrier['name'];
        }

        $fields_list = array(
            'id_order' => array(
                'title' => $this->l('Order Id'),
                'type' => 'text',
                'align' => 'center',
                'callback' => 'printGoToOrderButton'
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'type' => 'text',
                'align' => 'center',
                //'callback' => 'getVposName',
                //'callback' => 'printGoToTpvConfOrGetTpvName'
            ),
            'id_customer' => array(
                'title' => $this->l('Customer'),
                'type' => 'text',
                'align' => 'center',
                'callback' => 'printGoToCustomerButton'
            ),
            'payment' => array(
                'title' => $this->l('Payment method'),
                'type' => 'text',
                'align' => 'center',
            ),
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total'),
                //'type' => 'price',
                'align' => 'center',
                'badge_success' => true,
                'class' => 'badge_cancel',
                'callback' => 'getPriceWithCurrency'
            ),
            'codfee' => array(
                'title' => $this->l('Fee'),
                //'type' => 'price',
                'align' => 'center',
                'callback' => 'getPriceWithCurrency'
            ),
            'id_currency' => array(
                'title' => $this->l('Currency'),
                'type' => 'text',
                'align' => 'center',
                'callback' => 'getCurrencyName'
            ),
            'osname' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname'
            ),
            'id_reference' => array(
                'title' => $this->l('Carrier'),
                'type' => 'select',
                'list' => $this->carriers_array,
                'filter_key' => 'a!id_reference',
                'filter_type' => 'int',
                'align' => 'center',
                'callback' => 'getCarrierName'
            ),
            'valid' => array(
                'title' => $this->l('Valid'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printIsValidOrder'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'align' => 'center',
            ),
        );

        if ($export === true) {
            $list_codfeeorders_export = array();
            foreach ($list_codfeeorders as $list_codfeeorder) {
                $list_codfeeorder['total_paid_tax_incl'] = $this->formatDecimalsSeparator($list_codfeeorder['total_paid_tax_incl'], $this->context->currency, $this->context);
                $list_codfeeorder['fee'] = $this->formatDecimalsSeparator($list_codfeeorder['fee'], $this->context->currency, $this->context);
                $list_codfeeorder['id_currency'] = $this->getCurrencyName($list_codfeeorder['id_currency']);
                $list_codfeeorder['id_reference'] = $this->getCarrierName($list_codfeeorder['id_reference']);
                $list_codfeeorder['valid'] = $this->printIsValidOrder($list_codfeeorder['valid']);
                unset($list_codfeeorder['id_customer']);
                unset($list_codfeeorder['color']);
                unset($list_codfeeorder['cname']);
                unset($list_codfeeorder['badge_success']);
                $list_codfeeorders_export[] = $list_codfeeorder;
            }
            return array_merge(array($fields_list), $list_codfeeorders_export);
        }

        $module = new CodFee();
        $helper_list = new HelperList();
        $helper_list->module = $module;
        $helper_list->title = $this->l('Cash on delivery with fee orders list');
        $helper_list->shopLinkType = '';
        $helper_list->no_link = true;
        $helper_list->show_toolbar = true;
        $helper_list->simple_header = (version_compare(_PS_VERSION_, '1.5', '<')) ? true : false;
        $helper_list->identifier = 'id_order';
        $helper_list->table = 'order';
        $helper_list->list_id = $helper_list->table;
        $helper_list->currentIndex = $this->context->link->getAdminLink($this->tabClassName, false).'&configure='.$this->module->name;
        $helper_list->token = Tools::getAdminTokenLite($this->tabClassName);
        $helper_list->listTotal = count($list_codfeeorders);
        $helper_list->tpl_vars['icon'] = 'icon-money';
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $helper_list->toolbar_btn['export'] = array(
                'href' => $this->context->link->getAdminLink($this->tabClassName).'&exportcodfeeorders',
                'desc' => $this->l('Export')
            );
        }
        $this->_helperlist = $helper_list;

        if (version_compare(_PS_VERSION_, '1.6.0.14', '>')) {
            if (Tools::isSubmit($helper_list->table.'_pagination')) {
                $helper_list->_default_pagination = Tools::getValue($helper_list->table.'_pagination');
            } else {
                $helper_list->_default_pagination = $this->_default_pagination;
            }
        }

        $page = ($page = Tools::getValue('submitFilter'.$helper_list->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper_list->table.'_pagination')) ? $pagination : $this->_default_pagination;
        $list_codfeeorders = $this->paginate($list_codfeeorders, $page, $pagination);

        $this->tpl_list_vars['order_statuses'] = $this->statuses_array;
        $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        $this->tpl_list_vars['POST'] = $_POST;

        $list = $helper_list->generateList($list_codfeeorders, $fields_list);

        return $list;
    }

    public function getCodFeeOrders($params = false)
    {
        $_filters = '';
        $_order = 'a.`date_add` DESC';
        if (Tools::isSubmit('submitFilterorder') && (int)Tools::getValue('submitFilterorder') > 0) {
            foreach ($this->_filters as $_field => $_value) {
                if ($_value != '' && $_field == 'filter_date_add_from') {
                    $_filters .= ' AND a.`date_add` >= "'.pSQL($_value).'"';
                } elseif ($_value != '' && $_field == 'filter_date_add_to') {
                    $_filters .= ' AND a.`date_add` <= DATE_ADD("'.pSQL($_value).'", INTERVAL 1 DAY)';
                } elseif ($_field == 'filter_id_reference' && $_value != '') {
                    $_filters .= ' AND `'.bqSQL(str_replace('filter_', '', $_field)).'` = '.pSQL($_value);
                } elseif ($_field == 'filter_id_order_state' && $_value != '') {
                    $_filters .= ' AND os.`'.bqSQL(str_replace('filter_', '', ''.$_field)).'` = '.pSQL($_value);
                } elseif ($_value != '') {
                    $_filters .= ' AND a.`'.bqSQL(str_replace('filter_', '', $_field)).'` = "'.pSQL($_value).'"';
                }
            }
        }
        if ($params) {
            $_filters .= ' AND `id_order` = '.(int)$params['id_order'];
        }
        if (Tools::isSubmit($this->module->name.'ordersOrderby') && Tools::getValue($this->module->name.'ordersOrderway')) {
            $_order = '`'.bqSQL(Tools::getValue($this->module->name.'ordersOrderby')).'` '.(Validate::isOrderWay(Tools::getValue($this->module->name.'ordersOrderway')) ? Tools::getValue($this->module->name.'ordersOrderway') : '');
        }
        $sql = 'SELECT SQL_CALC_FOUND_ROWS a.`id_order`, `reference`, a.`id_customer`, CONCAT(LEFT(c.`firstname`, 1), " ", c.`lastname`) AS `customer`,
                    `payment`, `total_paid_tax_incl`, a.`codfee` as codfee, a.id_currency, osl.`name` AS `osname`, carrier.`id_reference`, a.`valid`,
                    a.`date_add` AS `date_add`, os.`color`, country_lang.name as cname, IF(a.valid, 1, 0) badge_success
                FROM `'._DB_PREFIX_.'orders` a
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
                LEFT JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
                LEFT JOIN `'._DB_PREFIX_.'carrier` carrier ON carrier.id_carrier = a.id_carrier
                LEFT JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
                LEFT JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.(int)$this->context->language->id.')
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
                LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')
                WHERE a.`id_shop` = '.(int)$this->context->shop->id.
                ' AND a.`module` = "'.pSQL($this->module->name).'"'.
                $_filters.'
                ORDER BY '.pSQL($_order);
        return Db::getInstance()->executeS($sql);
    }

    public function paginate($array_elements, $page = 1, $pagination = 5)
    {
        if (count($array_elements) > $pagination) {
            $array_elements = array_slice($array_elements, $pagination * ($page - 1), $pagination);
        }
        return $array_elements;
    }

    public static function printGoToCustomerButton($id_customer)
    {
        $customer = new Customer($id_customer);
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            return $id_customer.' - '.$customer->firstname.' '.$customer->lastname;
        }
        if ($customer->id) {
            $tpl = Context::getContext()->smarty->createTemplate('helpers/list/list_action_view.tpl');
            $_href = Context::getContext()->link->getAdminLink('AdminCustomers').'&viewcustomer&id_customer='.(int)$id_customer;
            if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
	            $_href = Context::getContext()->link->getAdminLink('AdminCustomers', true, [], [
	                'viewcustomer' => 1,
	                'id_customer' => $id_customer,
	            ]);
	        }
            $tpl->assign(array(
                'href' => $_href,
                'action' => $id_customer.' - '.$customer->firstname.' '.$customer->lastname,
                'id' => $id_customer,
            ));
            return $tpl->fetch();
        } else {
            return $id_customer;
        }
    }

    public static function printGoToOrderButton($id_order)
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            return $id_order;
        }
        $tpl = Context::getContext()->smarty->createTemplate('helpers/list/list_action_view.tpl');
        $_href = Context::getContext()->link->getAdminLink('AdminOrders').'&id_order='.(int)$id_order.'&vieworder';
        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            $_href = Context::getContext()->link->getAdminLink('AdminOrders', true, [], [
                'vieworder' => 1,
                'id_order' => $id_order,
            ]);
        }
        $tpl->assign(array(
            'href' => $_href,
            'action' => $id_order,
            'id' => $id_order,
        ));
        return $tpl->fetch();
    }

    public static function printGoToCartButton($id_cart)
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            return $id_cart;
        }
        $tpl = Context::getContext()->smarty->createTemplate('helpers/list/list_action_view.tpl');
        $tpl->assign(array(
            'href' => Context::getContext()->link->getAdminLink('AdminCarts').'&id_cart='.(int)$id_cart.'&viewcart',
            'action' => $id_cart,
            'id' => $id_cart,
        ));
        return $tpl->fetch();
    }

    public function printIsValidOrder($valid)
    {
        return ($valid ? $this->l('Yes') : $this->l('No'));
    }

    public function getPriceWithCurrency($price, $params)
    {
        return Tools::displayPrice($price, (int)$params['id_currency']);
    }

    public function getCurrencyName($id_currency)
    {
        $currency = new Currency((int)$id_currency);
        if (is_object($currency)) {
            return $currency->iso_code;
        }
        return '--';
    }

    public function getCarrierName($id_reference)
    {
        $carrier = Carrier::getCarrierByReference((int)$id_reference);
        if (is_object($carrier)) {
            return $carrier->name;
        }
        return '--';
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            return Context::getContext()->getTranslator()->trans($string, array(), 'Modules.Codfee.AdminCodfeeConfigurationController');
        }
        return parent::l($string, $class, $addslashes, $htmlentities);
    }

    public function downloadCsv($data, $filename)
    {
        header('Content-type: text/csv');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
        header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Content-Transfer-Encoding: binary');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        $head = array();
        foreach ($data[0] as $heading) {
            $head[] = $heading['title'];
        }
        fputcsv($output, $head, ';');
        unset($data[0]);
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
    }

    protected function formatDecimalsSeparator($price, $currency, $context)
    {
        if (!is_numeric($price)) {
            return $price;
        }
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return Tools::displayNumber($price);
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if ($currency === null) {
            $currency = $context->currency;
        } elseif (is_int($currency)) {
            $currency = Currency::getCurrencyInstance((int)$currency);
        }
        if (is_array($currency)) {
            $c_format = $currency['format'];
            $c_decimals = (int)$currency['decimals'] * _PS_PRICE_DISPLAY_PRECISION_;
        } elseif (is_object($currency)) {
            $c_format = $currency->format;
            $c_decimals = (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
        } else {
            return false;
        }
        $ret = 0;
        if (($is_negative = ($price < 0))) {
            $price *= -1;
        }
        $price = Tools::ps_round($price, $c_decimals);
        if (($c_format == 2) && ($context->language->is_rtl == 1)) {
            $c_format = 4;
        }
        switch ($c_format) {
            /* X 0,000.00 */
            case 1:
                $ret = $price;
                break;
            /* 0 000,00 X*/
            case 2:
                $ret = str_replace('.', ',', $price);
                break;
            /* X 0.000,00 */
            case 3:
                $ret = str_replace('.', ',', $price);
                break;
            /* 0,000.00 X */
            case 4:
                $ret = $price;
                break;
            /* X 0'000.00  Added for the switzerland currency */
            case 5:
                $ret = $price;
                break;
        }
        return (float)$ret;
    }

    protected function setFilters()
    {
        if (Tools::isSubmit('submitFilterorder') && (int)Tools::getValue('submitFilterorder') > 0) {
            $this->_filters = array(
                'filter_id_order' => (string)Tools::getValue('orderFilter_id_order'),
                'filter_reference' => (string)Tools::getValue('orderFilter_reference'),
                'filter_id_customer' => (string)Tools::getValue('orderFilter_id_customer'),
                'filter_payment' => (string)Tools::getValue('orderFilter_payment'),
                'filter_total_paid_tax_incl' => (string)Tools::getValue('orderFilter_total_paid_tax_incl'),
                'filter_codfee' => (string)Tools::getValue('orderFilter_codfee'),
                'filter_id_currency' => (string)Tools::getValue('orderFilter_id_currency'),
                'filter_id_order_state' => (string)Tools::getValue('orderFilter_os!id_order_state'),
                'filter_id_reference' => (string)Tools::getValue('orderFilter_a!id_reference'),
                'filter_valid' => (string)Tools::getValue('orderFilter_valid'),
                'filter_date_add_from' => Tools::getValue('orderFilter_date_add')[0],
                'filter_date_add_to' => Tools::getValue('orderFilter_date_add')[1],
            );
        }
    }

    private function _createTemplate($tpl_name)
    {
        if ($this->override_folder) {
            if ($this->context->controller instanceof ModuleAdminController) {
                $override_tpl_path = $this->context->controller->getTemplatePath().$tpl_name;
            } elseif ($this->module) {
                $override_tpl_path = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/'.$tpl_name;
            } else {
                if (file_exists($this->context->smarty->getTemplateDir(1).DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name)) {
                    $override_tpl_path = $this->context->smarty->getTemplateDir(1).DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name;
                } elseif (file_exists($this->context->smarty->getTemplateDir(0).DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name)) {
                    $override_tpl_path = $this->context->smarty->getTemplateDir(0).'controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name;
                }
            }
        } else if ($this->module) {
            $override_tpl_path = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/'.$tpl_name;
        }
        if (isset($override_tpl_path) && file_exists($override_tpl_path)) {
            return $this->context->smarty->createTemplate($override_tpl_path, $this->context->smarty);
        } else {
            return $this->context->smarty->createTemplate($tpl_name, $this->context->smarty);
        }
    }

    private function _formValidations()
    {
        /*
        if (trim(Tools::getValue('name')) == '') {
            $this->errors[] = Tools::displayError($this->l('Field Name can not be empty.'));
            $this->display = 'edit';
        }
        */
        if (trim(Tools::getValue('type')) == '') {
            $this->errors[] = Tools::displayError($this->l('Field Type can not be empty.'));
            $this->display = 'edit';
        }
        /*
        if (Tools::getValue('groups') == '') {
            $this->errors[] = Tools::displayError($this->l('Field "Customer group(s)" can not be empty.'));
            $this->display = 'edit';
        }
        if (Tools::getValue('carriers') == '') {
            $this->errors[] = Tools::displayError($this->l('Field "Carrier(s) allowed" can not be empty.'));
            $this->display = 'edit';
        }
        if (Tools::getValue('countries') == '') {
            $this->errors[] = Tools::displayError($this->l('Field "Country(s) allowed" can not be empty.'));
            $this->display = 'edit';
        }
        if (Tools::getValue('zones') == '') {
            $this->errors[] = Tools::displayError($this->l('Field "Zone(s) allowed" can not be empty.'));
            $this->display = 'edit';
        }
        if (Tools::getValue('categories') == '') {
            $this->errors[] = Tools::displayError($this->l('Field "Category(s)" can not be empty.'));
            $this->display = 'edit';
        }
        if (Tools::getValue('manufacturers') == '') {
            $this->errors[] = Tools::displayError($this->l('Field "Manufacturer(s)" can not be empty.'));
            $this->display = 'edit';
        }
        if (Tools::getValue('suppliers') == '') {
            $this->errors[] = Tools::displayError($this->l('Field "Supplier(s)" can not be empty.'));
            $this->display = 'edit';
        }
        */
        if (trim(Tools::getValue('initial_status')) == '') {
            $this->errors[] = Tools::displayError($this->l('Field Initial order status can not be empty.'));
            $this->display = 'edit';
        }
    }

    private function _updatePositions($positions)
    {
        foreach ($positions as $key => $position) {
            $pos = explode('_', $position);
            Db::getInstance()->execute('
                UPDATE `'.pSQL(_DB_PREFIX_.$this->module->name).'_configuration`
                SET `position` = '.(int)$key.'
                WHERE `id_codfee_configuration` = '.(int)$pos[2]);
        }
        return true;
    }

    private function getLastPosition()
    {
        $position = Db::getInstance()->getValue(
            'SELECT c.`position`
            FROM `'.pSQL(_DB_PREFIX_.$this->module->name).'_configuration` c
            WHERE c.`id_shop` = ' . (int)$this->context->shop->id . '
            ORDER BY c.`position` DESC;'
        );
        if ($position === false) {
            return -1;
        }
        return $position;
    }
}
