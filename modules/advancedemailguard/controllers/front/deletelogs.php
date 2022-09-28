<?php
/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2022 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

class AdvancedemailguardDeletelogsModuleFrontController extends ModuleFrontController
{
    /**
     * Module instance.
     *
     * @var \Advancedemailguard
     */
    public $module;

    /**
     * Create new module front controller.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->module->isDemo()) {
            die;
        }

        if (Tools::getValue('token') !== $this->module->getLogsToken()) {
            http_response_code(401);
            die;
        }

        $days = (int) Tools::getValue('days');
        if (!$this->module->deleteLogs($days)) {
            http_response_code(500);
        }
        die;
    }
}
