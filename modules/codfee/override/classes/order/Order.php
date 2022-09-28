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
*  @copyright 2022 idnovate
*  @license   See above
*/

class Order extends OrderCore
{
    public function refreshShippingCost()
    {
        if ($this->module != 'codfee') {
            return parent::refreshShippingCost();
        }
        if (
            Tools::isSubmit('submitAddaddress') ||
            Tools::isSubmit('submitAddressShipping')
        ) {
            return $this;
        }
        /*if (Tools::isSubmit('action') && Tools::getValue('action')=='editProductOnOrder') {
            $payments = OrderPayment::getByOrderReference($this->reference);
            if (count($payments) > 0) {
                foreach ($payments as $key => $payment) {
                    $payment->amount = $this->total_paid_tax_incl;
                    $payment->update();
                }
            }
            return $this;
        }*/
        if (empty($this->id)) {
            return false;
        }
        parent::refreshShippingCost();
        $old_cart = new Cart((int) $this->id_cart);
        $new_cart = $old_cart->duplicate();
        $new_cart = $new_cart['cart'];
        $new_cart->id_address_delivery = (int)$this->id_address_delivery;
        if (Tools::isSubmit('shipping_carrier') && Tools::getValue('shipping_carrier')) {
            $id_carrier = (int)Tools::getValue('shipping_carrier');
        } else {
            $id_carrier = (int)$this->id_carrier;
        }
        $new_cart->id_carrier = (int)$id_carrier;
        //remove all products : cart (maybe change in the meantime)
        foreach ($new_cart->getProducts() as $product) {
            $new_cart->deleteProduct((int) $product['id_product'], (int) $product['id_product_attribute']);
        }
        // add real order products
        foreach ($this->getProducts() as $product) {
            $new_cart->updateQty(
                $product['product_quantity'],
                (int) $product['product_id'],
                null,
                false,
                'up',
                0,
                null,
                true,
                true
            ); // - skipAvailabilityCheckOutOfStock
        }
        $order = new Order((int)$this->id);
        $module = Module::getInstanceByName($this->module);
        $customer = new Customer((int)$new_cart->id_customer);
        $group = new Group((int)$customer->id_default_group);
        if ($group->price_display_method == '1') {
            $price_display_method = false;
        } else {
            $price_display_method = true;
        }
        $base_total_shipping_tax_incl = (float)$new_cart->getPackageShippingCost((int)$new_cart->id_carrier, true, null);
        $base_total_shipping_tax_excl = (float)$new_cart->getPackageShippingCost((int)$new_cart->id_carrier, false, null);
        $old_fee_wt = 0;
        $new_fee_wt = 0;
        $old_fee = Db::getInstance()->getValue('
            SELECT codfee FROM `'._DB_PREFIX_.bqSQL('orders').'`
            WHERE `'.bqSQL('id_order').'` = '.(int)$this->id);
        $id_lang = $this->id_lang;
        $id_shop = $this->id_shop;
        $customer_groups = $customer->getGroupsStatic((int)$customer->id);
        $carrier = new Carrier((int)$new_cart->id_carrier);
        $address = new Address((int)$new_cart->id_address_delivery);
        $country = new Country((int)$address->id_country);
        if ($address->id_state > 0) {
            $zone = State::getIdZone((int)$address->id_state);
        } else {
            $zone = $country->getIdZone((int)$country->id);
        }
        $manufacturers = '';
        $suppliers = '';
        $products = $new_cart->getProducts();
        foreach ($products as $product) {
            $manufacturers .= $product['id_manufacturer'].';';
            $suppliers .= $product['id_supplier'].';';
        }
        $manufacturers = explode(';', trim($manufacturers, ';'));
        $manufacturers = array_unique($manufacturers, SORT_REGULAR);
        $suppliers = explode(';', trim($suppliers, ';'));
        $suppliers = array_unique($suppliers, SORT_REGULAR);
        $group = new Group((int)$customer->id_default_group);
        if ($group->price_display_method == '1') {
            $price_display_method = false;
        } else {
            $price_display_method = true;
        }
        $order_total = $new_cart->getOrderTotal($price_display_method, 3);

        $codfeeconf = new CodfeeConfiguration();
        $codfeeconf = $codfeeconf->getFeeConfiguration($id_shop, $id_lang, $customer_groups, $carrier->id_reference, $country, $zone, $products, $manufacturers, $suppliers, $order_total);
        if (!$codfeeconf['id_codfee_configuration'] || $codfeeconf['id_codfee_configuration'] == null) {
            $new_fee = $old_fee;
            $id_codfee_configuration = $module->getCodfeeConfIdFromOrderId($this->id);
        } else {
            $new_fee = (float)Tools::ps_round((float)$module->getFeeCost($new_cart, (array)$codfeeconf, $price_display_method), 2);
            $id_codfee_configuration = $codfeeconf['id_codfee_configuration'];
        }
        $carrier = new Carrier((int)$id_carrier);
        if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
            $this->carrier_tax_rate = $carrier->getTaxesRate(new Address($new_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
            $new_fee_wt = $new_fee / (1 + (($this->carrier_tax_rate) / 100));
            $new_fee_wt = (float)Tools::ps_round($new_fee_wt, 2);
            $old_fee_wt = $old_fee / (1 + (($this->carrier_tax_rate) / 100));
            $old_fee_wt = (float)Tools::ps_round($old_fee_wt, 2);
        }
        $new_shipping_tax_incl = $base_total_shipping_tax_incl + $new_fee;
        $new_shipping_tax_excl = $base_total_shipping_tax_excl + $new_fee_wt;
        $this->total_shipping_tax_excl = $new_shipping_tax_excl;
        $this->total_shipping_tax_incl = $new_shipping_tax_incl;
        $this->total_shipping = $this->total_shipping_tax_incl;
        $this->total_paid_tax_excl = $this->total_products + $this->total_shipping_tax_excl + $this->total_wrapping_tax_excl - $this->total_discounts_tax_excl;
        $this->total_paid_tax_incl = $this->total_products_wt + $this->total_shipping_tax_incl + $this->total_wrapping_tax_incl - $this->total_discounts_tax_incl;
        $this->total_paid_real = $this->total_paid_tax_incl;
        $this->total_paid = $this->total_paid_tax_incl;
        $this->update();
        if(Tools::isSubmit('action') && Tools::getValue('action')=='addProductOnOrder') {
            $this->total_paid_tax_excl = $this->total_paid_tax_excl;
            $this->total_paid_tax_incl = $this->total_paid_tax_incl;
            $this->total_paid_real = $this->total_paid_tax_incl;
            $this->total_paid = $this->total_paid_tax_incl;
            $this->update();
        }
        $id_order_carrier = Db::getInstance()->getValue('
            SELECT `id_order_carrier`
            FROM `'._DB_PREFIX_.'order_carrier`
            WHERE `id_order` = '.(int)$this->id);
        if ($id_order_carrier) {
            $order_carrier = new OrderCarrier((int)$id_order_carrier);
            $order_carrier->shipping_cost_tax_excl = $new_shipping_tax_excl;
            $order_carrier->shipping_cost_tax_incl = $new_shipping_tax_incl;
            $order_carrier->update();
        }
        $payments = OrderPayment::getByOrderReference($this->reference);
        if (count($payments) > 0) {
            foreach ($payments as $key => $payment) {
                $payment->amount = $this->total_products_wt + $this->total_shipping_tax_incl + $this->total_wrapping_tax_incl - $this->total_discounts_tax_incl;
                $payment->update();
            }
        }
        if ($this->hasInvoice()) {
            $order_invoice = OrderInvoice::getInvoiceByNumber($this->invoice_number);
            if ($order_invoice->id > 0) {
                $order_invoice->total_paid_tax_excl = $this->total_paid_tax_excl;
                $order_invoice->total_paid_tax_incl = $this->total_paid_tax_incl;
                $order_invoice->total_shipping_tax_excl = $this->total_shipping_tax_excl;
                $order_invoice->total_shipping_tax_incl = $this->total_shipping_tax_incl;
                $order_invoice->update();
            }
        }
        Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.bqSQL('orders').'`
            SET `codfee` = '.(float)$new_fee.'
            WHERE `'.bqSQL('id_order').'` = '.(int)$this->id
        );
        Db::getInstance()->getValue('
            UPDATE `'._DB_PREFIX_.bqSQL('codfee_orders').'`
            SET `fee_amount` = '.(float)$new_fee.',
            `id_codfee_configuration` = '.(int)$id_codfee_configuration.'
            WHERE `'.bqSQL('id_order').'` = '.(int)$this->id
        );
        $old_cart->delete();
        return $this;
    }
}
