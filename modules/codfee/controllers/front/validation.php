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

include_once(_PS_MODULE_DIR_.'codfee/classes/ValidateOrderCod.php');

class CodFeeValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        /*
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'codfee') {
                $authorized = true;
                break;
            }
        }
        
        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }
        */
        
        $cashOnDelivery = new CodFee();
        $codfeeconf = new CodfeeConfiguration(Tools::getValue('c'));
        if (!$codfeeconf->id_codfee_configuration) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }
        $fee = (float)Tools::ps_round((float)$cashOnDelivery->getFeeCost($cart, (array)$codfeeconf, true), 2);
        if ($codfeeconf->free_on_freeshipping == '1' && $cart->getOrderTotal(true, Cart::ONLY_SHIPPING) == 0) {
            $fee = (float)0.00;
        }
        if ($codfeeconf->free_on_freeshipping == '1' && count($cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING)) > 0) {
            $fee = (float)0.00;
        }

        $remove_taxes = false;
        if ($codfeeconf->id_tax_rule != '0' && $codfeeconf->id_tax_rule != '9999') {
            $codfee_wt = $fee;
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

        $order_total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $total = $fee + $order_total;
        $cart->additional_shipping_cost = $fee;

        if ($codfeeconf->type == '3') {
            $displayName = $this->module->l('Cash on pickup', 'validation');
        } else {
            $displayName = $cashOnDelivery->displayName;
        }

        $validateOrderCod = new ValidateOrderCod();
        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            $validateOrderCod->validateOrder177((int)$this->context->cart->id, $codfeeconf->initial_status, $total, $fee, $codfeeconf->id_codfee_configuration, $displayName, null, null, null, false, $cart->secure_key);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$cashOnDelivery->id.'&id_order='.$validateOrderCod->currentOrder.'&key='.$cart->secure_key.'&c='.$codfeeconf->id);
        } elseif (version_compare(_PS_VERSION_, '1.7.6.1', '>=')) {
            $validateOrderCod->validateOrder176((int)$this->context->cart->id, $codfeeconf->initial_status, $total, $fee, $codfeeconf->id_codfee_configuration, $displayName, null, null, null, false, $cart->secure_key);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$cashOnDelivery->id.'&id_order='.$validateOrderCod->currentOrder.'&key='.$cart->secure_key.'&c='.$codfeeconf->id);
        } elseif (version_compare(_PS_VERSION_, '1.7.4', '>=')) {
            $validateOrderCod->validateOrder174((int)$this->context->cart->id, $codfeeconf->initial_status, $total, $fee, $codfeeconf->id_codfee_configuration, $displayName, null, null, null, false, $cart->secure_key);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$cashOnDelivery->id.'&id_order='.$validateOrderCod->currentOrder.'&key='.$cart->secure_key.'&c='.$codfeeconf->id);
        } elseif (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $validateOrderCod->validateOrder17((int)$this->context->cart->id, $codfeeconf->initial_status, $total, $fee, $codfeeconf->id_codfee_configuration, $displayName, null, null, null, false, $cart->secure_key);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$cashOnDelivery->id.'&id_order='.$validateOrderCod->currentOrder.'&key='.$cart->secure_key.'&c='.$codfeeconf->id);
        } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $validateOrderCod->validateOrder16((int)$this->context->cart->id, $codfeeconf->initial_status, $total, $fee, $codfeeconf->id_codfee_configuration, $displayName, null, null, null, false, $cart->secure_key);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$cashOnDelivery->id.'&id_order='.$validateOrderCod->currentOrder.'&key='.$cart->secure_key.'&c='.$codfeeconf->id);
        } elseif (version_compare(_PS_VERSION_, '1.5.3', '>=')) {
            $validateOrderCod->validateOrder153((int)$this->context->cart->id, $codfeeconf->initial_status, $total, $fee, $codfeeconf->id_codfee_configuration, $displayName, null, null, null, false, $cart->secure_key);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$cashOnDelivery->id.'&id_order='.$validateOrderCod->currentOrder.'&key='.$cart->secure_key.'&c='.$codfeeconf->id);
        } else {
            $validateOrderCod->validateOrder15((int)$this->context->cart->id, $codfeeconf->initial_status, $total, $fee, $codfeeconf->id_codfee_configuration, $displayName, null, null, null, false, $cart->secure_key);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$cashOnDelivery->id.'&id_order='.$validateOrderCod->currentOrder.'&key='.$cart->secure_key.'&c='.$codfeeconf->id);
        }
    }
}
