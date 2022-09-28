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
*  @copyright 2021 idnovate
*  @license   See above
*/

include_once(_PS_MODULE_DIR_.'codfee/classes/CodfeeConfiguration.php');

class CodFee extends PaymentModule
{
    private $_html = '';
    private $_postErrors = array();

    public function __construct()
    {
        $this->name = 'codfee';
        $this->tab = 'payments_gateways';
        $this->version = '3.5.0';
        $this->author = 'idnovate';
        $this->module_key = '3b802d29c8d730c7b17aa2970ab57c95';
        //$this->author_address = '0xd89bcCAeb29b2E6342a74Bc0e9C82718Ac702160';
        $this->addons_id_product = '6337';
        $this->page = basename(__FILE__, '.php');
        $this->bootstrap = true;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->is_eu_compatible = 1;

        $this->tabClassName = 'AdminCodfeeConfiguration';
        $this->tabClassAjaxName = 'AdminCodfeeAjax';
        $this->imageType = 'png';

        parent::__construct();

        $this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->l('Cash on delivery with fee');
        $this->description = $this->l('Accept cash on delivery payments with extra fee and cash on pickup payments. Configurable by customer group, carrier, country, zone, category, manufacturer and supplier.');

        $this->confirmUninstall = $this->l('Are you sure you want to delete the module and the related data?');

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            if (Configuration::get('PS_DISABLE_NON_NATIVE_MODULE')) {
                $this->warning = $this->l('You have to enable non PrestaShop modules at ADVANCED PARAMETERS - PERFORMANCE');
            }
        }
    }

    public function install()
    {
        if (!parent::install()
            || !$this->initSQLCodfeeConfiguration()
            || (version_compare(_PS_VERSION_, '1.7', '<') && !$this->registerHook('payment'))
            || !$this->registerHook('paymentReturn')
            || (version_compare(_PS_VERSION_, '1.7', '<') && Hook::get('displayPaymentEU') && !$this->registerHook('displayPaymentEU'))
            || !$this->registerHook('displayPDFInvoice')
            || !$this->registerHook('displayOrderDetail')
            || !$this->registerHook('displayAdminOrder')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('displayRightColumnProduct')
            || !$this->registerHook('displayLeftColumnProduct')
            || (version_compare(_PS_VERSION_, '1.7.7', '>=') && !$this->registerHook('actionObjectOrderUpdateAfter'))
            || (version_compare(_PS_VERSION_, '1.7.7', '>=') && !$this->registerHook('actionObjectOrderInvoiceUpdateAfter'))
            || (version_compare(_PS_VERSION_, '1.7', '>=') && !$this->registerHook('paymentOptions'))
            || (version_compare(_PS_VERSION_, '1.7', '>=') && !$this->registerHook('displayProductAdditionalInfo'))
            || !$this->registerHook('header')) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->addTab($this->displayName, $this->tabClassName, -1);
            $this->addTab($this->displayName, $this->tabClassAjaxName, -1);
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallSQL()) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->removeTab($this->tabClassName);
            $this->removeTab($this->tabClassAjaxName);
        }
        return true;
    }

    public function getContent()
    {
        if (Tools::getValue('submitCOD')) {
            if (!count($this->_postErrors)) {
                Configuration::updateValue('COD_FEE_TAX', (float)str_replace(',', '.', Tools::getValue('fee_tax') ? Tools::getValue('fee_tax') : 0));
                Configuration::updateValue('COD_FEE', (float)str_replace(',', '.', Tools::getValue('fee') ? Tools::getValue('fee') : 0));
                Configuration::updateValue('COD_FREE_FEE', (float)str_replace(',', '.', Tools::getValue('free_fee') ? Tools::getValue('free_fee') : 0));
                Configuration::updateValue('COD_FEE_TYPE', (float)str_replace(',', '.', Tools::getValue('feetype') ? Tools::getValue('feetype') : 0));
                Configuration::updateValue('COD_FEE_MIN', (float)str_replace(',', '.', Tools::getValue('feemin') ? Tools::getValue('feemin') : 0));
                Configuration::updateValue('COD_FEE_MAX', (float)str_replace(',', '.', Tools::getValue('feemax') ? Tools::getValue('feemax') : 0));
                Configuration::updateValue('COD_FEE_MIN_AMOUNT', (float)str_replace(',', '.', Tools::getValue('minimum_amount') ? Tools::getValue('minimum_amount') : 0));
                Configuration::updateValue('COD_FEE_MAX_AMOUNT', (float)str_replace(',', '.', Tools::getValue('maximum_amount') ? Tools::getValue('maximum_amount') : 0));
                Configuration::updateValue('COD_FEE_CARRIERS', trim(Tools::getValue('id_carriers'), ';'));
                Configuration::updateValue('COD_FEE_STATUS', Tools::getValue('fee_status'));
                Configuration::updateValue('COD_SHOW_CONF', Tools::getValue('show_conf'));
            }
        }
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $id_tab = Tab::getIdFromClassName($this->tabClassName);
            if (!$id_tab) {
                $this->addTab($this->displayName, $this->tabClassName);
            }
            $id_tab_ajax = Tab::getIdFromClassName($this->tabClassAjaxName);
            if (!$id_tab_ajax) {
                $this->addTab($this->displayName, $this->tabClassAjaxName);
            }
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            Tools::redirectAdmin('index.php?tab=' . $this->tabClassName . '&token=' . Tools::getAdminTokenLite($this->tabClassName));
        } else {
            Tools::redirectAdmin('index.php?controller=' . $this->tabClassName . '&token=' . Tools::getAdminTokenLite($this->tabClassName));
        }
        return $this->display(__FILE__, 'admin.tpl');
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $order = $params['objOrder'];
        } else {
            $order = $params['order'];
        }

        $state = $order->getCurrentState();
        $codfeeconf = new CodfeeConfiguration(Tools::getValue('c'));

        if (
            $state == $codfeeconf->initial_status ||
            $state == Configuration::get('PS_OS_OUTOFSTOCK') ||
            $state == Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')
        ) {
            $products = $order->getProducts();

            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $total_to_pay = $params['total_to_pay'];
            } else {
                $total_to_pay = $order->getOrdersTotalPaid();
            }

            if ($codfeeconf->payment_text[$this->context->language->id] && $codfeeconf->payment_text[$this->context->language->id] != '') {
                $shop_message = $codfeeconf->payment_text[$this->context->language->id];
            } else {
                $shop_message = false;
            }

            if (!$codfeeconf) {
                $fee = 0;
            }

            $cart = new Cart((int)$order->id_cart);
            $carrier = new Carrier((int)$cart->id_carrier);

            $customer = new Customer((int)$cart->id_customer);
            $group = new Group((int)$customer->id_default_group);
            if ($group->price_display_method == '1') {
                $price_display_method = false;
            } else {
                $price_display_method = true;
            }

            $fee = (float)Tools::ps_round((float)$this->getFeeCost($cart, (array)$codfeeconf, true), 2);
            $fee_without_tax = (float)Tools::ps_round((float)$this->getFeeCost($cart, (array)$codfeeconf, false), 2);
            if ($codfeeconf->free_on_freeshipping == '1' && $cart->getOrderTotal(true, Cart::ONLY_SHIPPING) == 0) {
                $fee = (float)0.00;
                $fee_without_tax = (float)0.00;
            }
            if ($codfeeconf->free_on_freeshipping == '1' && count($cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING)) > 0) {
                $fee = (float)0.00;
                $fee_without_tax = (float)0.00;
            }

            $codfee_wt = $fee;
            if ($codfeeconf->id_tax_rule == '0') {
                if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                    $carrier_tax_rate = $carrier->getTaxesRate(new Address($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                    $codfee_wt = $fee / (1 + (($carrier_tax_rate) / 100));
                }
            } elseif ($codfeeconf->id_tax_rule == '9999') {
                $codfee_wt = $fee;
            } else {
                $remove_taxes = false;
                if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                    $address_id = (int)$cart->id_address_invoice;
                } else {
                    $address_id = (int)$cart->id_address_delivery;
                }
                if (!Address::addressExists($address_id)) {
                    $address_id = null;
                }
                $address = Address::initialize($address_id, true);
                $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf->id_tax_rule)->getTaxCalculator();
                $location_tax_rate = $tax_calculator->getTotalRate();
                $codfee_wt = $fee / (1 + (($location_tax_rate) / 100));
                if ($location_tax_rate == 0) {
                    $remove_taxes = true;
                    $address = new Address();
                    $address->id_country = (int)$this->context->country->id;
                    $address->id_state   = 0;
                    $address->postcode   = 0;
                    $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf->id_tax_rule)->getTaxCalculator();
                    $location_tax_rate = $tax_calculator->getTotalRate();
                    $codfee_wt = $fee / (1 + (($location_tax_rate) / 100));
                }
            }
            if (isset($remove_taxes) && $remove_taxes === true) {
                $fee = $codfee_wt;
            }

            Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.bqSQL('orders').'`
            SET `codfee` = '.(float)$fee.'
            WHERE `'.bqSQL('id_order').'` = '.(int)$order->id);

            try {
                Db::getInstance()->execute('
                    INSERT INTO `'._DB_PREFIX_.'codfee_orders` (`id_codfee_configuration`, `id_order`, `id_carrier`, `fee_amount`, `fee_amount_without_tax`)
                    VALUES ('.(int)$codfeeconf->id_codfee_configuration.','.(int)$order->id.','.(int)$order->id_carrier.','.(float)$fee.','.(float)$codfee_wt.')
                ');
            } catch (Exception $e) {

            }

            $currency = new Currency($order->id_currency);

            $this->context->smarty->assign(array(
                'total'         => Tools::displayPrice($total_to_pay, $currency, false),
                'success'       => true,
                'id_order'      => $order->id,
                'shop_message'  => $shop_message,
                'total_to_pay'  => Tools::displayPrice($total_to_pay, $currency),
                'order'         => $order,
                'order_products'=> $products,
                'fee_type'      => $codfeeconf->type,
                'codfee'        => $fee,
                'shop_name'     => Configuration::get('PS_SHOP_NAME'),
                'status'        => 'ok'
            ));
        } else {
            $this->context->smarty->assign(array(
                'success'       => false,
                'status'        => false
            ));
        }

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return $this->display(__FILE__, 'payment_return17.tpl');
        } else {
            return $this->display(__FILE__, 'payment_return.tpl');
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (version_compare(_PS_VERSION_, '1.6', '<') === true) {
            $this->context->controller->setMedia();
            $this->context->controller->addJs($this->_path.'views/js/back.js');
        }
    }

    public function hookHeader($params)
    {
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $this->context->controller->addCSS($this->_path.'views/css/codfee_1.6.css', 'all');
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                if (Module::isEnabled('bestkit_opc')) {
                    $this->context->controller->addJS(($this->_path).'views/js/codfee17_bestkit_opc.js');
                } else {
                    $this->context->controller->addJS(($this->_path).'views/js/codfee17.js');
                }
            }
            if ($this->isModuleActive('onepagecheckout') || Configuration::get('PS_ORDER_PROCESS_TYPE') == '0') {
                $this->context->controller->addJS(($this->_path).'views/js/codfee16.js');
            }
            if ($this->isModuleActive('advancedeucompliance') && Configuration::get('AEUC_FEAT_ADV_PAYMENT_API') && Configuration::get('AEUC_FEAT_ADV_PAYMENT_API') == '1') {
                $this->context->controller->addJS(($this->_path).'views/js/codfeeUE.js');
            }
        } else {
            $this->context->controller->addCSS($this->_path.'views/css/codfee_1.5.css', 'all');
        }
    }

    public function hookPaymentOptions($params)
    {
        return $this->hookPayment($params);
    }

    public function hookPayment($params)
    {
        if (!$this->active || $params['cart']->isVirtualCart()) {
            return false;
        }
        $id_lang = $params['cart']->id_lang;
        $id_shop = $params['cart']->id_shop;
        $customer = new Customer((int)$params['cart']->id_customer);
        $customer_groups = $customer->getGroupsStatic($customer->id);
        $carrier = new Carrier((int)$params['cart']->id_carrier);
        $carrier_ref = $carrier->id_reference;
        $address = new Address((int)$params['cart']->id_address_delivery);
        $country = new Country((int)$address->id_country);
        if ($address->id_state > 0) {
            $zone = State::getIdZone((int)$address->id_state);
        } else {
            $zone = $country->getIdZone((int)$country->id);
        }
        $manufacturers = '';
        $suppliers = '';
        $products = $params['cart']->getProducts();
        foreach ($products as $product) {
            $manufacturers .= $product['id_manufacturer'].';';
            $suppliers .= $product['id_supplier'].';';
        }
        $manufacturers = explode(';', trim($manufacturers, ';'));
        $manufacturers = array_unique($manufacturers, SORT_REGULAR);
        $suppliers = explode(';', trim($suppliers, ';'));
        $suppliers = array_filter(array_unique($suppliers, SORT_REGULAR), 'strlen');
        $group = new Group((int)$customer->id_default_group);

        if (Group::getPriceDisplayMethod($group->id) == PS_TAX_EXC) {
            $price_display_method = false;
            $price_display_method_cartsummary = false;
        } else {
            $price_display_method = true;
            $price_display_method_cartsummary = true;
        }

        $order_total = $params['cart']->getOrderTotal($price_display_method, 3);
        $order_total_with_taxes = $params['cart']->getOrderTotal(true, 3);

        $codfeeconfs = new CodfeeConfiguration();
        $codfeeconfs = $codfeeconfs->getFeeConfiguration($id_shop, $id_lang, $customer_groups, $carrier_ref, $country, $zone, $products, $manufacturers, $suppliers, $order_total, true);

        if (!$codfeeconfs) {
            return false;
        }

        $tpl = '';
        $payment_options = array();
        $chex_payment_options = array();
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $currency = new Currency($params['cart']->cart->id_currency);
            $conv_rate = (float)$currency->conversion_rate;
        } else {
            $conv_rate = (float)$this->context->currency->conversion_rate;
        }
        foreach ($codfeeconfs as $codfeeconf) {
            if ($codfeeconf['customers'] !== 'all' && $codfeeconf['filter_by_customer'] == '1') {
                $allowed_customers = explode(';', $codfeeconf['customers']);
                if (!in_array($customer->id, $allowed_customers)) {
                    continue;
                }
            }
            if ($codfeeconf['hide_first_order'] == '1') {
                $customer_stats = $customer->getStats();
                if ((int)$customer_stats['nb_orders'] == 0) {
                    continue;
                }
                if ((int)$customer_stats['nb_orders'] > 0 && $customer->id == (int)Configuration::get('OPC_ID_CUSTOMER')) {
                    continue;
                }
            }
            if ($codfeeconf['only_stock'] == '1') {
                $no_stock = false;
                foreach ($products as $product) {
                    if ($product['quantity'] > StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute'])) {
                        $no_stock = true;
                        break;
                    }
                    if (StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']) <= 0) {
                        $no_stock = true;
                        break;
                    }
                }
                if ($no_stock) {
                    continue;
                }
            }
            if ($codfeeconf['hide_customized'] == '1') {
                $hide_customized = false;
                foreach ($products as $product) {
                    if ((int)$product['id_customization'] > 0) {
                        $hide_customized = true;
                        break;
                    }
                }
                if ($hide_customized) {
                    continue;
                }
            }
            if ($params['cart']->id > 0) {
                $total_weight = $params['cart']->getTotalWeight();
            } else {
                $total_weight = -1;
            }
            if (($codfeeconf['max_weight'] > 0 && $total_weight < $codfeeconf['max_weight']) || ($codfeeconf['max_weight'] == 0)) {
                if (($codfeeconf['min_weight'] > 0 && $codfeeconf['min_weight'] <= $total_weight) || ($codfeeconf['min_weight'] == 0)) {
                } else {
                    continue;
                }
            } else {
                continue;
            }
            $order_max = $codfeeconf['order_max'] * (float)$conv_rate;
            $order_min = $codfeeconf['order_min'] * (float)$conv_rate;
            if (($order_max > 0 && $order_total < $order_max) || ($order_max == 0)) {
                if (($order_min > 0 && $order_min <= $order_total) || ($order_min == 0)) {
                    $fee = (float)Tools::ps_round((float)$this->getFeeCost($params['cart'], $codfeeconf, $price_display_method), 2);
                    $fee_with_taxes = (float)Tools::ps_round((float)$this->getFeeCost($params['cart'], $codfeeconf, true), 2);
                    $fee_wt = (float)0.00;
                    if ($codfeeconf['free_on_freeshipping'] == '1' && $params['cart']->getOrderTotal($price_display_method, Cart::ONLY_SHIPPING) == 0) {
                        $fee = (float)0.00;
                        $fee_with_taxes = (float)0.00;
                        $fee_wt = (float)0.00;
                    }
                    if ($codfeeconf['free_on_freeshipping'] == '1' && count($params['cart']->getCartRules(CartRule::FILTER_ACTION_SHIPPING)) > 0) {
                        $fee = (float)0.00;
                        $fee_with_taxes = (float)0.00;
                        $fee_wt = (float)0.00;
                    }
                    $order_total_withoutshipping = $params['cart']->getOrderTotal($price_display_method, Cart::BOTH_WITHOUT_SHIPPING);
                    $shipping_cost = $order_total - $order_total_withoutshipping;
                    $total = $fee + $order_total;
                    $currency = new Currency($params['cart']->id_currency);
                    $this_path_ssl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
                    if (file_exists(_PS_TMP_IMG_DIR_.$this->name.'_'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType)) {
                        if (version_compare(_PS_VERSION_, '1.6', '<')) {
                            $payment_logo_url = $this_path_ssl.'img/tmp/'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType;
                        } else {
                            $payment_logo_url = $this_path_ssl.'img/tmp/'.$this->name.'_'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType;
                        }
                    } else {
                        $payment_logo_url = $this_path_ssl.'modules/codfee/views/img/payment.png';
                    }
                    $modulePretty = false;
                    if ($this->isModuleActive('prettyurls') || $this->isModuleActive('purls') || $this->isModuleActive('fsadvancedurl') || $this->isModuleActive('smartseoplus') || $this->isModuleActive('sturls')) {
                        $modulePretty = true;
                    }
                    if ($modulePretty !== false) {
                        $payment_ctrl = $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=payment&c='.$codfeeconf['id_codfee_configuration'].'&id_lang='.$id_lang;
                        $validation_ctrl = $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=validation&c='.$codfeeconf['id_codfee_configuration'].'&id_lang='.$id_lang;
                        $action = $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=validation&c='.$codfeeconf['id_codfee_configuration'].'&id_lang='.$id_lang.'&confirm=1';
                    } else {
                        $payment_ctrl = $this->context->link->getModuleLink($this->name, 'payment', array('c' => $codfeeconf['id_codfee_configuration']), true);
                        $validation_ctrl = $this->context->link->getModuleLink($this->name, 'validation', array('c' => $codfeeconf['id_codfee_configuration']), true);
                        $action = $this->context->link->getModuleLink($this->name, 'validation', array('confirm' => true));
                    }
                    $codfee_taxes = 0;
                    $taxes = $params['cart']->getOrderTotal(true) - $params['cart']->getOrderTotal(false);
                    if ($codfeeconf['type'] == '3') {
                        $cta_text = sprintf($this->l('Pay upon cash on pickup'));
                        $label_text = sprintf($this->l('Pay upon cash on pickup'));
                        $fee_wt = 0;
                    } else {
                        $remove_taxes = false;
                        if ($codfeeconf['id_tax_rule'] == '0') {
                            if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                                $carrier_tax_rate = $carrier->getTaxesRate(new Address($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                                $codfee_taxes = $fee_with_taxes - ($fee_with_taxes / (1 + (($carrier_tax_rate) / 100)));
                            }
                        } elseif ($codfeeconf['id_tax_rule'] == '9999') {
                            $codfee_taxes = 0;
                        } else {
                            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                                $address_id = (int)$params['cart']->id_address_invoice;
                            } else {
                                $address_id = (int)$params['cart']->id_address_delivery;
                            }
                            if (!Address::addressExists($address_id)) {
                                $address_id = null;
                            }
                            $address = Address::initialize($address_id, true);
                            $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf['id_tax_rule'])->getTaxCalculator();
                            $location_tax_rate = $tax_calculator->getTotalRate();
                            $codfee_taxes = $fee_with_taxes - ($fee_with_taxes / (1 + (($location_tax_rate) / 100)));
                            if ($location_tax_rate == 0) {
                                $remove_taxes = true;
                                $address = new Address();
                                $address->id_country = (int)$this->context->country->id;
                                $address->id_state   = 0;
                                $address->postcode   = 0;
                                $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf['id_tax_rule'])->getTaxCalculator();
                                $location_tax_rate = $tax_calculator->getTotalRate();
                                $codfee_taxes = $fee_with_taxes - ($fee_with_taxes / (1 + (($location_tax_rate) / 100)));
                            }
                        }
                        if (isset($remove_taxes) && $remove_taxes === true) {
                            $fee_wt = $fee_with_taxes - $codfee_taxes;
                            $fee_with_taxes = $fee_wt;
                            $total = $fee_wt + $order_total;
                        } else {
                            $fee_wt = $fee_with_taxes - $codfee_taxes;
                            $total = $fee_wt + $order_total;
                        }
                        $cta_text = sprintf($this->l('Pay with cash on delivery: %s + %s (COD fee) = %s'), Tools::displayPrice($order_total_with_taxes, $currency, false), Tools::displayPrice($fee_with_taxes, $currency, false), Tools::displayPrice($order_total_with_taxes + $fee_with_taxes, $currency, false));
                        $label_text = sprintf($this->l('Cash on delivery fee'));
                    }
                    if ($codfeeconf['payment_name'] && $codfeeconf['payment_name'] != '' && !is_null($codfeeconf['payment_name'])) {
                        $cta_text = $codfeeconf['payment_name'];
                        $cta_text = Tools::str_replace_once('{total_without_fee}',Tools::displayPrice($order_total_with_taxes, $currency, false), $cta_text);
                        $cta_text = Tools::str_replace_once('{fee}',Tools::displayPrice($fee_with_taxes, $currency, false), $cta_text);
                        $cta_text = Tools::str_replace_once('{fee_wt}',Tools::displayPrice($fee_wt, $currency, false), $cta_text);
                        $cta_text = Tools::str_replace_once('{total_with_fee}',Tools::displayPrice($order_total_with_taxes + $fee_with_taxes, $currency, false), $cta_text);
                    }
                    $this->context->smarty->assign(array(
                        'this_path'         => $this->_path,
                        'order_total'       => number_format((float)$order_total, 2, '.', ''),
                        'fee'               => number_format((float)$fee, 2, '.', ''),
                        'shipping_cost'     => number_format((float)$shipping_cost, 2, '.', ''),
                        'total'             => number_format((float)$total, 2, '.', ''),
                        'payment_text'      => preg_replace('/[\n|\r|\n\r]/i', '', $codfeeconf['payment_text']),
                        'payment_logo'      => $payment_logo_url,
                        'label_text'        => $label_text,
                        'cta_text'          => $cta_text,
                        'codfee_id'         => 'codfeeid_'.$codfeeconf['id_codfee_configuration'],
                        'codfeeconf_class'  => 'codfeeconf_'.$codfeeconf['id_codfee_configuration'],
                        'show_conf_page'    => $codfeeconf['show_conf_page'],
                        'payment_size'      => $codfeeconf['payment_size'],
                        'payment_ctrl'      => $payment_ctrl,
                        'validation_ctrl'   => $validation_ctrl,
                        'this_path_ssl'     => $this_path_ssl.'modules/'.$this->name.'/',
                        'action'            => $action
                    ));
                    if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                        $newOption = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
                        if ($codfeeconf['show_conf_page'] == '0') {
                            if ($modulePretty !== false) {
                                $action_ctrl = $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=validation&c='.$codfeeconf['id_codfee_configuration'].'&id_lang='.$id_lang;
                            } else {
                                $action_ctrl = $this->context->link->getModuleLink($this->name, 'validation', array('c' => $codfeeconf['id_codfee_configuration']), true);
                            }
                        } else {
                            if ($modulePretty !== false) {
                                $action_ctrl = $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=payment&c='.$codfeeconf['id_codfee_configuration'].'&id_lang='.$id_lang;
                            } else {
                                $action_ctrl = $this->context->link->getModuleLink($this->name, 'payment', array('c' => $codfeeconf['id_codfee_configuration']), true);
                            }
                        }
                        $inputs = array();
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_id', 'value' => $codfeeconf['id_codfee_configuration']);
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_base_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($order_total, $currency, false));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_order_total_with_taxes_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($order_total_with_taxes, $currency, false));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_fee_with_taxes_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($fee_with_taxes, $currency, false));
                        if ($price_display_method) {
                            $inputs[] = array('type' => 'hidden', 'name' => 'codfee_fee_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($fee_with_taxes, $currency, false));
                            $inputs[] = array('type' => 'hidden', 'name' => 'codfee_total_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($total, $currency, false));
                        } else {
                            $inputs[] = array('type' => 'hidden', 'name' => 'codfee_fee_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($fee_wt, $currency, false));
                            $inputs[] = array('type' => 'hidden', 'name' => 'codfee_total_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($fee_wt + $order_total, $currency, false));
                        }
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_total_with_taxes_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($fee_with_taxes + $order_total_with_taxes, $currency, false));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_total_without_taxes_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($fee_wt + $params['cart']->getOrderTotal(false, 3), $currency, false));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_taxes_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($taxes + $codfee_taxes, $currency, false));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_fee_wt_'.$codfeeconf['id_codfee_configuration'], 'value' => Tools::displayPrice($fee_wt, $currency, false));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_price_display_method_'.$codfeeconf['id_codfee_configuration'], 'value' => $price_display_method);
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_price_display_method_cartsummary_'.$codfeeconf['id_codfee_configuration'], 'value' => $price_display_method_cartsummary);
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_text_'.$codfeeconf['id_codfee_configuration'], 'value' => $this->l('Cash on delivery fee'));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_tax_enabled_'.$codfeeconf['id_codfee_configuration'], 'value' => Configuration::get('PS_TAX'));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_tax_display_'.$codfeeconf['id_codfee_configuration'], 'value' => Configuration::get('PS_TAX_DISPLAY'));
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_type_'.$codfeeconf['id_codfee_configuration'], 'value' => $codfeeconf['type']);
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_free_flag_'.$codfeeconf['id_codfee_configuration'], 'value' => $fee_with_taxes == 0 ? '1' : '0');
                        $inputs[] = array('type' => 'hidden', 'name' => 'codfee_free_txt_'.$codfeeconf['id_codfee_configuration'], 'value' => $this->l('Free'));
                        $newOption->setCallToActionText($cta_text)
                                  ->setAdditionalInformation(preg_replace('/[\n|\r|\n\r]/i', '', $codfeeconf['payment_text']))
                                  ->setLogo(file_exists(_PS_TMP_IMG_DIR_.$this->name.'_'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType) ? $this_path_ssl.'img/tmp/'.$this->name.'_'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType : '')
                                  ->setAction($action_ctrl)
                                  ->setModuleName($this->name)
                                  ->setInputs($inputs);
                        array_push($payment_options, $newOption);
                    }
                    if (Configuration::get('AEUC_FEAT_ADV_PAYMENT_API') &&
                        Configuration::get('AEUC_FEAT_ADV_PAYMENT_API') == '1' &&
                        $this->isModuleActive('advancedeucompliance') &&
                        !$this->isModuleActive('onepagecheckoutps')
                    ) {
                        $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
                        if (!in_array('hookAdvancedPaymentOptions', array_column($backtrace, 'function'))) {
                            continue;
                        }
                        if ($codfeeconf['show_conf_page'] == '1') {
                            $action = $this->context->link->getModuleLink($this->name, 'payment', array('c' => $codfeeconf['id_codfee_configuration']), true);
                        } else {
                            $action = $this->context->link->getModuleLink($this->name, 'validation', array('c' => $codfeeconf['id_codfee_configuration']), true);
                        }
                        $this->context->smarty->assign(array(
                            'action' => $action,
                            'codfee_base' => Tools::displayPrice($order_total, $currency, false),
                            'codfee_fee' => Tools::displayPrice($fee, $currency, false),
                            'codfee_total' => Tools::displayPrice($total, $currency, false),
                            'codfee_included' => $this->l('Cash on delivery fee:').' '.Tools::displayPrice($fee, $currency, false),
                        ));
                        $formHtml = $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/hook/paymentEU.tpl');
                        return array(
                            'cta_text' => $cta_text,
                            'logo' => $payment_logo_url,
                            'action' => $action,
                            'form' => $formHtml
                        );
                    }
                    if (Module::isEnabled('chex') && version_compare(_PS_VERSION_, '1.6', '>=') && version_compare(_PS_VERSION_, '1.7', '<')) {
                        if ($codfeeconf['show_conf_page'] == '0') {
                            if ($modulePretty !== false) {
                                $action_ctrl = $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=validation&c='.$codfeeconf['id_codfee_configuration'].'&id_lang='.$id_lang;
                            } else {
                                $action_ctrl = $this->context->link->getModuleLink($this->name, 'validation', array('c' => $codfeeconf['id_codfee_configuration']), true);
                            }
                        } else {
                            if ($modulePretty !== false) {
                                $action_ctrl = $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=payment&c='.$codfeeconf['id_codfee_configuration'].'&id_lang='.$id_lang;
                            } else {
                                $action_ctrl = $this->context->link->getModuleLink($this->name, 'payment', array('c' => $codfeeconf['id_codfee_configuration']), true);
                            }
                        }
                        $new_chex_option = array(
                            'cta_text' => $cta_text,
                            'logo' => $payment_logo_url,
                            'action' => $action_ctrl
                        );
                        array_push($chex_payment_options, $new_chex_option);
                    }
                    if (version_compare(_PS_VERSION_, '1.7', '<')) {
                        $tpl .= $this->display(__FILE__, 'views/templates/hook/payment.tpl');
                    }
                }
            }
        }
        if (Module::isEnabled('chex') && version_compare(_PS_VERSION_, '1.6', '>=') && version_compare(_PS_VERSION_, '1.7', '<') && count($chex_payment_options) > 0) {
            return $chex_payment_options;
        }
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            if (isset($payment_options) && !empty($payment_options)) {
                return $payment_options;
            } else {
                return false;
            }
        } else {
            return $tpl;
        }
    }

    public function hookDisplayPaymentEU($params)
    {
        if (!$this->active) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            return $this->hookDisplayPaymentEU16($params);
        }

        foreach ($params['cart']->getProducts() as $product) {
            $pd = ProductDownload::getIdFromIdProduct((int)($product['id_product']));
            if ($pd && Validate::isUnsignedInt($pd)) {
                return false;
            }
        }

        $minimum_amount = Configuration::get('COD_FEE_MIN_AMOUNT');
        $maximum_amount = Configuration::get('COD_FEE_MAX_AMOUNT');
        $fee = (float)Tools::ps_round((float)$this->getFeeCost($params['cart']), 2);
        $cartcost = $params['cart']->getOrderTotal(true, 3);
        $cartwithoutshipping = $params['cart']->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        $shippingcost = $cartcost - $cartwithoutshipping;
        $total = $fee + $cartcost;
        $id_carriers_selected_array = explode(';', Configuration::get('COD_FEE_CARRIERS'));
        $carrier_selected = new Carrier($params['cart']->id_carrier);
        $currency = (int)$params['cart']->id_currency;

        return array(
            'this_path' => $this->_path,
            'cartcost' => number_format((float)$cartcost, 2, '.', ''),
            'fee' => number_format((float)$fee, 2, '.', ''),
            'minimum_amount' => number_format((float)$minimum_amount, 2, '.', ''),
            'maximum_amount' => number_format((float)$maximum_amount, 2, '.', ''),
            'shippingcost' => number_format((float)$shippingcost, 2, '.', ''),
            'total' => number_format((float)$total, 2, '.', ''),
            'carriers_array' => $id_carriers_selected_array,
            'carrier_selected' => version_compare(_PS_VERSION_, '1.5', '<') ? $carrier_selected->id : $carrier_selected->id_reference,
            'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/',
            'cta_text' => $this->l('Pay with cash on delivery:').' '.$this->convertSign(Tools::displayPrice($cartcost, $currency, false)).' + '.$this->convertSign(Tools::displayPrice($fee, $currency, false)).' '.$this->l('(COD fee)').' = '.$this->convertSign(Tools::displayPrice($total, $currency, false)),
            'logo' => Media::getMediaPath(dirname(__FILE__).'/img/codfee.gif'),
            'action' => $this->context->link->getModuleLink($this->name, 'validation', array('confirm' => true))
        );
    }

    public function hookDisplayPaymentEU16($params)
    {
        if (!$this->active) {
            return;
        }

        if ($this->hookPayment($params) == null) {
            return null;
        }

        return $this->hookPayment($params);
    }

    public function hookDisplayPDFInvoice($params)
    {
        $order_invoice = $params['object'];
        if (!($order_invoice instanceof OrderInvoice)) {
            return false;
        }
        $order = new Order((int)$order_invoice->id_order);
        $return = '';
        if ($order->module == 'codfee') {
            $codfee = $this->getFeeFromOrderId($order->id);
            $currency = new Currency($order->id_currency);
            if ($codfee > 0) {
                $use_taxes = Configuration::get('PS_TAX');
                $return = sprintf($this->l('Shipping cost includes a cash on delivery fee of:').' '.Tools::displayPrice($codfee, $currency, false));
                if ($use_taxes) {
                    $codfee_wt = $this->getFeeWtFromOrderId($order->id);
                    $return .= ' '.sprintf($this->l('(%s of tax included)'), Tools::displayPrice($codfee - $codfee_wt, $currency, false));
                }
            }
        }
        return $return;
    }

    public function hookDisplayOrderDetail($params)
    {
        $order = $params['order'];
        if (!($order instanceof Order)) {
            return '';
        }
        $codfee = $this->getFeeFromOrderId($order->id);
        $currency = new Currency($order->id_currency);
        if ($order->module == 'codfee' && $codfee > 0) {
            $this->context->smarty->assign(array(
                'order_detail_text' => sprintf($this->l('This order has a cash on delivery fee applied of %s'), Tools::displayPrice($codfee, $currency, false))
            ));
            return $this->display(__FILE__, 'views/templates/hook/order_detail.tpl');
        }
        return '';
    }

    public function hookDisplayAdminOrder($params)
    {
        $order = new Order($params['id_order']);
        if (!($order instanceof Order)) {
            return false;
        }
        $codfee = $this->getFeeFromOrderId($order->id);
        if ($order->module == 'codfee' && $codfee > 0) {
            $currency = new Currency($order->id_currency);
            $shipping_total = Tools::displayPrice($order->total_shipping_tax_incl, $currency, false);
            if ($order->getTaxCalculationMethod() == PS_TAX_EXC) {
                $shipping_total = Tools::displayPrice($order->total_shipping_tax_excl, $currency, false);
                $codfee = $this->getFeeWtFromOrderId($order->id);
            }
            if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
                $this->context->smarty->assign(array(
                    'shipping_total' => $shipping_total,
                    'cod_fee'        => Tools::displayPrice($codfee, $currency, false)
                ));
                return $this->display(__FILE__, 'views/templates/admin/summary.tpl');
            }
            $this->context->smarty->assign(array(
                'admin_order_text' => sprintf($this->l('COD fee applied %s'), Tools::displayPrice($codfee, $currency, false)),
                'shipping_total'   => $shipping_total,
            ));
            return $this->display(__FILE__, 'views/templates/admin/order.tpl');
        }
        return '';
    }

    public function hookOrderDetailDisplayed($params)
    {
        $order = $params['order'];
        if (!($order instanceof Order)) {
            return false;
        }
        $currency = new Currency($order->id_currency);
        $cart = new Cart($order->id_cart);
        $codfee = number_format((float)$this->getFeeCost($cart), 2, '.', '');
        if ($order->module == 'codfee' && $codfee > 0) {
            $this->context->smarty->assign(array(
                'order_detail_text' => sprintf($this->l('This order has a cash on delivery fee applied of %s'), Tools::displayPrice($codfee, $currency, false))
            ));
            return $this->display(__FILE__, 'views/templates/hook/order_detail.tpl');
        }
        return '';
    }

    public function hookAdminOrder($params)
    {
        $order = new Order($params['id_order']);
        if (!($order instanceof Order)) {
            return false;
        }
        $currency = new Currency($order->id_currency);
        $cart = new Cart($order->id_cart);
        $codfee = number_format((float)$this->getFeeCost($cart), 2, '.', '');
        $return = '';
        if ($order->module == 'codfee' && $codfee > 0) {
            $this->context->smarty->assign(array(
                'admin_order_text' => sprintf($this->l('COD fee applied %s'), Tools::displayPrice($codfee, $currency, false))
            ));
            return $this->display(__FILE__, 'views/templates/admin/order.tpl');
        }
        return $return;
    }

    public function hookDisplayLeftColumnProduct()
    {
        return false;
        //return $this->hookDisplayRightColumnProduct();
    }

    //PS17
    public function hookDisplayProductAdditionalInfo()
    {
        return $this->hookDisplayRightColumnProduct();
    }

    public function hookDisplayRightColumnProduct()
    {
        $product = new Product((int)Tools::getValue('id_product'));
        if (!$this->active ||
            $product->is_virtual) {
            return '';
        }
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $customer = new Customer((int)$this->context->customer->id);
        if ($customer->id) {
            $customer_groups = $customer->getGroupsStatic($customer->id);
        } else {
            $current_group = GroupCore::getCurrent();
            $customer_groups = array($current_group->id);
        }
        $cart = $this->context->cart;
        $codfeeconfs = new CodfeeConfiguration();
        $products = array(array('id_product' => $product->id));
        $manufacturers = array($product->id_manufacturer);
        $suppliers = array($product->id_supplier);
        if ($cart->id) {
            $carrier = new Carrier($cart->id_carrier);
            $carrier = $carrier->id_reference;
            if ($carrier == 0 || is_null($carrier)) {
                $carrier = false;
            }
            $address = new Address($cart->id_address_delivery);
            $country = new Country((int)$address->id_country);
            if ($address->id_state > 0) {
                $zone = State::getIdZone((int)$address->id_state);
            } else {
                $zone = $country->getIdZone((int)$country->id);
            }
            $order_total = $cart->getOrderTotal(true, 3);
            $codfeeconfs = $codfeeconfs->getFeeConfiguration($id_shop, $id_lang, $customer_groups, $carrier, $country, $zone, $products, $manufacturers, $suppliers, $order_total, true);
        } else {
            $country = new Country((int)$this->context->country->id);
            $zone = $country->getIdZone((int)$country->id);
            $order_total = 0;
            $codfeeconfs = $codfeeconfs->getFeeConfiguration($id_shop, $id_lang, $customer_groups, false, $country, $zone, $products, $manufacturers, $suppliers, $order_total, true);
        }
        if (!$codfeeconfs) {
            return '';
        } else {
            foreach ($codfeeconfs as $codfeeconf) {
                if ($codfeeconf['show_productpage'] == '1') {
                    $product_price = $product->getPrice();
                    if ($product_price > (float)$codfeeconf['order_max'] && (float)$codfeeconf['order_max'] > 0) {
                        return '';
                    }
                    $this_path_ssl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
                    if (file_exists(_PS_TMP_IMG_DIR_.$this->name.'_'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType)) {
                        $icon_logo_url = $this_path_ssl.'img/tmp/'.$this->name.'_'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType;
                        if (version_compare(_PS_VERSION_, '1.6', '<')) {
                            $icon_logo_url = $this_path_ssl.'img/tmp/'.$codfeeconf['id_codfee_configuration'].'.'.$this->imageType;
                        }
                    } else {
                        $icon_logo_url = $this_path_ssl.'modules/codfee/views/img/product_icon.png';
                    }
                    $this->context->smarty->assign(array(
                        'icon' => $icon_logo_url,
                        'codfee_type' => $codfeeconf['type'],
                    ));

                    return $this->display(__FILE__, 'product.tpl');
                }
            }
        }
        return '';
    }

    public function getFeeAmountFromOrderId($id_order)
    {
        return Db::getInstance()->getValue(
            'SELECT `codfee`
            FROM `'._DB_PREFIX_.'orders` o
            WHERE o.`id_order` = '.(int)$id_order.';'
        );
    }

    public function hookActionValidateOrder($params)
    {
        $cart = $params['cart'];
        $order = $params['order'];
        $controller = $this->context->controller;
        if ((isset($controller->controller_type) && $controller->controller_type == 'modulefront')
            || (isset($controller->page_name) && $controller->page_name == 'module-codfee-validation')) {
            return;
        } elseif ($order->module == 'codfee') {
            $id_lang = $order->id_lang;
            $id_shop = $order->id_shop;
            $customer = new Customer((int)$order->id_customer);
            $customer_groups = $customer->getGroupsStatic((int)$customer->id);
            $carrier = new Carrier((int)$order->id_carrier);
            $address = new Address((int)$order->id_address_delivery);
            $country = new Country((int)$address->id_country);
            if ($address->id_state > 0) {
                $zone = State::getIdZone((int)$address->id_state);
            } else {
                $zone = $country->getIdZone((int)$country->id);
            }
            $manufacturers = '';
            $suppliers = '';
            $products = $order->getProducts();
            foreach ($products as $product) {
                $manufacturers .= $product['id_manufacturer'].';';
                $suppliers .= $product['id_supplier'].';';
            }
            $manufacturers = explode(';', trim($manufacturers, ';'));
            $manufacturers = array_unique($manufacturers, SORT_REGULAR);
            $suppliers = explode(';', trim($suppliers, ';'));
            $suppliers = array_filter(array_unique($suppliers, SORT_REGULAR), 'strlen');
            $group = new Group((int)$customer->id_default_group);
            if (Group::getPriceDisplayMethod($group->id) == PS_TAX_EXC) {
                $price_display_method = false;
                $price_display_method_cartsummary = false;
            } else {
                $price_display_method = true;
                $price_display_method_cartsummary = true;
            }
            $order_total = $cart->getOrderTotal($price_display_method, 3);

            $codfeeconf = new CodfeeConfiguration();
            $codfeeconf = $codfeeconf->getFeeConfiguration($id_shop, $id_lang, $customer_groups, $carrier->id_reference, $country, $zone, $products, $manufacturers, $suppliers, $order_total);
            if (!$codfeeconf) {
                return;
            } else {
                $fee = (float)Tools::ps_round((float)$this->getFeeCost($cart, $codfeeconf, true), 2);
                $fee_without_tax = (float)Tools::ps_round((float)$this->getFeeCost($cart, $codfeeconf, false), 2);
                if ($codfeeconf['free_on_freeshipping'] == '1' && $cart->getOrderTotal($price_display_method, Cart::ONLY_SHIPPING) == 0) {
                    $fee = (float)0.00;
                    $fee_without_tax = (float)0.00;
                }
                if ($codfeeconf['free_on_freeshipping'] == '1' && count($cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING)) > 0) {
                    $fee = (float)0.00;
                    $fee_without_tax = (float)0.00;
                }
            }

            $id_order = Order::getOrderByCartId($cart->id);

            $codfee_wt = $fee;
            $remove_taxes = false;
            if ($codfeeconf['id_tax_rule'] == '0') {
                if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                    $carrier_tax_rate = $carrier->getTaxesRate(new Address($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                    $codfee_wt = $fee / (1 + (($carrier_tax_rate) / 100));
                }
            } elseif ($codfeeconf['id_tax_rule'] == '9999') {
                $codfee_wt = $fee;
            } else {
                if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                    $address_id = (int)$cart->id_address_invoice;
                } else {
                    $address_id = (int)$cart->id_address_delivery;
                }
                if (!Address::addressExists($address_id)) {
                    $address_id = null;
                }
                $address = Address::initialize($address_id, true);
                $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf['id_tax_rule'])->getTaxCalculator();
                $location_tax_rate = $tax_calculator->getTotalRate();
                $codfee_taxes = $fee - ($fee / (1 + (($location_tax_rate) / 100)));
                if ($location_tax_rate == 0) {
                    $remove_taxes = true;
                    $address = new Address();
                    $address->id_country = (int)$this->context->country->id;
                    $address->id_state   = 0;
                    $address->postcode   = 0;
                    $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf['id_tax_rule'])->getTaxCalculator();
                    $location_tax_rate = $tax_calculator->getTotalRate();
                    $codfee_taxes = $fee - ($fee / (1 + (($location_tax_rate) / 100)));
                }
            }
            if (isset($remove_taxes) && $remove_taxes === true) {
                $codfee_wt = $fee - $codfee_taxes;
                $fee = $codfee_wt;
            } else {
                $codfee_wt = $fee - $codfee_taxes;
            }

            Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.bqSQL('orders').'`
            SET `codfee` = '.$fee.'
            WHERE `'.bqSQL('id_order').'` = '.(int)$id_order);

            try {
                Db::getInstance()->execute('
                    INSERT INTO `'._DB_PREFIX_.'codfee_orders` (`id_codfee_configuration`, `id_order`, `id_carrier`, `fee_amount`, `fee_amount_without_tax`)
                    VALUES ('.(int)$codfeeconf['id_codfee_configuration'].','.(int)$id_order.','.(int)$cart->id_carrier.','.(float)$fee.','.(float)$codfee_wt.')
                ');
            } catch (Exception $e) {

            }

            $total_tax_incl = (float)Tools::ps_round($order->total_paid_tax_incl + $fee, 2);
            $order->total_shipping_tax_excl = (float)Tools::ps_round($order->total_shipping_tax_excl + $codfee_wt, 2);
            $order->total_shipping_tax_incl = (float)Tools::ps_round($order->total_shipping_tax_incl + $fee, 2);
            $order->total_shipping = (float)Tools::ps_round($order->total_shipping + $fee, 2);
            $order->total_paid_tax_excl = (float)Tools::ps_round($order->total_paid_tax_excl + $codfee_wt, 2);
            $order->total_paid_tax_incl = $total_tax_incl;
            $order->total_paid = $total_tax_incl;
            $order->total_paid_real = $total_tax_incl;
            $order->update();

            // Update order_carrier
            $order = new Order((int)$id_order);
            $id_order_carrier = Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `'._DB_PREFIX_.'order_carrier`
                WHERE `id_order` = '.(int)$id_order);

            if ($id_order_carrier) {
                $order_carrier = new OrderCarrier((int)$id_order_carrier);
                //$order_carrier->shipping_cost_tax_excl = $order->total_shipping_tax_excl;
                //$order_carrier->shipping_cost_tax_incl = $order->total_shipping_tax_incl;
                $order_carrier->shipping_cost_tax_excl = (float)Tools::ps_round($order_carrier->shipping_cost_tax_excl + $codfee_wt, 2);
                $order_carrier->shipping_cost_tax_incl = (float)Tools::ps_round($order_carrier->shipping_cost_tax_incl + $fee, 2);
                $order_carrier->update();
            }

            //Update order_payment
            $payments = OrderPayment::getByOrderReference($order->reference);
            if (count($payments) > 0) {
                foreach ($payments as $key => $payment) {
                    $payment->amount = $order->total_paid_tax_incl;
                    $payment->update();
                }
            }
            /*
            $payment = new OrderPayment();
            $payment->order_reference = Tools::substr($order->reference, 0, 9);
            $payment->id_currency = $order->id_currency;
            $payment->amount = $fee;
            $payment_method = Module::getInstanceByName($order->module);
            $payment->payment_method = $payment_method->displayName;
            $payment->conversion_rate = ($order ? $order->conversion_rate : 1);
            $payment->save();
            */
        }
    }

    public function hookActionObjectOrderUpdateAfter($params)
    {
        if (strpos($_SERVER['REQUEST_URI'], 'sell/orders/list/') !== false && strpos($_SERVER['REQUEST_URI'], '/status') !== false) {
            return false;
        }
        $controller = $this->context->controller;
        if ((isset($controller->controller_type) && $controller->controller_type == 'modulefront')
            || (isset($controller->page_name) && $controller->page_name == 'module-codfee-validation')) {
            return false;
        } elseif (Tools::getIsset('update_order_status') || Tools::getIsset('update_order_status_action_bar') || Tools::getIsset('change_orders_status')) {
            return false;
        } elseif ($params['object'] instanceof Order && $params['object']->module == 'codfee') {
            if (Tools::getIsset('cart_summary')) {
                return false;
            }
            $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
            $trace_repeat = 0;
            foreach (array_column($backtrace, 'function') as $trace) {
                if ($trace == 'hookActionObjectOrderUpdateAfter') {
                    $trace_repeat++;
                }
                if ($trace_repeat > 1) {
                    return false;
                }
            }
            foreach (array_column($backtrace, 'function') as $trace) {
                if ($trace == 'changeOrdersStatusAction') {
                    return false;
                }
            }
            $order = $params['object'];
            $module = new CodFee();
            $codfeeconf = new CodfeeConfiguration((int)$module->getCodfeeConfIdFromOrderId($order->id));
            $carrier = new Carrier((int)$order->id_carrier);
            $codfee = $this->getFeeFromOrderId($order->id);
            $codfee_wt = $codfee;
            if ($codfeeconf->id_tax_rule == '0') {
                if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                    $carrier_tax_rate = $carrier->getTaxesRate(new Address($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                    $codfee_wt = $codfee / (1 + (($carrier_tax_rate) / 100));
                }
            } elseif ($codfeeconf->id_tax_rule == '9999') {
                $codfee_wt = $codfee;
            } else {
                if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                    $address_id = (int)$order->id_address_invoice;
                } else {
                    $address_id = (int)$order->id_address_delivery;
                }
                if (!Address::addressExists($address_id)) {
                    $address_id = null;
                }
                $address = Address::initialize($address_id, true);
                $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf->id_tax_rule)->getTaxCalculator();
                $codfee_wt = $codfee / (1 + (($tax_calculator->getTotalRate()) / 100));
            }
            $codfee_wt = Tools::ps_round($codfee_wt, 2);

            $cart = new Cart((int)$order->id_cart);
            $base_total_shipping_tax_incl = (float)$cart->getPackageShippingCost((int)$cart->id_carrier, true, null);
            $base_total_shipping_tax_excl = (float)$cart->getPackageShippingCost((int)$cart->id_carrier, false, null);

            $order->total_shipping_tax_incl = $base_total_shipping_tax_incl + $codfee;
            $order->total_shipping_tax_excl = $base_total_shipping_tax_excl + $codfee_wt;
            $order->total_shipping = $order->total_shipping_tax_incl;

            $id_carrier_ori = $this->getCarrierIdFromCodOrderId($order->id);

            if (!Tools::getIsset('update_order_shipping')) {
                $order->total_paid_tax_incl = $order->total_paid_tax_incl + $codfee;
                $order->total_paid_tax_excl = $order->total_paid_tax_excl + $codfee_wt;
            } elseif (Tools::getIsset('update_order_shipping') && Tools::getValue('update_order_shipping')['new_carrier_id'] != $id_carrier_ori) {
                $order->total_paid_tax_incl = $order->total_paid_tax_incl + $codfee;
                $order->total_paid_tax_excl = $order->total_paid_tax_excl + $codfee_wt;
            }

            $order->total_paid_tax_excl = $order->total_products + $order->total_wrapping_tax_excl - $order->total_discounts_tax_excl + $order->total_shipping_tax_excl;
            $order->total_paid_tax_incl = $order->total_products_wt + $order->total_wrapping_tax_incl - $order->total_discounts_tax_incl + $order->total_shipping_tax_incl;
            $order->total_paid = $order->total_paid_tax_incl;
            $order->update();

            $payments = OrderPayment::getByOrderReference($order->reference);
            if (count($payments) > 0) {
                foreach ($payments as $key => $payment) {
                    $payment->amount = $order->total_products_wt + $order->total_shipping_tax_incl + $order->total_wrapping_tax_incl - $order->total_discounts_tax_incl;
                    $payment->update();
                }
            }

            $order = new Order((int)$order->id);
            if ($order->hasInvoice()) {
                $invoices = $order->getInvoicesCollection();
                foreach ($invoices as $invoice) {
                    if ($invoice->id > 0) {
                        $order_invoice = new OrderInvoice((int)$invoice->id);
                        $order_invoice->total_shipping_tax_excl = $order->total_shipping_tax_excl + $codfee_wt;
                        $order_invoice->total_shipping_tax_incl = $order->total_shipping_tax_incl + $codfee;
                        if (!Tools::getIsset('update_order_shipping')) {
                            $order_invoice->total_paid_tax_excl = $order->total_paid_tax_excl + $codfee_wt;
                            $order_invoice->total_paid_tax_incl = $order->total_paid_tax_incl + $codfee;
                        }
                        $order_invoice->update();
                    }
                }
            }

            $id_order_carrier = Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `'._DB_PREFIX_.'order_carrier`
                WHERE `id_order` = '.(int)$order->id);
            if ($id_order_carrier) {
                $order_carrier = new OrderCarrier((int)$id_order_carrier);
                //$order_carrier->shipping_cost_tax_excl = $order->total_shipping_tax_excl;
                //$order_carrier->shipping_cost_tax_incl = $order->total_shipping_tax_incl;
                $order_carrier->shipping_cost_tax_excl = (float)Tools::ps_round($order_carrier->shipping_cost_tax_excl + $codfee_wt, 2);
                $order_carrier->shipping_cost_tax_incl = (float)Tools::ps_round($order_carrier->shipping_cost_tax_incl + $codfee, 2);
                $order_carrier->update();
            }

            if (Tools::getIsset('update_order_shipping')) {
                Db::getInstance()->execute('
                    UPDATE `'._DB_PREFIX_.bqSQL('codfee_orders').'`
                    SET `id_carrier` = '.$order->id_carrier.'
                    WHERE `'.bqSQL('id_order').'` = '.(int)$order->id
                );
            }
        }
        return true;
    }

    public function hookActionObjectOrderInvoiceUpdateAfter($params)
    {
        if (strpos($_SERVER['REQUEST_URI'], 'sell/orders/list/') !== false && strpos($_SERVER['REQUEST_URI'], '/status') !== false) {
            return false;
        }
        $controller = $this->context->controller;
        if ((isset($controller->controller_type) && $controller->controller_type == 'modulefront')
            || (isset($controller->page_name) && $controller->page_name == 'module-codfee-validation')) {
            return false;
        } elseif (Tools::getIsset('update_order_status') || Tools::getIsset('update_order_status_action_bar') || Tools::getIsset('change_orders_status')) {
            return false;
        } elseif ($params['object'] instanceof OrderInvoice) {
            if (Tools::getIsset('cart_summary')) {
                return false;
            }
            $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
            $trace_repeat = 0;
            foreach (array_column($backtrace, 'function') as $trace) {
                if ($trace == 'hookActionObjectOrderInvoiceUpdateAfter') {
                    $trace_repeat++;
                }
                if ($trace_repeat > 1) {
                    return false;
                }
            }
            foreach (array_column($backtrace, 'function') as $trace) {
                if ($trace == 'changeOrdersStatusAction') {
                    return false;
                }
            }
            $order = new Order((int)$params['object']->id_order);
            if ($order->module != $this->name) {
                return false;
            }
            $module = new CodFee();
            $codfeeconf = new CodfeeConfiguration((int)$module->getCodfeeConfIdFromOrderId($order->id));
            $carrier = new Carrier((int)$order->id_carrier);
            $codfee = $this->getFeeFromOrderId($order->id);
            $codfee_wt = $codfee;
            if ($codfeeconf->id_tax_rule == '0') {
                if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                    $carrier_tax_rate = $carrier->getTaxesRate(new Address($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                    $codfee_wt = $codfee / (1 + (($carrier_tax_rate) / 100));
                }
            } elseif ($codfeeconf->id_tax_rule == '9999') {
                $codfee_wt = $codfee;
            } else {
                if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                    $address_id = (int)$order->id_address_invoice;
                } else {
                    $address_id = (int)$order->id_address_delivery;
                }
                if (!Address::addressExists($address_id)) {
                    $address_id = null;
                }
                $address = Address::initialize($address_id, true);
                $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf->id_tax_rule)->getTaxCalculator();
                $codfee_wt = $codfee / (1 + (($tax_calculator->getTotalRate()) / 100));
            }
            $codfee_wt = Tools::ps_round($codfee_wt, 2);
            $cart = new Cart((int)$order->id_cart);
            $base_total_shipping_tax_incl = (float)$cart->getPackageShippingCost((int)$cart->id_carrier, true, null);
            $base_total_shipping_tax_excl = (float)$cart->getPackageShippingCost((int)$cart->id_carrier, false, null);
            if ($order->hasInvoice()) {
                $invoices = $order->getInvoicesCollection();
                foreach ($invoices as $invoice) {
                    if ($invoice->id > 0) {
                        $order_invoice = new OrderInvoice((int)$invoice->id);
                        $order_invoice->total_shipping_tax_excl = $base_total_shipping_tax_excl + $codfee_wt;
                        $order_invoice->total_shipping_tax_incl = $base_total_shipping_tax_incl + $codfee;
                        $order_invoice->total_paid_tax_excl = $order->total_paid_tax_excl;
                        $order_invoice->total_paid_tax_incl = $order->total_paid_tax_incl;
                        $order_invoice->update();
                    }
                }
            }
        }
        return true;
    }

    public function hookOrderConfirmation($params)
    {

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $order = $params['objOrder'];
        } else {
            $order = $params['order'];
        }
        $products = $order->getProducts();

        $this->context->smarty->assign(array(
            'order'=> $order,
            'order_products' => $products
        ));

        return $this->display(__FILE__, 'confirmation.tpl');
    }

    public function execPayment($cart)
    {
        if (!$this->_checkCurrency($cart)) {
            return false;
        } else {
            $cashOnDelivery = new CodFee();
            $fee = (float)Tools::ps_round((float)$cashOnDelivery->getFeeCost($cart), 2);
            $cartcost = $cart->getOrderTotal(true, 3);
            $cartwithoutshipping = $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
            $shippingcost = $cartcost - $cartwithoutshipping;
            $total = $fee + $cartcost;
            $cart->additional_shipping_cost = $fee;

            if (Tools::isSubmit('paymentSubmit')) {
                $authorized = false;
                if (version_compare(_PS_VERSION_, '1.4.4', '>=')) {
                    $modules = Module::getPaymentModules();
                } else {
                    $modules = $this->_getPaymentModules($cart);
                }

                foreach ($modules as $module) {
                    if ($module['name'] == 'codfee') {
                        $authorized = true;
                        break;
                    }
                }

                if (!$authorized) {
                    die($this->l('This payment method is not available.'));
                }

                $id_currency = (int)Tools::getValue('id_currency');
                if (version_compare(_PS_VERSION_, '1.5', '<')) {
                    $validateOrderCod = new ValidateOrderCod();
                    $validateOrderCod->validateOrder14($cart->id, Configuration::get('COD_FEE_STATUS'), $total, $fee, $this->displayName, null, null, $id_currency, false, $cart->secure_key);
                }

                $this->context->smarty->assign(array(
                    'total'         => $total,
                    'success'       => true,
                    'currency'      => new Currency((int)$cart->id_currency),
                ));

                return $this->display(__FILE__, 'views/templates/hook/payment_return14.tpl');
            }

            $currency = new Currency($cart->id_currency);
            $conv_rate = (float)$currency->conversion_rate;
            $carriers = explode(';', Configuration::get('COD_FEE_CARRIERS'));
            $this_path_ssl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
            $payment_logo_url = $this_path_ssl.'modules/codfee/views/img/payment.png';

            $this->context->smarty->assign(array(
                'this_path' => $this->_path,
                'nbProducts' => $cart->nbProducts(),
                'cartcost' => number_format((float)$cartcost, 2, '.', ''),
                'cartwithoutshipping' => number_format((float)$cartwithoutshipping, 2, '.', ''),
                'shippingcost' => number_format((float)$shippingcost, 2, '.', ''),
                'fee' => number_format((float)$fee, 2, '.', ''),
                'free_fee' => (float)Tools::ps_round((float)Configuration::get('COD_FREE_FEE') * (float)$conv_rate, 2),
                'currency' => new Currency((int)$cart->id_currency),
                'total' => number_format((float)$total, 2, '.', ''),
                'payment_text'      => '',
                'payment_logo'      => $payment_logo_url,
                'carrier' => $cart->id_carrier,
                'carriers' => $carriers,
                'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/codfee/'
            ));

            $this->context->smarty->assign('this_path', __PS_BASE_URI__.'modules/codfee/');

            return $this->display(__FILE__, 'views/templates/front/codfee_val14.tpl');
        }
    }

    public function getFeeCost($cart, $codfeeconf = false, $price_display_method = 0)
    {
        if (!$codfeeconf) {
            return $this->getFeeCost14($cart);
        }

        if ((Module::isInstalled('onepagecheckoutps') && Module::isEnabled('onepagecheckoutps')) ||
            (Module::isInstalled('webicheckout') && Module::isEnabled('webicheckout'))
        ) {
            $price_display_method = true;
        }

        /*
        $is_vat_valid = false;
        if (Module::isEnabled('vatnumber') &&
            Configuration::get('VATNUMBER_MANAGEMENT') &&
            Configuration::get('VATNUMBER_CHECKING')
        ) {
            $address_invoice = new Address((int)$cart->id_address_invoice);
            if (VatNumber::isApplicable((int)$address_invoice->id_country)) {
                if ($address_invoice->vat_number && $address_invoice->vat_number != '') {
                    $isAVatNumber = VatNumber::WebServiceCheck($address_invoice->vat_number);
                    if (is_array($isAVatNumber) && count($isAVatNumber) > 0) {
                        $is_vat_valid = false;
                    } else {
                        $price_display_method = 0;
                        $is_vat_valid = true;
                    }
                }
            }
        }
        */

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $currency = new Currency($cart->id_currency);
            $conv_rate = (float)$currency->conversion_rate;
        } else {
            $conv_rate = (float)$this->context->currency->conversion_rate;
        }

        $fee = 0;
        switch ($codfeeconf['amount_calc']) {
            case '0':
                $cartvalue = (float)$cart->getOrderTotal($price_display_method, 3);
                break;
            case '1':
                $cartvalue = (float)$cart->getOrderTotal($price_display_method, 4);
                break;
            case '2':
                $cartvalue = (float)$cart->getOrderTotal($price_display_method, 5);
                break;
            default:
                $cartvalue = (float)$cart->getOrderTotal($price_display_method, 3);
        }
        if ($codfeeconf['type'] == '0') {
            $free_fee = (float)Tools::ps_round((float)$codfeeconf['amount_free'] * (float)$conv_rate, 2);
            if (($free_fee < $cartvalue) && ($free_fee != 0)) {
                $fee = (float)0;
            } else {
                $fee = (float)Tools::ps_round((float)$codfeeconf['fix'] * (float)$conv_rate, 2);
            }
        } else if ($codfeeconf['type'] == '1') {
            $minimalfee = (float)Tools::ps_round((float)$codfeeconf['min'] * (float)$conv_rate, 2);
            $maximalfee = (float)Tools::ps_round((float)$codfeeconf['max'] * (float)$conv_rate, 2);
            $free_fee = (float)Tools::ps_round((float)$codfeeconf['amount_free'] * (float)$conv_rate, 2);
            $percent = (float)$codfeeconf['percentage'];
            $percent = $percent / 100;
            $fee = $cartvalue * $percent;

            if (($fee < $minimalfee) && ($minimalfee != 0)) {
                $fee = $minimalfee;
            } elseif (($fee > $maximalfee) && ($maximalfee != 0)) {
                $fee = $maximalfee;
            }

            if (($free_fee < $cartvalue) && ($free_fee != 0)) {
                $fee = 0;
            }
        } else if ($codfeeconf['type'] == '2') {
            $minimalfee = (float)Tools::ps_round((float)$codfeeconf['min'] * (float)$conv_rate, 2);
            $maximalfee = (float)Tools::ps_round((float)$codfeeconf['max'] * (float)$conv_rate, 2);
            $free_fee = (float)Tools::ps_round((float)$codfeeconf['amount_free'] * (float)$conv_rate, 2);
            $percent = (float)$codfeeconf['percentage'];
            $percent = $percent / 100;
            $fee_tax = (float)Tools::ps_round((float)$codfeeconf['fix'] * (float)$conv_rate, 2);
            $fee = ($cartvalue * $percent) + $fee_tax;

            if (($fee < $minimalfee) && ($minimalfee != 0)) {
                $fee = $minimalfee;
            } else if (($fee > $maximalfee) && ($maximalfee != 0)) {
                $fee = $maximalfee;
            }

            if (($free_fee < $cartvalue) && ($free_fee != 0)) {
                $fee = 0;
            }
        }
        if ($codfeeconf['round'] == '1') {
            $cartvalue = (float)$cart->getOrderTotal($price_display_method, 3);
            $cart_not_rounded = $cartvalue + $fee;
            $cart_rounded = ceil($cart_not_rounded);
            $diff_to_addfee = $cart_rounded - $cart_not_rounded;
            if ($diff_to_addfee > 0) {
                $fee = $fee + $diff_to_addfee;
            }
        }
        if ((Module::isInstalled('onepagecheckoutps') && Module::isEnabled('onepagecheckoutps')) ||
            (Module::isInstalled('webicheckout') && Module::isEnabled('webicheckout'))
        ) {
            $backtrace = debug_backtrace();
            foreach ($backtrace as $trace) {
                if ($trace['function'] == 'addModulesExtraFee') {
                    $remove_taxes = false;
                    if ($codfeeconf['id_tax_rule'] == '0') {
                        $carrier = new Carrier((int)$cart->id_carrier);
                        if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                            $carrier_tax_rate = $carrier->getTaxesRate(new Address($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                            $codfee_taxes = $fee - ($fee / (1 + (($carrier_tax_rate) / 100)));
                        }
                    } elseif ($codfeeconf['id_tax_rule'] == '9999') {
                        $codfee_taxes = 0;
                    } else {
                        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                            $address_id = (int)$cart->id_address_invoice;
                        } else {
                            $address_id = (int)$cart->id_address_delivery;
                        }
                        if (!Address::addressExists($address_id)) {
                            $address_id = null;
                        }
                        $address = Address::initialize($address_id, true);
                        $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf['id_tax_rule'])->getTaxCalculator();
                        $location_tax_rate = $tax_calculator->getTotalRate();
                        $codfee_taxes = $fee - ($fee / (1 + (($location_tax_rate) / 100)));
                        if ($location_tax_rate == 0) {
                            $remove_taxes = true;
                            $address = new Address();
                            $address->id_country = (int)$this->context->country->id;
                            $address->id_state   = 0;
                            $address->postcode   = 0;
                            $tax_calculator = TaxManagerFactory::getManager($address, $codfeeconf['id_tax_rule'])->getTaxCalculator();
                            $location_tax_rate = $tax_calculator->getTotalRate();
                            $codfee_taxes = $fee - ($fee / (1 + (($location_tax_rate) / 100)));
                        }
                    }
                    if (isset($remove_taxes) && $remove_taxes === true) {
                        $fee_wt = $fee - $codfee_taxes;
                        $fee = $fee_wt;
                    }
                    break;
                }
            }
        }
        return (float)$fee;
    }

    public function getFeeCost14($cart, $price_display_method = 0)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $currency = new Currency($cart->id_currency);
            $conv_rate = (float)$currency->conversion_rate;
        } else {
            $conv_rate = (float)$this->context->currency->conversion_rate;
        }

        if (Configuration::get('COD_FEE_TYPE') == 0) {
            $free_fee = (float)Tools::ps_round((float)Configuration::get('COD_FREE_FEE') * (float)$conv_rate, 2);
            $cartvalue = (float)$cart->getOrderTotal($price_display_method, 3);

            if (($free_fee < $cartvalue) && ($free_fee != 0)) {
                return (float)0;
            } else {
                return (float)Tools::ps_round((float)Configuration::get('COD_FEE') * (float)$conv_rate, 2);
            }
        } else if (Configuration::get('COD_FEE_TYPE') == 1) {
            $minimalfee = (float)Tools::ps_round((float)Configuration::get('COD_FEE_MIN') * (float)$conv_rate, 2);
            $maximalfee = (float)Tools::ps_round((float)Configuration::get('COD_FEE_MAX') * (float)$conv_rate, 2);
            $free_fee = (float)Tools::ps_round((float)Configuration::get('COD_FREE_FEE') * (float)$conv_rate, 2);
            $cartvalue = (float)$cart->getOrderTotal($price_display_method, 3);
            $percent = (float)Configuration::get('COD_FEE_TAX');
            $percent = $percent / 100;
            $fee = $cartvalue * $percent;

            if (($fee < $minimalfee) && ($minimalfee != 0)) {
                $fee = $minimalfee;
            } elseif (($fee > $maximalfee) && ($maximalfee != 0)) {
                $fee = $maximalfee;
            }

            if (($free_fee < $cartvalue) && ($free_fee != 0)) {
                $fee = 0;
            }

            return (float)$fee;
        } else if (Configuration::get('COD_FEE_TYPE') == 2) {
            $minimalfee = (float)Tools::ps_round((float)Configuration::get('COD_FEE_MIN') * (float)$conv_rate, 2);
            $maximalfee = (float)Tools::ps_round((float)Configuration::get('COD_FEE_MAX') * (float)$conv_rate, 2);
            $free_fee = (float)Tools::ps_round((float)Configuration::get('COD_FREE_FEE') * (float)$conv_rate, 2);
            $cartvalue = (float)$cart->getOrderTotal($price_display_method, 3);
            $percent = (float)Configuration::get('COD_FEE_TAX');
            $percent = $percent / 100;
            $fee_tax = (float)Tools::ps_round((float)Configuration::get('COD_FEE') * (float)$conv_rate, 2);
            $fee = ($cartvalue * $percent) + $fee_tax;

            if (($fee < $minimalfee) && ($minimalfee != 0)) {
                $fee = $minimalfee;
            } else if (($fee > $maximalfee) && ($maximalfee != 0)) {
                $fee = $maximalfee;
            }

            if (($free_fee < $cartvalue) && ($free_fee != 0)) {
                $fee = 0;
            }

            return (float)$fee;
        }
    }

    public function getCodfeeConfIdFromOrderId($id_order)
    {
        return Db::getInstance()->getValue('
            SELECT `id_codfee_configuration` FROM `'._DB_PREFIX_.bqSQL('codfee_orders').'`
            WHERE `'.bqSQL('id_order').'` = '.(int)$id_order);
    }

    public function getCarrierIdFromCodOrderId($id_order)
    {
        return Db::getInstance()->getValue('
            SELECT `id_carrier` FROM `'._DB_PREFIX_.bqSQL('codfee_orders').'`
            WHERE `'.bqSQL('id_order').'` = '.(int)$id_order);
    }

    public function _checkCurrency($cart)
    {
        $currency_order = new Currency((int)$cart->id_currency);
        $currencies_module = $this->getCurrency();

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
    * @param Object Address $the_address that needs to be txt formated
    * @return String the txt formated address block
    */

    protected function _getFormatedAddress14(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }

    private function convertSign($s)
    {
        return str_replace(array('€', '£', '¥'), array(chr(128), chr(163), chr(165)), $s);
    }

    private function _getPaymentModules($cart)
    {
        $id_customer = (int)($cart->id_customer);
        $billing = new Address((int)($cart->id_address_invoice));

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT DISTINCT h.`id_hook`, m.`name`, hm.`position`
            FROM `'._DB_PREFIX_.'module_country` mc
            LEFT JOIN `'._DB_PREFIX_.'module` m ON m.`id_module` = mc.`id_module`
            INNER JOIN `'._DB_PREFIX_.'module_group` mg ON (m.`id_module` = mg.`id_module`)
            INNER JOIN `'._DB_PREFIX_.'customer_group` cg on (cg.`id_group` = mg.`id_group` AND cg.`id_customer` = '.(int)($id_customer).')
            LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
            LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
        WHERE h.`name` = \'payment\'
        AND mc.id_country = '.(int)($billing->id_country).'
        AND m.`active` = 1
        ORDER BY hm.`position`, m.`name` DESC');

        return $result;
    }

    protected function initSQLCodfeeConfiguration()
    {
        Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration` (
            `id_codfee_configuration` int(10) unsigned NOT NULL auto_increment,
            `name` VARCHAR(100) NULL,
            `type` int(1) unsigned NOT NULL DEFAULT "1",
            `amount_calc` tinyint(1) unsigned NOT NULL DEFAULT "9",
            `fix` decimal(10,3) NULL DEFAULT "0.000",
            `percentage` decimal(10,3) NULL DEFAULT "0.000",
            `id_tax_rule` int(10) unsigned NOT NULL DEFAULT "0",
            `min` decimal(10,3) NULL DEFAULT "0.000",
            `max` decimal(10,3) NULL DEFAULT "0.000",
            `order_min` decimal(10,3) NULL DEFAULT "0.000",
            `order_max` decimal(10,3) NULL DEFAULT "0.000",
            `amount_free` decimal(10,3) NULL DEFAULT "0.000",
            `groups` VARCHAR(250) NULL,
            `filter_by_customer` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `customers` TEXT NULL,
            `carriers` VARCHAR(250) NULL,
            `countries` VARCHAR(1000) NULL,
            `zones` VARCHAR(250) NULL,
            `categories` TEXT NULL,
            `filter_by_product` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `products` TEXT NULL,
            `manufacturers` VARCHAR(4500) NULL,
            `suppliers` VARCHAR(4500) NULL,
            `initial_status` int(1) unsigned NOT NULL DEFAULT "3",
            `show_conf_page` tinyint(1) unsigned NOT NULL DEFAULT "1",
            `free_on_freeshipping` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `hide_first_order` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `only_stock` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `round` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `show_productpage` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `hide_customized` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `active` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `priority` int(1) unsigned DEFAULT "0",
            `position` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `payment_size` varchar(10) NOT NULL DEFAULT "col-md-12",
            `min_weight` decimal(10,3) NULL DEFAULT "0.000",
            `max_weight` decimal(10,3) NULL DEFAULT "0.000",
            `id_shop` tinyint(1) unsigned NOT NULL DEFAULT "0",
            `date_add` DATETIME,
            `date_upd` DATETIME,
        PRIMARY KEY (`id_codfee_configuration`),
        KEY `id_codfee_configuration` (`id_codfee_configuration`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

        Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration_lang` (
            `id_codfee_configuration` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned,
            `payment_name` VARCHAR(250) NULL,
            `payment_text` text NULL,
        PRIMARY KEY (`id_codfee_configuration`, `id_lang`),
        KEY `id_codfee_configuration` (`id_codfee_configuration`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

        Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_orders` (
            `id_codfee_orders` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_codfee_configuration` int(10) unsigned NOT NULL,
            `id_order` int(11) unsigned NOT NULL,
            `id_carrier` int(5) unsigned NOT NULL,
            `fee_amount` decimal(10,3) DEFAULT "0.000",
            `fee_amount_without_tax` decimal(10,3) DEFAULT "0.000",
            PRIMARY KEY (`id_codfee_orders`),
            KEY `id_codfee_orders` (`id_codfee_orders`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

        Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration_shop` (
            `id_codfee_configuration` int(10) unsigned NOT NULL,
            `id_shop` int(11) unsigned NOT NULL,
        PRIMARY KEY (`id_codfee_configuration`, `id_shop`),
        KEY `id_codfee_configuration` (`id_codfee_configuration`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

        try {
            Db::getInstance()->Execute('
            ALTER TABLE `'.pSQL(_DB_PREFIX_.'orders').'`
                ADD `codfee` decimal(10,3) NOT NULL DEFAULT "0.000";');
        } catch (Exception $e) {
            return true;
        }

        return true;
    }

    protected function uninstallSQL()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration_lang`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration_shop`');
        //Db::getInstance()->Execute('ALTER TABLE `'.pSQL(_DB_PREFIX_.'orders').'` DROP `codfee`');
        return true;
    }

    protected function addTab($tabName, $tabClassName)
    {
        $id_tab = Tab::getIdFromClassName($tabClassName);
        $tabNames = array();

        if (!$id_tab) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                $langs = Language::getlanguages(false);

                foreach ($langs as $l) {
                    $tabNames[$l['id_lang']] = Tools::substr($tabName, 0, 32);
                }

                $tab = new Tab();
                $tab->module = $this->name;
                $tab->name = $tabNames;
                $tab->class_name = $tabClassName;
                $tab->id_parent = -1;

                if (!$tab->save()) {
                    return false;
                }
            } else {
                $tab = new Tab();
                $tab->class_name = $tabClassName;
                $tab->id_parent = -1;
                $tab->module = $this->name;
                $languages = Language::getLanguages();

                foreach ($languages as $language) {
                    $tab->name[$language['id_lang']] = Tools::substr($this->l($tabName), 0, 32);
                }

                if (!$tab->add()) {
                    return false;
                }
            }
        }
        return true;
    }

    private function removeTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);

        if ($idTab) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }

        return false;
    }

    public function getFeeWtFromOrderId($id_order)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT o.`fee_amount_without_tax` FROM `'.pSQL(_DB_PREFIX_.$this->name).'_orders` o WHERE o.`id_order` = '.(int)$id_order.';');
    }

    public function getFeeFromOrderId($id_order)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT o.`codfee` FROM `'._DB_PREFIX_.'orders` o WHERE o.`id_order` = '.(int)$id_order.';');
    }

    public function setFeeFromOrderId($id_order)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT o.`codfee` FROM `'._DB_PREFIX_.'orders` o WHERE o.`id_order` = '.(int)$id_order.';');
    }

    public function isModuleActive($name_module, $function_exist = false)
    {
        if (version_compare(_PS_VERSION_, '1.7.2', '>=')) {
            return false;
        }
        if (Module::isInstalled($name_module)) {
            $module = Module::getInstanceByName($name_module);
            if ((Validate::isLoadedObject($module) && $module->active)
                || (Validate::isLoadedObject($module) && $name_module == 'prettyurls')
                || (Validate::isLoadedObject($module) && $name_module == 'purls')
                || (Validate::isLoadedObject($module) && $name_module == 'fsadvancedurl')
                || (Validate::isLoadedObject($module) && $name_module == 'smartseoplus')
                || (Validate::isLoadedObject($module) && $name_module == 'sturls')
            ) {
                if ($function_exist) {
                    if (method_exists($module, $function_exist)) {
                        return $module;
                    } else {
                        return false;
                    }
                }
                return $module;
            }
        }
        return false;
    }

    public static function getProductsLite($id_lang, $only_active = false, $front = false)
    {
        $sql = 'SELECT p.`id_product`, CONCAT(p.`reference`, " - ", pl.`name`) as name FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
                WHERE pl.`id_lang` = '.(int)$id_lang.
               ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
               ($only_active ? ' AND product_shop.`active` = 1' : '');
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return ($rq);
    }

    protected function createOrderCartRules(
        Order $order,
        Cart $cart,
        $order_list,
        $total_reduction_value_ti,
        $total_reduction_value_tex,
        $id_order_state
    ) {
        $cart_rule_used = array();

        // prepare cart calculator to correctly get the value of each cart rule
        $calculator = $cart->newCalculator($order->product_list, $cart->getCartRules(), $order->id_carrier);
        $calculator->processCalculation(_PS_PRICE_COMPUTE_PRECISION_);
        $cartRulesData = $calculator->getCartRulesData();

        $cart_rules_list = array();
        foreach ($cartRulesData as $cartRuleData) {
            $cartRule = $cartRuleData->getCartRule();
            // Here we need to get actual values from cart calculator
            $values = array(
                'tax_incl' => $cartRuleData->getDiscountApplied()->getTaxIncluded(),
                'tax_excl' => $cartRuleData->getDiscountApplied()->getTaxExcluded(),
            );

            // If the reduction is not applicable to this order, then continue with the next one
            if (!$values['tax_excl']) {
                continue;
            }

            // IF
            //  This is not multi-shipping
            //  The value of the voucher is greater than the total of the order
            //  Partial use is allowed
            //  This is an "amount" reduction, not a reduction in % or a gift
            // THEN
            //  The voucher is cloned with a new value corresponding to the remainder
            $remainingValue = $cartRule->reduction_amount - $values[$cartRule->reduction_tax ? 'tax_incl' : 'tax_excl'];
            if (count($order_list) == 1 && $remainingValue > 0 && $cartRule->partial_use == 1 && $cartRule->reduction_amount > 0) {
                // Create a new voucher from the original
                $voucher = new CartRule((int) $cartRule->id); // We need to instantiate the CartRule without lang parameter to allow saving it
                unset($voucher->id);

                // Set a new voucher code
                $voucher->code = empty($voucher->code) ? substr(md5($order->id . '-' . $order->id_customer . '-' . $cartRule->id), 0, 16) : $voucher->code . '-2';
                if (preg_match('/\-([0-9]{1,2})\-([0-9]{1,2})$/', $voucher->code, $matches) && $matches[1] == $matches[2]) {
                    $voucher->code = preg_replace('/' . $matches[0] . '$/', '-' . (intval($matches[1]) + 1), $voucher->code);
                }

                // Set the new voucher value
                $voucher->reduction_amount = $remainingValue;
                if ($voucher->reduction_tax) {
                    // Add total shipping amout only if reduction amount > total shipping
                    if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_incl) {
                        $voucher->reduction_amount -= $order->total_shipping_tax_incl;
                    }
                } else {
                    // Add total shipping amout only if reduction amount > total shipping
                    if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_excl) {
                        $voucher->reduction_amount -= $order->total_shipping_tax_excl;
                    }
                }
                if ($voucher->reduction_amount <= 0) {
                    continue;
                }

                if ($this->context->customer->isGuest()) {
                    $voucher->id_customer = 0;
                } else {
                    $voucher->id_customer = $order->id_customer;
                }

                $voucher->quantity = 1;
                $voucher->reduction_currency = $order->id_currency;
                $voucher->quantity_per_user = 1;
                if ($voucher->add()) {
                    // If the voucher has conditions, they are now copied to the new voucher
                    CartRule::copyConditions($cartRule->id, $voucher->id);
                    $orderLanguage = new Language((int) $order->id_lang);

                    $params = array(
                        '{voucher_amount}' => Tools::displayPrice($voucher->reduction_amount, $this->context->currency, false),
                        '{voucher_num}' => $voucher->code,
                        '{firstname}' => $this->context->customer->firstname,
                        '{lastname}' => $this->context->customer->lastname,
                        '{id_order}' => $order->reference,
                        '{order_name}' => $order->getUniqReference(),
                    );
                    Mail::Send(
                        (int) $order->id_lang,
                        'voucher',
                        Context::getContext()->getTranslator()->trans(
                            'New voucher for your order %s',
                            array($order->reference),
                            'Emails.Subject',
                            $orderLanguage->locale
                        ),
                        $params,
                        $this->context->customer->email,
                        $this->context->customer->firstname . ' ' . $this->context->customer->lastname,
                        null, null, null, null, _PS_MAIL_DIR_, false, (int) $order->id_shop
                    );
                }

                $values['tax_incl'] = $order->total_products_wt - $total_reduction_value_ti;
                $values['tax_excl'] = $order->total_products - $total_reduction_value_tex;
                if (1 == $voucher->free_shipping) {
                    $values['tax_incl'] += $order->total_shipping_tax_incl;
                    $values['tax_excl'] += $order->total_shipping_tax_excl;
                }
            }
            $total_reduction_value_ti += $values['tax_incl'];
            $total_reduction_value_tex += $values['tax_excl'];

            $order->addCartRule($cartRule->id, $cartRule->name, $values, 0, $cartRule->free_shipping);

            if ($id_order_state != Configuration::get('PS_OS_ERROR') && $id_order_state != Configuration::get('PS_OS_CANCELED') && !in_array($cartRule->id, $cart_rule_used)) {
                $cart_rule_used[] = $cartRule->id;

                // Create a new instance of Cart Rule without id_lang, in order to update its quantity
                $cart_rule_to_update = new CartRule((int) $cartRule->id);
                $cart_rule_to_update->quantity = max(0, $cart_rule_to_update->quantity - 1);
                $cart_rule_to_update->update();
            }

            $cart_rules_list[] = array(
                'voucher_name' => $cartRule->name,
                'voucher_reduction' => ($values['tax_incl'] != 0.00 ? '-' : '') . Tools::displayPrice($values['tax_incl'], $this->context->currency, false),
            );
        }

        return $cart_rules_list;
    }
}
