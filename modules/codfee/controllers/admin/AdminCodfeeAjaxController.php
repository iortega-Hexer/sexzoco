<?php
/**
* Cash On Delivery With Fee
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitutes a violation of the user agreement.
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
*  @copyright 2020 idnovate
*  @license   See above
*/

class AdminCodfeeAjaxController extends ModuleAdminController
{
    public function __construct()
    {
        $this->module_name = 'codfee';
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        if (Tools::isSubmit('entity')) {
            if (Tools::getValue('entity') === 'products') {
                die(json_encode(Codfee::getProductsLite($this->context->language->id, true, true)));
            } elseif (Tools::getValue('entity') === 'manufacturers') {
                die(json_encode(Manufacturer::getManufacturers(false, $this->context->language->id, false)));
            } elseif (Tools::getValue('entity') === 'suppliers') {
                die(json_encode(Supplier::getSuppliers(false, $this->context->language->id, false)));
            } elseif (Tools::getValue('entity') === 'customers') {
                die(json_encode(Customer::getCustomers(true)));
            }
        } else {
            throw new PrestaShopException('Entity is not defined.');
        }
    }
}
